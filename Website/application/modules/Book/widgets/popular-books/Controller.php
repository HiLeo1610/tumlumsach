<?php

class Book_Widget_PopularBooksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if ($request->isPost()) {
			$this->getElement()->clearDecorators();
		}
		
		$bookTable = new Book_Model_DbTable_Books();
		$bookTableName = $bookTable->info(Zend_Db_Table_Abstract::NAME); 
		
		$bookSelect = $bookTable->getSelect();
		
		$popularityTbl = new Book_Model_DbTable_Popularities();
		$popularityTblName = $popularityTbl->info(Zend_Db_Table_Abstract::NAME);
		
		$bookSelect->joinLeft($popularityTblName, "$popularityTblName.resource_id = $bookTableName.book_id", array("$popularityTblName.point"));
		$bookSelect->where("$popularityTblName.resource_type = ?", 'book');
		$bookSelect->order("$popularityTblName.point DESC");
		$bookSelect->order("$popularityTblName.posted_date DESC");
		$this->view->paginator = $paginator = Zend_Paginator::factory($bookSelect);
		if ($paginator->getTotalItemCount() == 0) {
        	return $this->setNoRender();
        }
		
		$itemCountPerPage = $this->_getParam('itemCountPerPage', 12);
		if (empty($itemCountPerPage)) {
			$itemCountPerPage = 12;
		}
		$paginator->setItemCountPerPage($itemCountPerPage);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$this->view->viewInfo = $this->_getParam('viewInfo');
		
	}
}