<?php
class Book_Model_DbTable_Chapters extends Book_Model_DbTable_Bases
{
	protected $_rowClass = "Book_Model_Chapter";
	
	protected function _getType() {
		return 'book_chapter';
	}
}