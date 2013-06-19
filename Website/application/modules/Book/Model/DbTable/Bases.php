<?php
abstract class Book_Model_DbTable_Bases extends Engine_Db_Table
{
	protected abstract function _getType();
	
	protected $_selectedColumns = "*";
		
	public function getSelect($selectedColumns = null)
	{
		$signatureTable = new Book_Model_DbTable_Signatures();
		$signatureTableName = $signatureTable->info(Zend_Db_Table_Abstract::NAME); 
		$tableName = $this->info(Zend_Db_Table_Abstract::NAME);
		
		// TODO [DangTH] : check again
		$tablePrimaryKey = current($this->info(Zend_Db_Table_Abstract::PRIMARY));
		
		if ($selectedColumns == null) {
		    $select = $this->select()->from($tableName, $this->_selectedColumns);
		} else {
		    $select = $this->select()->from($tableName, $selectedColumns);
		}
		
		$select->setIntegrityCheck(false);
		
		$select->joinLeft(
	        $signatureTableName, 
	        "$signatureTableName.parent_object_id = $tableName.$tablePrimaryKey"
        );
		
		$select->where("$signatureTableName.parent_object_type = ?", $this->_getType());
		
		$select->group("$tableName.$tablePrimaryKey");
		
		return $select;				
	}
}