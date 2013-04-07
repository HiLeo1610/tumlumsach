<?php

class Book_Widget_ListRandomBooksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$numberOfBooks = $this->_getParam('itemCountPerPage', 5);
		
		$bookTable = new Book_Model_DbTable_Books();
		$bookSelect = $bookTable->getSelect();
		$bookSelect->limit($numberOfBooks);
		$bookSelect->order('RAND()');
		
		$this->view->books = $books = $bookTable->fetchAll($bookSelect); 
	}
}