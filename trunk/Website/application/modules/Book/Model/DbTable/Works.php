<?php
class Book_Model_DbTable_Works extends Book_Model_DbTable_Bases
{
	protected $_rowClass = "Book_Model_Work";
	
	protected $_selectedColumns = 
	    array('work_id', 'title', 'user_id', 'published', 'photo_id', 'creation_date');
	
	protected function _getType() {
		return 'book_work';
	}
}