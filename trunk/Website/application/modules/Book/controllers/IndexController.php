<?php

class Book_IndexController extends Core_Controller_Action_Standard
{
	public function fixBookAction() {
		echo 'fix books' . PHP_EOL;
		
		$bookTbl = new Book_Model_DbTable_Books();
		$bookSelect = $bookTbl->select()->where('rawbook_id > 0');
		$bookSelect->limit(200);
		$rawBookIds = array();
		
		$books = $bookTbl->fetchAll($bookSelect);
		
		echo 'number of books : ' . count($books) . PHP_EOL;
		
		foreach ($books as $book) {
			array_push($rawBookIds, $book->rawbook_id);
		}
		
		$rawBookTbl = new Book_Model_DbTable_Rawbooks();
		$rawBookTbl->delete(array('rawbook_id IN (?)' => $rawBookIds));
		
		foreach ($books as $book) {
			$book->delete();
		}
		
		echo 'The books have been deleted !' . PHP_EOL;		
		die;
	}
	
	public function fixPostAction() {
		echo 'fix posts' . PHP_EOL;
		
		$postTbl = new Book_Model_DbTable_Posts();
		$postSelect = $postTbl->select()->where('rawpost_id > 0');
		$postSelect->limit(200);
		$rawPostIds = array();
		
		$posts = $postTbl->fetchAll($postSelect);
		
		echo 'number of posts : ' . count($posts) . PHP_EOL;
		
		foreach ($posts as $post) {
			array_push($rawPostIds, $post->rawpost_id);
		}
		
		$rawPostTbl = new Book_Model_DbTable_Rawposts();
		$rawPostTbl->delete(array('rawpost_id IN (?)' => $rawPostIds));
		
		foreach ($posts as $post) {
			$post->delete();
		}
		
		echo 'The posts have been deleted !' . PHP_EOL;
		die;
	}
	
	public function uploadAction() {
		$this->_helper->layout->disableLayout();
		
        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }
		
        $destination = "public/book/";
        if (!is_dir($destination)) {
            mkdir($destination);
        }
        $upload = new Zend_File_Transfer_Adapter_Http();
        $upload->setDestination($destination);
        $fullFilePath = $destination . time() . '_' . $upload->getFileName('Filedata', false);
        
        $image = Engine_Image::factory();
        $image->open($_FILES['Filedata']['tmp_name'])
                ->resize(720, 720)
                ->write($fullFilePath);
        
        $this->view->status = true;
        $this->view->name = $_FILES['Filedata']['name'];
        $this->view->photo_url = Zend_Registry::get('StaticBaseUrl') . $fullFilePath;
        $this->view->photo_width = $image->getWidth();
        $this->view->photo_height = $image->getHeight();
    }
	
	public function indexAction()
	{
		$this->_helper->content->setEnabled();
	}

	public function createBookAction()
	{
		if (!$this->_helper->requireUser()->isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();

		$this->view->form = $form = new Book_Form_Book_Create();

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
					$book->setPhoto($form->photo);

					$bookPhotoTable = new Book_Model_DbTable_Photos();
					$bookPhoto = $bookPhotoTable->createRow(array(
						'parent_type' => $book->getType(),
						'parent_id' => $book->getIdentity(),
						'file_id' => $book->photo_id,
						'approved' => 1,
						'default' => 1
					));
					$bookPhoto->save();
				}
				catch (Engine_Image_Adapter_Exception $e)
				{
					Zend_Registry::get('Zend_Log')->log($e->__toString(), Zend_Log::WARN);
				}
			}

			$authorIds = explode(',', $values['toValues']);

			foreach ($authorIds as $authorId)
			{
				$bookAuthor = $bookAuthorTable->createRow();
				$bookAuthor->book_id = $book->getIdentity();
				$bookAuthor->author_id = $authorId;
				$bookAuthor->save();
			}

			$photoTbl = new Book_Model_DbTable_Photos;
			$photo = $photoTbl->createRow(array(
				'parent_object_type' => $book->getType(),
				'parent_object_id' => $book->getIdentity(),
				'file_id' => $book->photo_id,
				'user_id' => $viewer->getIdentity(),
				'default' => 1,
				'approved' => 1
			));
			$photo->save();

			$db->commit();
		}
		catch (Exception $e)
		{
			$db->rollBack();
			throw $e;
		}

		$this->_redirectCustom($book->getHref());
	}

	public function listAction()
	{
		$this->_helper->content->setNoRender()->setEnabled();
	}

	public function suggestAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity())
		{
			$data = null;
		}
		else
		{
			$data = array();
			$table = Engine_Api::_()->getItemTable('user');
			$select = $table->select();
			$select->where('level_id != ?', Book_Plugin_Constants::AUTHOR_LEVEL);
			$select->where('level_id != ?', Book_Plugin_Constants::PUBLISHER_LEVEL);
			$select->where('level_id != ?', Book_Plugin_Constants::BOOK_COMPANY_LEVEL);
			$select->where('level_id != ?', Book_Plugin_Constants::SELLER_LEVEL);
			$select->where('user_id != ?', $viewer->getIdentity());

			if (0 < ($limit = (int)$this->_getParam('limit', 10)))
			{
				$select->limit($limit);
			}

			if (null !== ($text = $this->_getParam('search', $this->_getParam('value'))))
			{
				$select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
			}

			$ids = array();
			foreach ($select->getTable()->fetchAll($select) as $user)
			{
				$data[] = array(
					'type' => $user->getType(),
					'id' => $user->getIdentity(),
					'guid' => $user->getGuid(),
					'label' => $user->getTitle(),
					'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
					'url' => $user->getHref(),
				);
			}
		}

		$this->_helper->viewRenderer->setNoRender(true);
		$data = Zend_Json::encode($data);
		$this->getResponse()->setBody($data);
	}
	
	public function importRawBooksAction() {		
		if (Engine_Api::_()->book()->importRawBooks()) {
			echo 'The books are imported successfully !';
			die;
		}
	}
}