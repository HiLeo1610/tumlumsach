<?php

class Book_Widget_PostProfileBookController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$post = Engine_Api::_()->core()->getSubject();
		if (!empty($post->parent_object_type) && !empty($post->parent_object_id))
		{
			if ($post->parent_object_type == 'book')
			{
				$bookTbl = new Book_Model_DbTable_Books;
				$bookSelect = $bookTbl->getSelect()->where('book_id = ?', $post->parent_object_id); 
				$this->view->book = $book = $bookTbl->fetchRow($bookSelect);
				$this->view->authors = $authors = $book->getAuthors(0);

				$categoryTable = new Book_Model_DbTable_Categories();
				$this->view->category = $categoryTable->findRow($book->category_id);

				$bookApi = Engine_Api::_()->book();
				$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
				$this->view->rated = $bookApi->checkRated($book->getIdentity(), $book->getType(), $viewer->getIdentity());

				if ($book->is_foreign)
				{
					$this->view->translators = $translators = $book->getAuthors(1);
				}
			}
		}
		else
		{
			return $this->setNoRender();
		}
	}

}