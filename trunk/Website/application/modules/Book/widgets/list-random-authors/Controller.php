<?php

class Book_Widget_ListRandomAuthorsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$userTable = Engine_Api::_()->getItemTable('user');
		$userTableName = $userTable->info('name');
		$bookAuthorTable = new Book_Model_DbTable_BookAuthor();
		$bookAuthorTableName = $bookAuthorTable->info('name');
		 
		$userSelect = $userTable->select()->from($userTableName);
		$userSelect->setIntegrityCheck(false);
		$userSelect->join($bookAuthorTableName, 
			"$bookAuthorTableName.author_id = $userTableName.user_id",
			array("COUNT(*) AS num_books"));
		$userSelect->group(array("$userTableName.user_id"));
		
		$userSelect->where("$userTableName.level_id = ?", Book_Plugin_Constants::AUTHOR_LEVEL);
		$userSelect->where("$userTableName.enabled = ?", 1);
		$userSelect->where("$userTableName.verified = ?", 1);
		$userSelect->where("$userTableName.approved = ?", 1);
		
		$numberOfAuthors = $this->_getParam('itemCountPerPage', 5);
		$userSelect->order('RAND()');
		$userSelect->limit($numberOfAuthors);
		
		$this->view->authors = $users = $userTable->fetchAll($userSelect);
	}
}