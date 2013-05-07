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
		if (Engine_Api::_()->book()->importRawPosts()) {
			return $this->_forward('success', 'utility', 'core', array(
					'layout' => 'default-simple',
					'parentRefresh' => true,
					'messages' => array(Zend_Registry::get('Zend_Translate')->_('The data is imported successfully.'))
			));
		}
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
		if (Engine_Api::_()->book()->importRawBooks()) {	
			return $this->_forward('success', 'utility', 'core', array(
	            'layout' => 'default-simple',
	            'parentRefresh' => true,
	        	'messages' => array(Zend_Registry::get('Zend_Translate')->_('The data is imported successfully.'))
			)); 
		}
	}
}