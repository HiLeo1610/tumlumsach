<?php
class Book_Model_DbTable_Rawbooks extends Engine_Db_Table
{
	protected $_rowClass = "Book_Model_Rawbook";
	
	public function getBookFromBookLinkId($bookLinkId) {
		$bookTbl = new Book_Model_DbTable_Books();
		$bookTblName = $bookTbl->info(Zend_Db_Table_Abstract::NAME);
		$rawBookTblName = $this->info(Zend_Db_Table_Abstract::NAME);
		
		$bookSelect = $bookTbl->select()->setIntegrityCheck(false);
		$bookSelect->join($rawBookTblName, "$bookTblName.rawbook_id = $rawBookTblName.rawbook_id");
		$bookSelect->where("$rawBookTblName.link_id = ?", $bookLinkId);
		
		return $bookTbl->fetchRow($bookSelect);
	}
}