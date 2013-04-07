<?php
class Book_AdminSettingsController extends Core_Controller_Action_Admin {
	public function categoriesAction() {
		$this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
				'application/modules/Book/externals/scripts/collapsible.js');

		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('book_admin_main', array(), 'book_admin_main_categories');

		$table = new Book_Model_DbTable_Categories();
		$this->view->categories = $table->getCategoriesByLevel(null, array('category_name'));
	}
}