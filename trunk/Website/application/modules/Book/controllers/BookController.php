<?php
class Book_BookController extends Book_Controller_Base
{
	protected function _getSubjectType()
	{
		return 'book';
	}

	public function indexAction()
	{
		$this->_helper->content->setEnabled()->setNoRender();
	}

	protected function _initActions()
	{
		$bookId = $this->_getParam('id', 0);
		if ($bookId)
		{
			//$book = Engine_Api::_()->getItem('book', $book_id);
			$book = Engine_Api::_()->book()->getObject('book', $bookId);
			if ($book && $book instanceof Book_Model_Book)
			{

				Engine_Api::_()->core()->setSubject($book);
			}
		}
	}

	protected function _checkAuthorization($action = 'view')
	{
		parent::_checkAuthorization('view');

		if ($action != 'view')
		{
			$subject = $this->_getSubject();

			$viewer = Engine_Api::_()->user()->getViewer();
			if (!$subject->isBookAuthor($viewer) 
				&& !$subject->isPublisher($viewer) 
				&& !$subject->isBookCompany($viewer) 
				&& !$viewer->isAdmin()
				&& !$subject->getOwner()->isSelf($viewer))
			{
				return !$this->_helper->requireAuth()->forward();
			}
		}
	}

	public function editAction()
	{
		$this->_initActions();
		$subject = $this->_getSubject();
		$this->_checkSubject();
		$this->_checkAuthorization('edit');

		$this->view->form = $form = new Book_Form_Book( array('bookTitle' => 'Edit the book'));
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		if (!$this->getRequest()->isPost())
		{
			// TODO [DangTH] : check again the translator and authors
			$authors = $subject->getAuthors(Book_Plugin_Constants::AUTHOR);
			$this->view->toObjects = $toObjects = array();
			if ($subject->is_foreign) {
				$this->view->toTranslatros = $translators = $subject->getAuthors(Book_Plugin_Constants::TRANSLATOR);
			}
			
			$toValues = array();
			$authorNames = array();
			foreach ($authors as $author)
			{
				if ($author instanceof User_Model_User) {
					array_push($toValues, $author->getIdentity());
					array_push($toObjects, $author);
				} else {
					array_push($authorNames, $author);
				}
			}
			$arrData = $subject->toArray();
			if (!empty($toValues)) {
				$arrData = array_merge($arrData, array('toValues' => implode($toValues, ',')));
			}
			if (!empty($authorNames)) {
				$arrData = array_merge($arrData, array('authors' => implode($authorNames, ',')));
			}
			
			if ($subject->is_foreign) {
				$translators = $subject->getAuthors(Book_Plugin_Constants::TRANSLATOR, TRUE);
				$toTranslatorValues = array();
				$translatorNames = array();
				$this->view->toTranslators = $toTranslators = array();
				foreach ($translators as $translator)
				{
					if ($translator instanceof User_Model_User) {
						array_push($toTranslatorValues, $translator->getIdentity());
						array_push($toTranslators);
					} else {
						array_push($translatorNames, $translator);
					}
				}
				if (!empty($toTranslatorValues)) {
					$arrData = array_merge($arrData, array('toTranslatorValues' => implode($toTranslatorValues, ',')));
				}
				if (!empty($translatorNames)) {
					$arrData = array_merge($arrData, array('translators' => implode($translatorNames, ',')));
				}
				// TODO [DangTH] : check again, it is not convienient using this
				$arrData['is_foreign'] = '1';
			}
			
			$form->populate($arrData);
			$this->view->isPopulated = true;
			return;
		}
		$form->getElement('photo')->setRequired(false);
		if ($form->isValid($this->getRequest()->getPost()))
		{
			$bookAuthorTbl = new Book_Model_DbTable_BookAuthor;
			$notificationTbl = Engine_Api::_()->getDbtable('notifications', 'activity');
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			try
			{
				$values = $form->getValues();
				$subject->setFromArray($values);
				$subject->save();
				
				if (!empty($values['photo']))
				{
					try
					{
						$subject = $subject->setPhoto($form->photo);
						$photoTable = new Book_Model_DbTable_Photos;
						$photoTableName = $photoTable->info('name');
						$photoTable->update(
							array('file_id' => $subject->photo_id),
							array(
								"$photoTableName.default = ?" => '1',
								"$photoTableName.parent_object_id = ?" => $subject->getIdentity(),
								"$photoTableName.parent_object_type = ?" => $subject->getType(),
							)
						);
						
						// $photoTbl->update(array('default' => 0), array(
							// 'parent_object_id = ?' => $book->getIdentity(),
							// 'parent_object_type = ?' => $book->getType()
						// ));
					}
					catch (Engine_Image_Adapter_Exception $e)
					{
						Zend_Registry::get('Zend_Log')->log($e->__toString(), Zend_Log::WARN);
					}
				}
				
				$authorIds = explode(',', $values['toValues']);
				$authors = $subject->getAuthors();
				$listAuthorIds = array();
				foreach ($authors as $idx => $author)
				{
					if (!empty($author) && is_object($author)) {
						array_push($listAuthorIds, $author->getIdentity());
						if (!in_array($author->getIdentity(), $authorIds)) {
							$bookAuthorTbl->delete(array(
								'book_id = ?' => $subject->getIdentity(),
								'author_id = ?' => $author->getIdentity()
							));
							if (!$author->isSelf($viewer)) {
								$notificationTbl->addNotification($author, $viewer, $subject, 'book_remove_author_from_book');
							}
							unset($authors[$idx]);		
						}
					}
				}
				$select = $bookAuthorTbl->select()->where('book_id = ?', $subject->getIdentity());
				foreach ($authorIds as $authorId) {
					if (!empty($authorId)) {
						if (!in_array($authorId, $listAuthorIds)) {
							$row = $bookAuthorTbl->createRow(
								array('author_id' => $authorId, 'book_id' => $subject->getIdentity())
							);
							$row->save();
							$author = Engine_Api::_()->user()->getUser($authorId);
							$notificationTbl->addNotification($author, $viewer, $subject, 'book_add_author_to_book');
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
			
			$this->_redirectCustom($subject);
		}
	}

	public function viewAction()
	{
		parent::viewAction();

		$subject = $this->_getSubject();
		$popularity = Engine_Api::_()->getItemTable('book_popularity')->getObject($subject->getType(), $subject->getIdentity());
		$popularity->view_count = $popularity->view_count + 1;
		$popularity->point = $popularity->point + Book_Plugin_Core::VIEW_POINT;
		$popularity->save();
		
		$this->_helper->content->setNoRender()->setEnabled();
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

		$this->view->form = $form = new Book_Form_Book( array('bookTitle' => 'Post a book'));

		if (!$this->getRequest()->isPost())
		{
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost()))
		{
			return;
		}

		$values = $form->getValues();
		$bookTable = new Book_Model_DbTable_Books();
		$bookAuthorTable = new Book_Model_DbTable_BookAuthor();

		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();

		try
		{
			$book = $bookTable->createRow($values);
			$book->creation_date = date('Y-m-d H:i:s');
			$book->modified_date = $book->creation_date;
			$book->category_id = $this->_getParam('category_id_0', 0);
			$book->user_id = $viewer->getIdentity();
			$book->save();

			if (!empty($values['photo']))
			{
				try
				{
					$book = $book->setPhoto($form->photo);
					$photoTable = new Book_Model_DbTable_Photos;
					$photo = $photoTable->createRow(array(
						'parent_object_id' => $book->getIdentity(),
						'parent_object_type' => $book->getType(),
						'file_id' => $book->photo_id,
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

			$authorIds = explode(',', $values['toValues']);
			if (!empty($authorIds)) {
				foreach ($authorIds as $authorId)
				{
					if (!empty($authorId)) {
						$bookAuthor = $bookAuthorTable->createRow();
						$bookAuthor->book_id = $book->getIdentity();
						$bookAuthor->author_id = $authorId;
						$bookAuthor->save();
					}
				}
			}
			 
			$authorsName = explode(',', $values['authors']);
			foreach ($authorsName as $name) {
				if (!empty($name)) {
					$bookAuthor = $bookAuthorTable->createRow();
					$bookAuthor->book_id = $book->getIdentity();
					$bookAuthor->author_id = 0;
					$bookAuthor->author_name = $name;
					$bookAuthor->save();
				}
			}
			
			if ($book->is_foreign) {
				$translatorIds = explode(',', $values['toTranslatorsValues']);
				if (!empty($translatorIds)) {
					foreach ($translatorIds as $translatorId)
					{
						if (!empty($translatorId)) {
							$bookAuthor = $bookAuthorTable->createRow();
							$bookAuthor->book_id = $book->getIdentity();
							$bookAuthor->author_id = $translatorId;
							$bookAuthor->type = Book_Plugin_Constants::TRANSLATOR;
							$bookAuthor->save();
						}
					}
				} 
				
				$translatorsName = explode(',', $values['translators']);
				foreach ($translatorsName as $name) {
					if (!empty($name)) {
						$bookAuthor = $bookAuthorTable->createRow();
						$bookAuthor->book_id = $book->getIdentity();
						$bookAuthor->author_id = 0;
						$bookAuthor->author_name = $name;
						$bookAuthor->type = Book_Plugin_Constants::TRANSLATOR;
						$bookAuthor->save();
					}
				}
			}
			
			// CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
          	$roles = array('owner', 'parent_member', 'registered', 'everyone');
            foreach ($roles as $i => $role) {
                $auth->setAllowed($book, $role, 'view', true);
            }

			$db->commit();
		}
		catch (Exception $e)
		{
			$db->rollBack();
			throw $e;
		}

		$actionTbl = Engine_Api::_()->getDbTable('actions', 'activity');
		$action = $actionTbl->addActivity($viewer, $book, 'book_new');
		if ($action != null)
		{
			$actionTbl->attachActivity($action, $book);
		}

		$this->_redirectCustom($book->getHref());
	}

	public function deleteAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$bookId = $this->_getParam('id');
		if ($bookId)
		{
			$book = Engine_Api::_()->getItem('book', $bookId);
			if ($book && is_object($book))
			{
				Engine_Api::_()->core()->setSubject($book);
			}
		}

		if (!$this->_helper->requireSubject('book')->isValid())
		{
			return;
		}

		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');

		$this->view->form = $form = new Book_Form_Book_Delete();

		if (!$this->getRequest()->isPost())
		{
			return;
		}

		$book->delete();
		
		$url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'book_general', true);
		
		return $this->_forward('success', 'utility', 'core', array(
			'layout' => 'default-simple',
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('The book is deleted successfully.')),
			'parentRedirect' => $url
		));
	}

	private function _getBook()
	{
		$bookId = $this->_getParam('id');
		if ($bookId)
		{
			$bookTbl = new Book_Model_DbTable_Books;
			$book = $bookTbl->fetchRow($bookTbl->select()->where('book_id = ?', $bookId));

			return $book;
		}
	}

	public function setDefaultPhotoAction()
	{
		$book = $this->_getBook();
		if (isset($book) && is_object($book))
		{
			$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
			//$authors = $book->getAuthors(Book_Plugin_Constants::AUTHOR, TRUE);
			$concernedUsers = $book->getConcernedUsers();

			$allowSetDefault = false;
			if ($viewer->isAdmin() || $viewer->getIdentity() == $book->user_id)
			{
				$allowSetDefault = true;
			}
			else
			{
				foreach ($concernedUsers as $u)
				{
					if ($viewer->isSelf($u))
					{
						$allowSetDefault = true;
					}
				}
			}

			if ($allowSetDefault)
			{
				$photoId = $this->_getParam('photo_id');
				if ($photoId)
				{
					$db = Engine_Db_Table::getDefaultAdapter();
					$db->beginTransaction();
					try
					{
						$photoTbl = new Book_Model_DbTable_Photos;
						$photoTbl->update(array('default' => 0), array(
							'parent_object_id = ?' => $book->getIdentity(),
							'parent_object_type = ?' => $book->getType()
						));

						$select = $photoTbl->select()->where("photo_id = ?", $photoId);
						$photo = $photoTbl->fetchRow($select);
						$photo->default = 1;
						$photo->save();

						$book->photo_id = $photo->file_id;
						$book->save();

						$db->commit();
					}
					catch (Exception $e)
					{
						$db->rollBack();
						throw $e;
					}

					$this->view->status = 1;
					$this->view->message = Zend_Registry::get('Zend_Translate')->_('The photo is set as default successfully !');
				}
			}
		}
	}

	public function deletePhotoAction()
	{
		$book = $this->_getBook();
		if (isset($book) && is_object($book))
		{
			$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
			$authors = $book->getAuthors();

			$allowDelete = false;
			if ($viewer->isAdmin() || $viewer->getIdentity() != $book->user_id)
			{
				$allowDelete = true;
			}
			else
			{
				foreach ($authors as $author)
				{
					if ($viewer->isSelf($author))
					{
						$allowDelete = true;
					}
				}
			}

			if ($allowDelete)
			{
				$photoId = $this->_getParam('photo_id');
				if ($photoId)
				{
					$db = Engine_Db_Table::getDefaultAdapter();
					$db->beginTransaction();
					try
					{
						$photoTbl = new Book_Model_DbTable_Photos;
						$select = $photoTbl->select()->where("photo_id = ?", $photoId);
						$photo = $photoTbl->fetchRow($select);
						$photo->delete();

						$db->commit();
					}
					catch (Exception $e)
					{
						$db->rollBack();
						throw $e;
					}

					$this->view->status = 1;
					$this->view->message = Zend_Registry::get('Zend_Translate')->_('The photo is deleted successfully !');
				}
			}
		}
	}

	public function uploadPhotoAction()
	{
		$this->view->form = $form = new Book_Form_Book_UploadPhoto;

		if (!$this->getRequest()->isPost())
		{
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost()))
		{
			return;
		}

		$values = $form->getValues();

		if (!empty($values['photo']))
		{
			$book_id = $this->_getParam('id', 0);
			if ($book_id)
			{
				$book = Engine_Api::_()->getItem('book', $book_id);
				if ($book && $book instanceof Book_Model_Book)
				{
					Engine_Api::_()->core()->setSubject($book);
				}
			}

			$book = Engine_Api::_()->core()->getSubject();
			$viewer = Engine_Api::_()->user()->getViewer();

			$bookPhoto = $book->addPhoto($form->getElement('photo'), $viewer);
			if ($bookPhoto)
			{
				$activityTbl = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityTbl->addActivity($viewer, $book, 'book_photo_new');
				if ($action!=null) {
                    $activityTbl->attachActivity($action, $bookPhoto);
                }
				
				// Rebuild privacy
	            foreach ($activityTbl->getActionsByObject($book) as $action) {
	                $activityTbl->resetActivityBindings($action);
	            }
				$bookConcernedUsers = $book->getConcernedUsers();
				if (in_array($viewer, $bookConcernedUsers))
				{
					$notifyTbl = Engine_Api::_()->getDbtable('notifications', 'activity');
					foreach ($bookConcernedUsers as $notifyUser)
					{
						$notifyTbl->addNotification($notifyUser, $viewer, $bookPhoto, 'book_new_photo');

						return $this->_forward('success', 'utility', 'core', array(
							'messages' => array(Zend_Registry::get('Zend_Translate')->_('The photo is added and waiting the approval from the ts creator and its authors.')),
							'layout' => 'default-simple',
							'parentRefresh' => true
						));
					}
				}
				else
				{
					$bookPhoto->approved = 1;
					$bookPhoto->save();

					return $this->_forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate')->_('The photo is added successfully.')),
						'layout' => 'default-simple',
						'parentRefresh' => true
					));
				}
			}
			else
			{
				return $this->_forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('The photo is added unsuccessfully. Please try again !')),
					'layout' => 'default-simple',
					'parentRefresh' => true
				));
			}
		}
	}

}
