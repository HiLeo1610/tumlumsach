<?php
class Book_Model_DbTable_Books extends Book_Model_DbTable_Bases
{
	protected $_rowClass = "Book_Model_Book";

	protected function _getType() {
		return 'book';
	}
	
	public function getTotalCount($params = NULL)
	{
		$adapter = $this->getAdapter();
		$select = new Zend_Db_Select($adapter);
		$tableName = $this->info(Zend_Db_Table_Abstract::NAME);
		$select->from($tableName, 'COUNT(book_id) AS book_count');
		if ($params && isset($params['text']) && !empty($params['text'])) {
			$select->where("$tableName.book_name LIKE ?", "%{$params['text']}%");
		}	

		$data = $adapter->fetchRow($select);
		return (int)@$data['book_count'];
	}
}