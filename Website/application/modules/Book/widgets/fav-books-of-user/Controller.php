<?php

class Book_Widget_FavBooksOfUserController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
	public function indexAction()
	{
		// Get subject and check auth
    	$subject = Engine_Api::_()->core()->getSubject();
		
		$bookTable = new Book_Model_DbTable_Books();
		$bookTableName = $bookTable->info(Zend_Db_Table_Abstract::NAME); 
		$favTable = new Book_Model_DbTable_Favorites();
		$favTableName = $favTable->info(Zend_Db_Table_Abstract::NAME); 
		 
		$bookSelect = $bookTable->getSelect();
		$bookSelect->join($favTableName, "$bookTableName.book_id = $favTableName.parent_object_id");
		$bookSelect->where("$favTableName.parent_object_type = ?", 'book');
		$bookSelect->where("$favTableName.user_id = ?", $subject->getIdentity());
		$this->view->paginator = $paginator = Zend_Paginator::factory($bookSelect);
	
		// Do not render if nothing to show
	    if( $paginator->getTotalItemCount() <= 0 ) {
	      	return $this->setNoRender();
	    } else {
	      	$this->_childCount = $paginator->getTotalItemCount();
			$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
			$paginator->setCurrentPageNumber($this->_getParam('page', 1));
	    }
	}

	public function getChildCount()
	{
		return $this->_childCount;
	}
}