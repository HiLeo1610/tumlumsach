<?php

class Book_Widget_SearchBooksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$formBookSearch = new Book_Form_Book_Search();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$params = $request->getParams();
		if (!empty($params)) {
			$formBookSearch->populate($params);
		}
		$this->view->formBookSearch = $formBookSearch;
	}
}
