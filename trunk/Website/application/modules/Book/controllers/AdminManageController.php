<?php
class Book_AdminManageController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('book_admin_main', array(), 'book_admin_main_manage');

		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			if (isset($values['action']) && !empty($values['action'])) {
				return $this->_forward($values['action'], 'admin-manage', 'book');				
			}
			foreach ($values as $key => $value)
			{
				if ($key == 'delete_' . $value)
				{
					$video = Engine_Api::_() -> getItem('video', $value);
					$video -> delete();
				}
			}
		}

		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> book() -> getBooksPaginator(array('orderby' => 'book_id', ));

		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function deletesAction() {
		// In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
		if ($this->getRequest()->isPost()) {
			$ids = $this->_getParam('id');
			$bookTbl = new Book_Model_DbTable_Books();
			
			if (!empty($ids) && is_array($ids)) {
				$bookSelect = $bookTbl->getSelect();
				$bookSelect->where('book_id IN (?)', $ids);
				$books = $bookTbl->fetchAll($bookSelect);
				
				$db = Engine_Db_Table::getDefaultAdapter();
	            $db->beginTransaction();
	
	            try {
	                foreach ($books as $book) {
						$book->delete();
					}
	                $db->commit();
	            } catch (Exception $e) {
	                $db->rollBack();
	                throw $e;
	            }
	
	            return $this->_forward('success', 'utility', 'core', array(
	                'layout' => 'default-simple',
	                'parentRefresh' => true,
	                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The books are deleted successfully.'))
	            ));
			}
		}
	}
	
	public function deleteAction()
	{
		// In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->book_id = $id;
        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $book = Engine_Api::_()->getItem('book', $id);
				$book->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The book is deleted successfully.'))
            ));
        }
	}
	
	public function importAction() {
		$bookTbl = new Book_Model_DbTable_Books();
		$select = $bookTbl->select();
		$select->from($bookTbl->info('name'), new Zend_Db_Expr('MAX(`rawbook_id`) as max_rawbook_id'));
		$data = $select->query()->fetch();
		$maxRawbookId = (int)$data['max_rawbook_id'];
		
		$userTbl = new User_Model_DbTable_Users();		
		
		$rawBookTbl = new Book_Model_DbTable_Rawbooks();
		$rawBookSelect = $rawBookTbl->select();
		$rawBookSelect->where('rawbook_id > ?', $maxRawbookId);
		foreach ($rawBookTbl->fetchAll($rawBookSelect) as $rawBook) {
			if (!empty($rawBook['publisher'])) {
				$publisherSelect = $userTbl->select()->where('displayname LIKE ?', $rawBook['publisher']);
				$publisher = $userTbl->fetchRow($publisherSelect);
			}
			
			if (!empty($rawBook['book_company'])) {
				$bookCompanySelect = $userTbl->select()->where('displayname LIKE ?', $rawBook['book_company']);
				$bookCompany = $userTbl->fetchRow($bookCompanySelect);
			}

			$data = array(
				'book_name' => $rawBook->book_name,
				'published_date' => date('Y-m-d H:i:s', $rawBook->published_date),
				'price' => $rawBook->price,
				'num_page' => $rawBook->num_page,
				'description' => $rawBook->description,		
				'rawbook_id' => $rawBook->getIdentity(),
				'creation_date' => date('Y-m-d H:i:s', $rawBook->published_date),	
				'user_id' => 1 //superadmin		
			);
			
			if (isset($publisher) && !empty($publisher)) {
				$data['publisher_id'] = $publisher->getIdentity();
			}
			
			if (isset($bookCompany) && !empty($bookCompany)) {
				$data['book_company_id'] = $bookCompany->getIdentity();
			}
			
			$book = $bookTbl->createRow($data);
			$book->save();
			
			if (!empty($rawBook['photo'])) {
				$image = Engine_Image::factory();
				
				$name = basename($rawBook['photo']);
				$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
				$params = array(
		            'parent_id' => $book->getIdentity(),
		            'parent_type' => $book->getType()
        		);

		        // Save
		        $storage = Engine_Api::_()->storage();
				
        		$image->open($rawBook['photo'])
                	->write($path . '/m_' . $name)
                	->destroy();

		        // Store
		        $iMain = $storage->create($path . '/m_' . $name, $params);

		        // Remove temp files
		        @unlink($path . '/m_' . $name);

       			$book->photo_id = $iMain->getIdentity();
				$book->save();
				
				$photoTbl = new Book_Model_DbTable_Photos();
				$photo = $photoTbl->createRow(array(
					'parent_object_type' => $book->getType(),
					'parent_object_id' => $book->getIdentity(),
					'file_id' => $iMain->getIdentity(),
					'user_id' => 1, // superadmin
					'approved' => 1,
					'default' => 1 
				));
				$photo->save();
			}	
		} 	

		return $this->_forward('success', 'utility', 'core', array(
            'layout' => 'default-simple',
            'parentRefresh' => true,
        	'messages' => array(Zend_Registry::get('Zend_Translate')->_('The data is imported successfully.'))
		)); 
	}
}