<?php

class Widget_WelcomeController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$this->view->navigation = $navigation = Engine_Api::_()
			->getApi('menus', 'core')->getNavigation('core_main');
	}
}