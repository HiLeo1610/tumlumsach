<?php

class Book_Widget_ManageBookPhotosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->book = $subject = Engine_Api::_()->core()->getSubject();
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		
		if ($viewer->isAdmin() || $subject->isBookAuthor($viewer) || $subject->user_id == $viewer->getIdentity()) {
			$photoTbl = new Book_Model_DbTable_Photos;
			$select = $photoTbl->select()->where('parent_object_type = ?', $subject->getType())
				->where('parent_object_id = ?', $subject->getIdentity());
			
			$this->view->photos = $photos = $photoTbl->fetchAll($select);
			
			return;
		} 
		
		return $this->setNoRender();
	}
}