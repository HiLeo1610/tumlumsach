<?php

class Book_Widget_BooksOfAuthorController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
	public function getChildCount()
	{
		return $this->_childCount;
	}
	
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject())
		{			
			return $this->setNoRender();
		}

		$subject = Engine_Api::_()->core()->getSubject();

		if ($subject->getType() == 'user' && $subject->level_id == Book_Plugin_Constants::AUTHOR_LEVEL)
		{
			$bookTable = new Book_Model_DbTable_Books();
			$bookTableName = $bookTable->info('name'); 
			$bookSelect = $bookTable->getSelect();
			$bookAuthorTable = new Book_Model_DbTable_BookAuthor();
			$bookAuthorTableName = $bookAuthorTable->info('name');

			$bookSelect->join($bookAuthorTableName, "$bookTableName.book_id = $bookAuthorTableName.book_id");
			$bookSelect->where("$bookAuthorTableName.author_id = ?", $subject->getIdentity());
			$bookSelect->order('RAND()');
			
			$this->view->paginator = $paginator = Zend_Paginator::factory($bookSelect);
			$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
			$paginator->setCurrentPageNumber($this->_getParam('page', 1));
			
			$itemCount = $paginator->getTotalItemCount();
			if ($itemCount == 0) {
				return $this->setNoRender();	
			} else {
				$this->_childCount = $paginator->getTotalItemCount();
				$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
				$paginator->setCurrentPageNumber($this->_getParam('page', 1));
			}
		}
		else
		{
			return $this->setNoRender();
		}

	}

}
