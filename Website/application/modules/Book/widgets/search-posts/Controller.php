<?php

class Book_Widget_SearchPostsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$formPostSearch = new Book_Form_Post_Search();
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$params = $request->getParams();
		if (!empty($params)) {
			$formPostSearch->populate($params);
		}
		$this->view->formPostSearch = $formPostSearch; 
	}
}
