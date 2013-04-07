<?php

class Book_Widget_HighestRatingBooksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$bookTable = new Book_Model_DbTable_Books();
		$signatureTable = new Book_Model_DbTable_Signatures();
		$signatureTableName = $signatureTable->info('name'); 
		$bookSelect = $bookTable->getSelect()->order("$signatureTableName.rating_count DESC");
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($bookSelect);
	 	if ($paginator->getTotalItemCount() == 0) {
            return $this->setNoRender();
        }
		$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	}
}