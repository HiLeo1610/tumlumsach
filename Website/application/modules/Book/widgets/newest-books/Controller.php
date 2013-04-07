<?php

class Book_Widget_NewestBooksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if ($request->isPost()) {
			$this->getElement()->clearDecorators();
		}
		$bookTable = new Book_Model_DbTable_Books();
		$bookTableName = $bookTable->info('name'); 
		$bookSelect = $bookTable->getSelect()->order("$bookTableName.creation_date DESC");
		$this->view->paginator = $paginator = Zend_Paginator::factory($bookSelect);
	 	if ($paginator->getTotalItemCount() == 0) {
            return $this->setNoRender();
        }
		$itemCountPerPage = $this->_getParam('itemCountPerPage', 12);
		if (empty($itemCountPerPage)) {
			$itemCountPerPage = 12;
		}
		$this->view->paginator->setItemCountPerPage($itemCountPerPage);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$this->view->viewInfo = $this->_getParam('viewInfo');
	}
}