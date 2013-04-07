<?php

class Book_Widget_ProfilePostController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->postTags = $subject->tags()->getTagMaps();
		
	}
}