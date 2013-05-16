<?php
class Book_PostController extends Book_Controller_Base
{
	protected function _getSubjectType()
	{
		return 'book_post';
	}

	public function indexAction()
	{
		$this->_helper->content->setEnabled();

		$params = $this->_getAllParams();
		$postTable = new Book_Model_DbTable_Posts();
		$postTableName = $postTable->info(Zend_Db_Table_Abstract::NAME);
		$postSelect = $postTable->getSelect();
		$postSelect->order('creation_date DESC');

		if (!empty($params['tag']))
		{
			$tagMapTbl = Engine_Api::_()->getDbtable('TagMaps', 'core');
			$tagMapTblName = $tagMapTbl->info('name');

			$postSelect->joinLeft($tagMapTblName, "$tagMapTblName.resource_id = $postTableName.post_id", NULL)->where($tagMapTblName . '.resource_type = ?', 'book_post')->where($tagMapTblName . '.tag_id = ?', $params['tag']);
		}

		if (!empty($params['text']))
		{
			$postSelect->where("$postTableName.post_name LIKE ?", "%{$params['text']}%");
		}

		$this->view->paginator = $paginator = Zend_Paginator::factory($postSelect);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
	}

	public function createAction()
	{
		if (!$this->_helper->requireUser->isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();

		// Create form
		$this->view->form = $form = new Book_Form_Post(array('postName' => 'Write a post'));
		if (!$this->getRequest()->isPost())
		{
			$parent_id = $this->_getParam('parent_id');
			$parent_type = $this->_getParam('parent_type');
			if (!empty($parent_id) && !empty($parent_type)) {
				if ($parent_type == 'book') {
					$this->view->parentBook = Engine_Api::_()->getItem('book', $parent_id);
					$form->getElement('hasParent')->setChecked(true);
				}				
			} else {
				$form->getElement('hasParent')->setChecked(false);
			}
			return;
		}


		if ($form->isValid($this->getRequest()->getPost()))
		{
			$values = $form->getValues();
			if ($values['hasParent'] != '1') {
				unset($values['parentBookValue']);
			}
			if (isset($values['parentBookValue'])) {
				$parent_id = $values['parentBookValue'];
				$parent_type = 'book';
			}
			$postTable = new Book_Model_DbTable_Posts();
			$tagPostsTable = new Book_Model_DbTable_Tags();
			$notifyApi = Engine_Api::_()->getDbTable('notifications', 'activity');
			$activityApi = Engine_Api::_()->getDbTable('actions', 'activity');

			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			try
			{
				$post = $postTable->createRow(array(
					'content' => $values['content'],
					'post_name' => $values['post_name'],
					'parent_type' => $parent_type,
					'parent_id' => $parent_id,
					'creation_date' => date('Y-m-d H:i:s'),
					'modified_date' => date('Y-m-d H:i:s'),
					'user_id' => $viewer->getIdentity()
				));
				$post->save();

				$tags = array();
				foreach (preg_split('/[,]+/', $values['tags']) as $tag) {
					$t = trim($tag);
					if (!empty($t)) {
						array_push($tags, $t);
					}					
				} 
				$post->tags()->addTagMaps($viewer, $tags);

				$tagUsers = preg_split('/[,]+/', $values['toValues']);
				foreach ($tagUsers as $userId)
				{
					$tagUser = $tagPostsTable->createRow(array(
						'object_type' => 'user',
						'object_id' => $userId,
						'post_id' => $post->getIdentity()
					));
					$tagUser->save();
				}

				$tagBooks = preg_split('/[,]+/', $values['toBookValues']);
				foreach ($tagBooks as $bookId)
				{
					$tagBook = $tagPostsTable->createRow(array(
						'object_type' => 'book',
						'object_id' => $bookId,
						'post_id' => $post->getIdentity()
					));
					$tagBook->save();
				}

				// CREATE AUTH STUFF HERE
	            $auth = Engine_Api::_()->authorization()->context;
	          	$roles = array('owner', 'parent_member', 'registered', 'everyone');
	            foreach ($roles as $i => $role) {
	                $auth->setAllowed($post, $role, 'view', true);
	            }
			
				$db->commit();
			}
			catch(Exception $e)
			{
				$db->rollBack();
				throw $e;
			}

			// add activity
			if ($post->parent_type == 'book' && !empty($post->parent_id)) {
				$action = $activityApi->addActivity($viewer, $post, 'post_new_parent');
				$book = $post->getParent();
				$actionForBook = $activityApi->addActivity($viewer, $book, 'book_new_post');
			} else {
				$action = $activityApi->addActivity($viewer, $post, 'post_new');
			}
			if ($action)
			{
				$action->attach($post);
				if (isset($actionForBook)) {
					$actionForBook->attach($post);
				}
			}

			// add notification
			if (!empty($tagUsers))
			{
				$notifyUsers = Engine_Api::_()->user()->getUserMulti($tagUsers);
				foreach ($notifyUsers as $notifyUser)
				{
					$notifyApi->addNotification($notifyUser, $viewer, $post, 'post_tagged');
				}
			}

			// add popularity for the book
			if (isset($book) && !empty($book)) {
				$popularity = Engine_Api::_()->getItemTable('book_popularity')->getObject($book->getType(), $book->getIdentity());
				$popularity->post_count = $popularity->post_count + 1;
				$popularity->point = $popularity->point + Book_Plugin_Core::POST_POINT;
				$popularity->save();	
			}
			
			return $this->_redirectCustom($post);
		}
	}

	public function viewAction()
	{
		parent::viewAction();
		
		/*$subject = $this->_getSubject();
		$this->view->postTags = $subject->tags()->getTagMaps();*/
		$this->_helper->content->setEnabled()->setNoRender();
	}

	public function editAction()
	{
		$this->_initActions();
		$subject = $this->_getSubject();
		$this->_checkSubject();
		$this->_checkAuthorization('view');

		$this->view->form = $form = new Book_Form_Post( array('postName' => 'Edit the post'));
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		if (!$this->getRequest()->isPost())
		{
			$this->view->toTaggedUsers = $taggedUsers = $subject->getTaggedUsers();
			$toValues = array();
			foreach ($taggedUsers as $taggedUser)
			{
				array_push($toValues, $taggedUser->getIdentity());
			}

			$this->view->toTaggedBooks = $taggedBooks = $subject->getTaggedBooks();
			$toBookValues = array();
			foreach ($taggedBooks as $taggedBook)
			{
				array_push($toBookValues, $taggedBook->getIdentity());
			}
			
			// prepare tags
	        $postTags = $subject->tags()->getTagMaps();
	
	        $tagString = '';
	        foreach ($postTags as $tagmap) {
	            if ($tagString !== '')
	                $tagString .= ', ';
	            $tagString .= $tagmap->getTag()->getTitle();
	        }
	        
	        $data = array(
				'toValues' => implode($toValues, ','),
				'toBookValues' => implode($toBookValues, ','),
				'tags' => $tagString
			);
			
	        $parentBook = $subject->getParentObject();
	        if (!empty($parentBook)) {
	        	$this->view->parentBook = $parentBook;
	        	$data['parentBookValue'] = $parentBook->getIdentity();
	        	$form->getElement('hasParent')->setChecked(true);
	        } else {
	        	$form->getElement('hasParent')->setChecked(false);
	        }
	
			$form->populate(array_merge($subject->toArray(), $data));

			$this->view->isPopulated = true;

			return;
		} else {
			if ($form->isValid($this->getRequest()->getPost()))
			{
				$values = $form->getValues();
				
				$values['parent_id'] = null;
				$values['parent_type'] = null;
				if ($values['hasParent'] === '1') {
					if (!empty($values['parentBookValue'])) {
						$values['parent_id'] = $values['parentBookValue'];
						$values['parent_type'] = 'book';
					}
				}
	
				$newTaggedUsers = array();
				$bookAuthorTbl = new Book_Model_DbTable_BookAuthor;
				$notificationTbl = Engine_Api::_()->getDbtable('notifications', 'activity');
				$tagTbl = new Book_Model_DbTable_Tags;
				$db = Engine_Db_Table::getDefaultAdapter();
				$db->beginTransaction();
	
				try
				{
					$subject->setFromArray($values);
					$subject->save();
	
					$tags = array();
					foreach (preg_split('/[,]+/', $values['tags']) as $tag) {
						$t = trim($tag);
						if (!empty($t)) {
							array_push($tags, $t);
						}					
					} 
					$subject->tags()->setTagMaps($viewer, $tags);
	
					$bookIds = explode(',', $values['toBookValues']);
					$taggedBooks = $subject->getTaggedBooks();
					$taggedBookIds = array();
					if (empty($bookIds)) {
						$tagTbl->delete(array(
							'object_type = ?' => 'book',
							'post_id = ?' => $subject->getIdentity(),
						));
					} else {
						foreach ($taggedBooks as $taggedBook)
						{
							array_push($taggedBookIds, $taggedBook->getIdentity());
							if (!in_array($taggedBook->getIdentity(), $bookIds))
							{
								$tagTbl->delete(array(
									'object_type = ?' => 'book',
									'object_id = ?' => $taggedBook->getIdentity(),
									'post_id = ?' => $subject->getIdentity(),
								));
							}
						}
						
						foreach ($bookIds as $bookId)
						{
							if (!in_array($bookId, $taggedBookIds))
							{
								$newBook = Engine_Api::_()->getItem('book', $bookId);
								if ($newBook)
								{
									$newTaggedBook = $tagTbl->createRow(array(
										'post_id' => $subject->getIdentity(),
										'object_type' => 'book',
										'object_id' => $bookId
									));
									$newTaggedBook->save();
								}
							}
						}
					}
					
					$userIds = explode(',', $values['toValues']);
					$taggedUsers = $subject->getTaggedUsers();
					$taggedUserIds = array();
					if (empty($userIds)) {
						$tagTbl->delete(array(
							'object_type = ?' => 'user',
							'post_id = ?' => $subject->getIdentity(),
						));
					} else {
						foreach ($taggedUsers as $taggedUser)
						{
							array_push($taggedUserIds, $taggedUser->getIdentity());
							if (!in_array($taggedUser->getIdentity(), $userIds))
							{
								$tagTbl->delete(array(
									'object_type = ?' => 'user',
									'object_id = ?' => $taggedUser->getIdentity(),
									'post_id = ?' => $subject->getIdentity(),
								));
							}
						}
						
						foreach ($userIds as $userId)
						{
							if (!in_array($userId, $taggedUserIds))
							{
								$newUser = Engine_Api::_()->user()->getUser($userId);
								if ($newUser)
								{
									$newTaggedUser = $tagTbl->createRow(array(
										'post_id' => $subject->getIdentity(),
										'object_type' => 'user',
										'object_id' => $userId
									));
									$newTaggedUser->save();
									array_push($newTaggedUsers, $newUser);
								}
							}
						}
					}	
	
					$db->commit();
				}
				catch(Exception $e)
				{
					$db->rollBack();
					throw $e;
				}
	
				foreach ($newTaggedUsers as $newTaggedUser)
				{
					$notificationTbl->addNotification($newTaggedUser, $viewer, $subject, 'post_tagged');
				}
	
				$this->_redirectCustom($subject);
			}
		}
	}

	public function deleteAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$postId = $this->_getParam('id');
		if ($postId)
		{
			$post = Engine_Api::_()->getItem('book_post', $postId);
			if ($post && is_object($post))
			{
				Engine_Api::_()->core()->setSubject($post);
			}
		}

		if (!$this->_helper->requireSubject('book_post')->isValid())
		{
			return;
		}

		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');

		$this->view->form = $form = new Book_Form_Post_Delete();

		if (!$this->getRequest()->isPost())
		{
			return;
		}

		if ($post->parent_type == 'book')
		{
			$url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $post->parent_id), 'book', true);
		}
		else
		{
			$url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'post_general', true);
		}

		$post->delete();

		return $this->_forward('success', 'utility', 'core', array(
			'layout' => 'default-simple',
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('The post is deleted successfully.')),
			'parentRedirect' => $url
		));
	}

	// public function rateAction()
	// {
		// if (!$this->_helper->requireUser()->isValid())
		// {
			// return;
		// }
		// $post_id = (int)$this->_getParam('id');
		// if ($post_id)
		// {
			// $post = Engine_Api::_()->getItem('book_post', $post_id);
			// if ($post)
			// {
				// Engine_Api::_()->core()->setSubject($post);
			// }
		// }
		// if (!$this->_helper->requireSubject('book_post')->isValid())
		// {
			// return;
		// }
// 
		// if (!$this->_helper->requireAuth()->setAuthParams('book_post', null, 'view')->isValid())
		// {
			// return;
		// }
// 
		// $viewer = Engine_Api::_()->user()->getViewer();
		// $user_id = $viewer->getIdentity();
// 
		// $rating = (int)$this->_getParam('rating');
		// $post->setRating($user_id, $rating);
		// $total = $post->getRatingCount();
// 
		// $data = array();
		// $data[] = array(
			// 'total' => $total,
			// 'rating' => $rating,
		// );
// 
		// return $this->_helper->json($data);
	// }
}