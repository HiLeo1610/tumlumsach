<?php

class Book_Widget_ListBooksController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$catId = $request->getParam('category_id', NULL);
		$bookTable = new Book_Model_DbTable_Books();
		$select = $bookTable->getSelect();
		$params = $request->getParams();
		
		$page = $request->getParam('page', 1);
		if (!($catId == NULL || $catId == ''))
		{
			if (isset($params['text']) && !empty($params['text'])) {
				$bookTableName = $bookTable->info('name');
				$select->where("$bookTableName.book_name LIKE ?", "%{$params['text']}%");
			}
			$select->where('category_id = ?', $catId);
			$this->view->category = $category = Engine_Api::_()->getItem('book_category', $catId);
			
			$bookPaginator = Zend_Paginator::factory($select);
			$bookPaginator->setCurrentPageNumber($page);
			$bookPaginator->setItemCountPerPage(20);
			$this->view->bookPaginator = $bookPaginator; 
		}
		else
		{
			$bookCategoryTable = new Book_Model_DbTable_Categories();
			$bookCategoryTableName = $bookCategoryTable->info('name'); 
			$bookTable = new Book_Model_DbTable_Books();
			$bookTableName = $bookTable->info('name');
			 
			$categorySelect = $bookCategoryTable->select();
			$bookSelectExist = $bookTable->getSelect();
			if (isset($params['text']) && !empty($params['text'])) {
				$bookSelectExist->where("$bookTableName.book_name LIKE ?", "%{$params['text']}%");
			}
			$bookSelectExist->where("$bookTableName.category_id = $bookCategoryTableName.category_id");
			
			$categorySelect->where(
				new Zend_Db_Expr('exists (' . $bookSelectExist . ')'));
			$this->view->categoryPaginator = $categoryPaginator = Zend_Paginator::factory($categorySelect);
			$categoryPaginator->setCurrentPageNumber($page);
			
			$booksByCategory = array();
			foreach ($categoryPaginator->getCurrentItems() as $cat) {
				$booksByCategory[$cat->category_id] = array();
				foreach ($cat->getNewestBooks(10, $params) as $book) {
					array_push($booksByCategory[$cat->category_id], $book);
				}
			}
			$this->view->booksByCategory = $booksByCategory;
			$this->view->numberOfBooks = Engine_Api::_()->book()->getTotalBookCount($params);
		}	
	}
}