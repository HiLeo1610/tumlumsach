<?php
class Book_Model_DbTable_Books extends Book_Model_DbTable_Bases
{
	protected $_rowClass = "Book_Model_Book";

	protected $_selectedColumns = 
	    array('book_id', 'book_name', 'type', 'user_id', 'photo_id', 'creation_date');
	
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
	
	public function getSelect($selectedColumns = null)
	{
	    $select = parent::getSelect($selectedColumns);
	    
	    $tableName = $this->info(Zend_Db_Table_Abstract::NAME);
	    
	    $bookAuthorTable = new Book_Model_DbTable_BookAuthor();
	    $bookAuthorTableName = $bookAuthorTable->info(Zend_Db_Table_Abstract::NAME);
	    
	    $select->join(
            $bookAuthorTableName, 
            "$tableName.book_id = $bookAuthorTableName.book_id",
	        array('GROUP_CONCAT(author_id) As author_ids', 'GROUP_CONCAT(author_name) As author_names')     
        );
	    $select->where("$bookAuthorTableName.type = ?", Book_Plugin_Constants::AUTHOR);
	    
	    return $select;
	}
}