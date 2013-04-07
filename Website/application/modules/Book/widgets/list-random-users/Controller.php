<?php

class Book_Widget_ListRandomUsersController extends Engine_Content_Widget_Abstract
{
	const DEFAULT_LIMIT = 10;
	
	public function indexAction()
	{
		$limit = $this->_getParam('limit', self::DEFAULT_LIMIT);
		$levels = $this->_getParam('levels');
		$userTbl = Engine_Api::_()->getItemTable('user');
		
		$select = $userTbl->select()->where('level_id IN (?)', $levels);
		$select->order('RAND()');
		$select->limit($limit);
		
		$this->view->users = $users = $userTbl->fetchAll($select); 
	}
}