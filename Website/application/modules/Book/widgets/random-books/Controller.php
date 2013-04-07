<?php

class Book_Widget_RandomBooksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if ($request->isPost()) {
			$this->getElement()->clearDecorators();
		}
		
		$numberOfBooks = $this->_getParam('itemCountPerPage', 12);
		
		$bookTable = new Book_Model_DbTable_Books();
		$bookSelect = $bookTable->getSelect();
		$bookSelect->limit($numberOfBooks);
		$bookSelect->order('RAND()');
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($bookSelect);
		if ($paginator->getTotalItemCount() == 0) {
        	return $this->setNoRender();
        }
		
		if ($paginator->getTotalItemCount() == 0) {
            return $this->setNoRender();
        } 
		$itemCountPerPage = $this->_getParam('itemCountPerPage', 12);
		if (empty($itemCountPerPage)) {
			$itemCountPerPage = 12;
		}
		$paginator->setItemCountPerPage($itemCountPerPage);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1)); 
	}
}