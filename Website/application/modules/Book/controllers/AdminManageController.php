<?php
class Book_AdminManageController extends Core_Controller_Action_Admin
{
	public function postsAction() 
	{
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			if (isset($values['action']) && !empty($values['action'])) {
				return $this->_forward($values['action'], 'admin-manage', 'book');
			}
		}
		
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('book_admin_main', array(), 'book_admin_main_manage-posts');
		
		$postTable = new Book_Model_DbTable_Posts();
		$postTableName = $postTable->info(Zend_Db_Table_Abstract::NAME);
		
		$postSelect = $postTable->getSelect();
		$postSelect->order("$postTableName.post_id DESC");
		$this->view->paginator = $paginator = Zend_Paginator::factory($postSelect);
		$page = $this -> _getParam('page', 1);
		$paginator->setItemCountPerPage(25);
		$paginator->setCurrentPageNumber($page);
	}
	
	public function deletePostsAction() {
		// In smoothbox
		$this->_helper->layout->setLayout('admin-simple');
		if ($this->getRequest()->isPost()) {
			$ids = $this->_getParam('id');
			$postTbl = new Book_Model_DbTable_Posts();
				
			if (!empty($ids) && is_array($ids)) {
				$postSelect = $postTbl->select();
				$postSelect->where('post_id IN (?)', $ids);
				$posts = $postTbl->fetchAll($postSelect);
	
				$db = Engine_Db_Table::getDefaultAdapter();
				$db->beginTransaction();
	
				try {
					foreach ($posts as $post) {
						$post->delete();
					}
					$db->commit();
				} catch (Exception $e) {
					$db->rollBack();
					throw $e;
				}
	
				return $this->_forward('success', 'utility', 'core', array(
						'layout' => 'default-simple',
						'parentRefresh' => true,
						'messages' => array(Zend_Registry::get('Zend_Translate')->_('The posts are deleted successfully.'))
				));
			}
		}
	}
	
	public function importPostsAction() {
		$postTbl = new Book_Model_DbTable_Posts();
		$select = $postTbl->select();
		$select->from($postTbl->info('name'), new Zend_Db_Expr('MAX(`rawpost_id`) as max_rawpost_id'));
		$data = $select->query()->fetch();
		$maxRawpostId = (int)$data['max_rawpost_id'];
	
		$userTbl = new User_Model_DbTable_Users();
		$rawbookTbl = new Book_Model_DbTable_Rawbooks();
	
		$rawPostTbl = new Book_Model_DbTable_Rawposts();
		$rawPostSelect = $rawPostTbl->select();
		$rawPostSelect->where('rawpost_id > ?', $maxRawpostId);
		foreach ($rawPostTbl->fetchAll($rawPostSelect) as $rawPost) {
			$data = array(
				'post_name' => $rawPost->name,
				'content' => $rawPost->content,
				'user_id' => 1, //superadmin
				'rawpost_id' => $rawPost->rawpost_id
			);
			if (!empty($rawPost->book_link_id)) {
				$book = $rawbookTbl->getBookFromBookLinkId($rawPost->book_link_id);
				if (!empty($book)) {
					$data['parent_type'] = $book->getType();
					$data['parent_id'] = $book->getIdentity();
				}	
			}
				
			$post = $postTbl->createRow($data);
			$post->save();
		}
	
		return $this->_forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_('The data is imported successfully.'))
		));
	}
	
	public function indexAction()
	{
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			if (isset($values['action']) && !empty($values['action'])) {
				return $this->_forward($values['action'], 'admin-manage', 'book');				
			}
		}

		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('book_admin_main', array(), 'book_admin_main_manage');
		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> book() -> getBooksPaginator(array('orderby' => 'book_id'));

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