<?php
class Book_Model_DbTable_Posts extends Book_Model_DbTable_Bases 
{
	protected $_rowClass = "Book_Model_Post";
	
	protected function _getType() {
		return 'book_post';
	}
	
	// public function getSelect()
	// {
		// $signatureTable = new Book_Model_DbTable_Signatures();
		// $signatureTableName = $signatureTable->info(Zend_Db_Table_Abstract::NAME); 
		// $tableName = $this->info(Zend_Db_Table_Abstract::NAME);
		// $tablePrimaryKey = current($this->info(Zend_Db_Table_Abstract::PRIMARY));
// 		
		// $select = $this->select()->from($tableName)->setIntegrityCheck(false);
		// $select->join($signatureTableName, "$signatureTableName.parent_object_id = $tableName.$tablePrimaryKey", 
			// array("$signatureTableName.favorite_count", "$signatureTableName.view_count"));
		// $select->where("$signatureTableName.parent_object_type = ?", $this->_getType());
// 		
		// return $select;				
	// }
}