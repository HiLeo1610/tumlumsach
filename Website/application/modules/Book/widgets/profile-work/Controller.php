<?php

class Book_Widget_ProfileWorkController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$subject = Engine_Api::_()->core()->getSubject();
		if ($subject->getType() != 'book_work') {
			$subject = $subject->getParent();
		} 
		
		if ($subject->getType() == 'book_work') {
			$this->view->subject = $subject;
		} else {
			return $this->setNoRender();
		}
	}
}
