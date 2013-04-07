<?php
class Book_Model_DbTable_Signatures extends Engine_Db_Table {
	protected $_rowClass = "Book_Model_Signature";

	public function getSignaturesOfItems($items) {
		if (!empty($items)) {
			$item = $items[0];
            if ($item) {
    			$itemIds = array();
    			foreach($items as $item) {
    				array_push($itemIds, $item->getIdentity());
    			}
    			$select = $this->select()->where('parent_object_type = ?', $item->getType())
    				->where('parent_object_id IN (?)', $itemIds);
    			$objects = array();
    			foreach($this->fetchAll($select) as $record) {
    				$objects[$record->getIdentity()] = $record;
    			}
    			return $objects;
            }
		}

		return null;
	}
}