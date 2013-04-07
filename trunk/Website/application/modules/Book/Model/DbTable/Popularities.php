<?php
class Book_Model_DbTable_Popularities extends Engine_Db_Table {
	protected $_rowClass = "Book_Model_Popularity";
	
	public function getObject($objectType, $objectId) {
		$select = $this->select()->where('resource_type = ?', $objectType)->where('resource_id = ?', $objectId);
		
		$row = $this->fetchRow($select);
		if ($row == NULL) {
			$row = $this->createRow(array('resource_type' => $objectType, 'resource_id' => $objectId));
		}
		
		return $row;
	}
}