<?php
class Book_Model_DbTable_Rawbooks extends Engine_Db_Table
{
	protected $_rowClass = "Book_Model_Rawbook";
	
	public function getBookFromBookLinkId($bookLinkId) {
		$bookTbl = new Book_Model_DbTable_Books();
		$bookTblName = $bookTbl->info(Zend_Db_Table_Abstract::NAME);
		$rawbookTblName = $this->info(Zend_Db_Table_Abstract::NAME);
		$linkTbl = new Book_Model_DbTable_Links();
		$linkTblName = $linkTbl->info(Zend_Db_Table_Abstract::NAME);
		
		$bookSelect = $bookTbl->select()->setIntegrityCheck(false);
		$bookSelect->join($rawbookTblName, "$bookTblName.rawbook_id = $rawbookTblName.rawbook_id");
		$bookSelect->join($linkTblName, "$rawbookTblName.link_id = $linkTblName.link_id");
		
		return $bookTbl->fetchRow($bookSelect);
	}
}