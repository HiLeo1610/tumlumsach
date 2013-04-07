<?php

class Book_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$categoryTable = new Book_Model_DbTable_Categories();
		$select = $categoryTable->select()->order('ordering');
		$this->view->categories = $categories = $categoryTable->fetchAll($select);
	}
}
