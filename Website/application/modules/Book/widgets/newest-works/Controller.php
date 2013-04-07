<?php

class Book_Widget_NewestWorksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$numberOfWorks = $this->_getParam('numberOfWorks', 5);
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if ($request->isPost()) {
			$this->getElement()->clearDecorators();
		}
		$workTable = new Book_Model_DbTable_Works();
		$workTableName = $workTable->info('name'); 
		$workSelect = $workTable->getSelect()->order("$workTableName.creation_date DESC");
		$workSelect->limit($numberOfWorks);
		$this->view->works = $works = $workTable->fetchAll($workSelect);
		$this->view->thumbnailOnly = $this->_getParam('thumbnailOnly', 0);
	}
}