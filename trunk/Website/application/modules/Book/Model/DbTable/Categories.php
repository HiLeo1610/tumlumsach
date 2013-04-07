<?php
class Book_Model_DbTable_Categories extends Engine_Db_Table
{
    protected $_rowClass = "Book_Model_Category";

    public function getMultiOptions()
    {
        $select = $this->_db->select()->from($this->info('name'), array(
            'key' => 'category_id',
            'value' => 'category_name'
        ));
		$select->order(array('ordering', 'category_name'));
        $result = $this->getAdapter()->fetchAll($select);
        return $result;

    }
}
