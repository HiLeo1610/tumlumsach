<?php
class Book_WorkController extends Book_Controller_Base
{
	protected function _getSubjectType()
	{
		return 'book_work';
	}

	protected function _getAddFavoriteMsg() {
		return "Add this work to your favorite works successfully.";
	}
	
	protected function _getRemoveFavoriteMsg() {
		return "Remove this work from your favorite works successfully.";
	}
	
	public function indexAction()
	{
		$this->_helper->content->setEnabled();
		
		$params = $this->_getAllParams();
		$workTbl = new Book_Model_DbTable_Works();
		$workTblName = $workTbl->info(Zend_Db_Table_Abstract::NAME);
		$workSelect = $workTbl->getSelect();
		$workSelect->order('creation_date DESC');

		if (!empty($params['tag']))
		{
			$tagMapTbl = Engine_Api::_()->getDbtable('TagMaps', 'core');
			$tagMapTblName = $tagMapTbl->info('name');

			$workSelect->joinLeft($tagMapTblName, "$tagMapTblName.resource_id = $workSelect.work_id", NULL)
				->where($tagMapTblName . '.resource_type = ?', 'book_work')
				->where($tagMapTblName . '.tag_id = ?', $params['tag']);
		}

		if (!empty($params['text']))
		{
			$workSelect->where("$workTblName.title LIKE ?", "%{$params['text']}%");
		}
		if (!empty($params['author']))
		{
			$userTbl = Engine_Api::_()->getItemTable('user');
			$userTblName = $userTbl->info(Zend_Db_Table_Abstract::NAME);
			$workSelect->join($userTblName, "$workTblName.user_id = $userTblName.user_id", array());
			$workSelect->where("$userTblName.displayname LIKE ?", "%{$params['author']}%");
		}
		
		$this->view->viewer = Engine_Api::_()->user()->getViewer();
		$this->view->paginator = $paginator = Zend_Paginator::factory($workSelect);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$paginator->setItemCountPerPage(10);
	}

	protected function _initActions()
	{
		$workId = $this->_getParam('id', 0);
		if ($workId)
		{
			$work = Engine_Api::_()->book()->getObject('book_work', $workId);
			if ($work && $work instanceof Book_Model_Work)
			{
				$viewer = Engine_Api::_()->user()->getViewer();
				if ($work->published || (!$work->published && $work->user_id == $viewer->getIdentity()) || $viewer->isAdmin()) {
					Engine_Api::_()->core()->setSubject($work);
				}
			}
		}
	}

	public function viewAction()
	{
		parent::viewAction();
		
		$this->view->work = $work = $this->_getSubject();
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		
		$chapterTbl = new Book_Model_DbTable_Chapters();
		$chapterSelect = $chapterTbl->select()->where('work_id = ?', $work->getIdentity());
		$workOwner = $work->getOwner();
		if (!$workOwner->isSelf($viewer)) {
			$chapterSelect->where('published = ?', 1);
		}
		$this->view->paginator = $paginator = Zend_Paginator::factory($chapterSelect);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		
		$this->_helper->content->setEnabled();
	}
	
	public function deleteAction()
	{
		$this->_initActions();
		$chapter = $this->_getSubject();
		
		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');

		$this->view->form = $form = new Book_Form_Work_Delete();
		
		if (!$this->getRequest()->isPost())
		{
			return;
		}
		
		$work = $chapter->getParent();

		$chapter->delete();

		return $this->_forward('success', 'utility', 'core', array(
			'layout' => 'default-simple',
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('The work is deleted successfully.')),
			'parentRedirect' => $work->getHref()
		));
	}

	// public function rateAction()
	// {
		// if (!$this->_helper->requireUser()->isValid())
		// {
			// return;
		// }
		// $book_id = (int)$this->_getParam('id');
		// if ($book_id)
		// {
			// $book = Engine_Api::_()->getItem('book', $book_id);
			// if ($book)
			// {
				// Engine_Api::_()->core()->setSubject($book);
			// }
		// }
		// if (!$this->_helper->requireSubject('book')->isValid())
		// {
			// return;
		// }
// 
		// if (!$this->_helper->requireAuth()->setAuthParams('book', null, 'view')->isValid())
		// {
			// return;
		// }
// 
		// $viewer = Engine_Api::_()->user()->getViewer();
// 
		// $rating = (int)$this->_getParam('rating');
		// $book->setRating($viewer->getIdentity(), $rating);
		// $total = $book->getRatingCount();
// 		
		// // add popularity for the book
		// if (!empty($book)) {
			// $popularity = Engine_Api::_()->getItem('book_popularity', $book->getIdentity());
			// $previousRating = $popularity->rating_point; 
			// $popularity->rating_count = $popularity->rating_count + 1;
			// $popularity->rating_point = $rating;
			// $popularity->point = $popularity->point + Book_Plugin_Core::RATING_COUNT_POINT + ($rating - $previousRating) * Book_Plugin_Core::RATING_POINT;
			// $popularity->save();	
		// }
// 
		// return $this->_helper->json(array(
			// 'total' => $total,
			// 'rating' => $rating
		// ));
	// }

	public function suggestAction()
	{
		$text = $this->_getParam('value');

		if (!empty($text))
		{
			$parent_id = $this->_getParam('parent_id', 0);
			$bookTable = new Book_Model_DbTable_Books();
			$select = $bookTable->select()->where('book_name LIKE ?', "%$text%");
			if ($parent_id != 0)
			{
				$select->where('book_id != ?', $parent_id);
			}

			$data = array();
			foreach ($bookTable->fetchAll($select) as $book)
			{
				$record = array(
					'id' => $book->getIdentity(),
					'label' => $book->book_name,
					'photo' => $this->view->itemPhoto($book, 'thumb.icon'),
					'url' => $book->getHref(),
					'type' => $book->getType(),
					'id' => $book->getIdentity(),
					'guid' => $book->getGuid(),
				);
				array_push($data, $record);
			}
			return $this->_helper->json($data);
		}
	}

	public function createAction()
	{
		if (!$this->_helper->requireUser()->isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();

		$this->view->form = $form = new Book_Form_Work( array('workTitle' => 'Post your work'));

		if (!$this->getRequest()->isPost())
		{
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost()))
		{
			return;
		}

		$values = $form->getValues();

		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();

		try
		{
			$workTable = new Book_Model_DbTable_Works();
			$work = $workTable->createRow($values);
			$work->creation_date = date('Y-m-d H:i:s');
			$work->modified_date = $work->creation_date;
			$work->user_id = $viewer->getIdentity();
			$work->save();

			if (!empty($values['photo']))
			{
				try
				{
					$work = $work->setPhoto($form->photo);
					$photoTable = new Book_Model_DbTable_Photos;
					$photo = $photoTable->createRow(array(
						'parent_object_id' => $work->getIdentity(),
						'parent_object_type' => $work->getType(),
						'file_id' => $work->photo_id,
						'user_id' => $viewer->getIdentity(),
						'approved' => 1,
						'default' => 1
					));
					$photo->save();
				}
				catch (Engine_Image_Adapter_Exception $e)
				{
					Zend_Registry::get('Zend_Log')->log($e->__toString(), Zend_Log::WARN);
				}
			}

			// CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
          	$roles = array('owner', 'parent_member', 'registered', 'everyone');
            foreach ($roles as $i => $role) {
                $auth->setAllowed($work, $role, 'view', true);
            }

			$db->commit();
		}
		catch (Exception $e)
		{
			$db->rollBack();
			throw $e;
		}

		if ($work->published) {
			$actionTbl = Engine_Api::_()->getDbTable('actions', 'activity');
			$action = $actionTbl->addActivity($viewer, $work, 'work_new');
		}

		$this->_redirectCustom($work->getHref());
	}

	public function editAction()
	{
		$this->_initActions();
		$subject = $this->_getSubject();
		$this->_checkSubject();
		$this->_checkAuthorization('view');

		$this->view->form = $form = new Book_Form_Work( array('workTitle' => 'Edit the work'));
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		if (!$this->getRequest()->isPost())
		{
			$form->populate($subject->toArray());
			return;
		}

		if ($form->isValid($this->getRequest()->getPost()))
		{
			$values = $form->getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			try
			{
				$subject->setFromArray($values);
				$subject->save();
				
				if (!empty($values['photo']))
				{
					$subject->setPhoto($form->photo);
				}	

				$db->commit();
			}
			catch(Exception $e)
			{
				$db->rollBack();
				throw $e;
			}

			if ($subject->published) {
				$actionTbl = Engine_Api::_()->getDbTable('actions', 'activity');
				$actions = $actionTbl->getActionsByObjectAndType($subject, 'work_new');
				if (empty($actions)) {
					$action = $actionTbl->addActivity($viewer, $work, 'work_new');
				}	
			}
			
			$this->_redirectCustom($subject);
		}
	}
}
