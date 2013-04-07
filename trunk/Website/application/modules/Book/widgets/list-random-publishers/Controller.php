<?php

class Book_Widget_ListRandomPublishersController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$userTable = Engine_Api::_()->getItemTable('user');
		$userTableName = $userTable->info('name');
		$bookTable = new Book_Model_DbTable_Books();
		$bookTableName = $bookTable->info('name');
		 
		$userSelect = $userTable->select()->from($userTableName);
		$userSelect->setIntegrityCheck(false);
		$userSelect->join($bookTableName, 
			"$bookTableName.publisher_id = $userTableName.user_id",
			array("COUNT(*) AS num_books"));
		$userSelect->group(array("$userTableName.user_id"));
		
		$userSelect->where("$userTableName.level_id IN (?)", 
			array(Book_Plugin_Constants::PUBLISHER_LEVEL, Book_Plugin_Constants::BOOK_COMPANY_LEVEL));
		$userSelect->where("$userTableName.enabled = ?", 1);
		$userSelect->where("$userTableName.verified = ?", 1);
		$userSelect->where("$userTableName.approved = ?", 1);
		
		$numberOfPublishers = $this->_getParam('itemCountPerPage', 5);
		$userSelect->order('RAND()');
		$userSelect->limit($numberOfPublishers);
		
		$this->view->authors = $users = $userTable->fetchAll($userSelect);
	}
}