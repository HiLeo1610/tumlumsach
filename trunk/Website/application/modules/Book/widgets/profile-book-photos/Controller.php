<?php

class Book_Widget_ProfileBookPhotosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->book = $book = Engine_Api::_()->core()->getSubject();
		$this->view->viewer = Engine_Api::_()->user()->getViewer();
	}
}
