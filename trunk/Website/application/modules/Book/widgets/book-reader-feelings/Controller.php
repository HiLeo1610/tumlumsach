<?php

class Book_Widget_BookReaderFeelingsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->book = $book = Engine_Api::_()->core()->getSubject();
		if ($book->getType() == 'book')
		{
			$postTable = new Book_Model_DbTable_Posts();
			$select = $postTable->select()->where('parent_type = ?', $book->getType())
				->where('parent_id = ?', $book->getIdentity());
			$select->order('post_id desc');

			$this->view->paginator = $paginator = Zend_Paginator::factory($select);
			$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
			$paginator->setCurrentPageNumber($this->_getParam('page', 1));

			$userIds = array();
			foreach ($paginator->getCurrentItems() as $post)
			{
				array_push($userIds, $post->user_id);
			}
			$this->view->users = Engine_Api::_()->user()->getUserMulti($userIds);
		}
		else
		{
			return $this->setNoRender();
		}
	}
}