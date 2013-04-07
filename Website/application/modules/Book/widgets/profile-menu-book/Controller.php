<?php

class Book_Widget_ProfileMenuBookController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Get menu items
    	$this->view->menu = $menu = Engine_Api::_()->getApi('menus', 'core')
      		->getNavigation('book_profile_menu');
	}
}