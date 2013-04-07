<?php

class Book_Widget_ListRandomWorksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if ($request->isPost()) {
			$this->getElement()->removeDecorator('title');	
		}
		
		$numberOfWorks = $this->_getParam('itemCountPerPage');
		
		$workTable = new Book_Model_DbTable_Works();
		$workTableName = $workTable->info('name'); 
		$workSelect = $workTable->getSelect();
		$workSelect->limit($numberOfWorks);
		$workSelect->order('RAND()');
		
		$this->view->works = $workTable->fetchAll($workSelect);
	}
}