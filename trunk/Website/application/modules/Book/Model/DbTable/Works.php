<?php
class Book_Model_DbTable_Works extends Book_Model_DbTable_Bases
{
	protected $_rowClass = "Book_Model_Work";
	
	protected function _getType() {
		return 'book_work';
	}
}