<?php

class Book_Widget_ProfileBookController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this->view->viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		if ($subject->getType() != 'book') {
			$subject = $subject->getParentObject();
		}
		if ($subject && $subject->getType() == 'book') {
			$this->view->book = $book = $subject;
			$this->view->authors = $authors = $book->getAuthors(Book_Plugin_Constants::AUTHOR);
	
			$categoryTable = new Book_Model_DbTable_Categories;
			$this->view->category = $categoryTable->findRow($book->category_id);
	
			$bookApi = Engine_Api::_()->book();
			$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
			$this->view->rated = $bookApi->checkRated($book->getIdentity(), $book->getType(), $viewer->getIdentity());
	
			if ($book->is_foreign)
			{
				$this->view->translators = $translators = $book->getAuthors(1);
			}
		} else {
			return $this->setNoRender();
		}
	}
}