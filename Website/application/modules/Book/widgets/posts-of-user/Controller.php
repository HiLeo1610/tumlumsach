<?php

class Book_Widget_PostsOfUserController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject())
		{
			return $this->setNoRender();
		}

		$subject = Engine_Api::_()->core()->getSubject();

		if ($subject->getType() == 'book_post' || $subject->getType() == 'book')
		{
			$user = $subject->getOwner();
		}
		elseif ($subject->getType() == 'user')
		{
			$user = $subject;
		}

		if (!isset($user))
		{
			return $this->setNoRender();
		}
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if ($request->isPost()) {
			$this->getElement()->clearDecorators();
		}
		
		$postTable = new Book_Model_DbTable_Posts();
		$postTableName = $postTable->info(Zend_Db_Table_Abstract::NAME);
		$postSelect = $postTable->getSelect(); 
		$postSelect->where("$postTableName.user_id = ?", $user->getIdentity());
		$postSelect->order(new Zend_Db_Expr('rand()'));
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($postSelect);
		$itemCountPerPage = $this->_getParam('itemCountPerPage', 10);
		if (empty($itemCountPerPage)) {
			$itemCountPerPage = 10;	
		}
		$paginator->setItemCountPerPage($itemCountPerPage);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		
		// Do not render if nothing to show
	    if( $paginator->getTotalItemCount() <= 0 ) {
	      	return $this->setNoRender();
	    } else {
	      	$this->_childCount = $paginator->getTotalItemCount();
	    }	
	}

	public function getChildCount()
	{
		return $this->_childCount;
	}
}
