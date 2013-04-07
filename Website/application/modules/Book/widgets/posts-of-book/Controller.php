<?php

class Book_Widget_PostsOfBookController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$subject = Engine_Api::_()->core()->getSubject();
		if ($subject->getType() != 'book') {
			$book = $subject->getParentObject();
		} else {
			$book = $subject;
		}	
		if ($book && $book->getType() == 'book') {
			$postTable = new Book_Model_DbTable_Posts();
			$postSelect = $postTable->getSelect();
			$postSelect->where('parent_type = ?', 'book')
				->where('parent_id = ?', $book->getIdentity());
			$postSelect->order('RAND()');
			
			$this->view->paginator = $paginator = Zend_Paginator::factory($postSelect);
			$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
			$paginator->setCurrentPageNumber($this->_getParam('page', 1));
			
			if ($paginator->getTotalItemCount() == 0) {
				return $this->setNoRender();	
			}
		} else {
			return $this->setNoRender();
		}
	}	
}