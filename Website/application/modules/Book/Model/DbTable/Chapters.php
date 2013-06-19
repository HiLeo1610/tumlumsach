<?php
class Book_Model_DbTable_Chapters extends Book_Model_DbTable_Bases
{
	protected $_rowClass = "Book_Model_Chapter";
	
	protected $_selectedColumns = array('chapter_id', 'title', 'work_id', 'creation_date');
	
	protected function _getType() {
		return 'book_chapter';
	}
}