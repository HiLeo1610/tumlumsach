<?php

class Book_Widget_MyWorksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Get subject and check auth
    	$subject = Engine_Api::_()->core()->getSubject();
		
		$workTable = new Book_Model_DbTable_Works();
		$workTableName = $workTable->info(Zend_Db_Table_Abstract::NAME); 
		$workSelect = $workTable->getSelect();
		$workSelect->where("$workTableName.user_id = ?", $subject->getIdentity());
		$workSelect->order("$workTableName.creation_date DESC");
		
		$this->view->works = $works = $workTable->fetchAll($workSelect);
		
		// Do not render if nothing to show
	    if( count($works) == 0 ) {
	      	return $this->setNoRender();
	    } 
	}
}