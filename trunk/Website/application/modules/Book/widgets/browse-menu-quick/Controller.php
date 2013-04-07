<?php

class Book_Widget_BrowseMenuQuickController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Get quick navigation
    	$this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      		->getNavigation('book_quick');
	}
}