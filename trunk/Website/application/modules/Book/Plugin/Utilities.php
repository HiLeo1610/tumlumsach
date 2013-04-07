<?php
class Book_Plugin_Utilities
{
    public static function getPublishers()
    {
        $table = Engine_Api::_()->getItemTable('user');
        $select = $table->select()->from($table->info('name'), array(
                'key' => 'user_id',
                'value' => 'displayname'
        ));
        $select->where('level_id = ?', Book_Plugin_Constants::PUBLISHER_LEVEL);
        $select->order('displayname');
        $result = $table->getAdapter()->fetchAll($select);
        
        return $result;
    }
	
	public static function getBookCompanies()
    {
        $table = Engine_Api::_()->getItemTable('user');
        $select = $table->select()->from($table->info('name'), array(
                'key' => 'user_id',
                'value' => 'displayname'
        ));
        $select->where('level_id = ?', Book_Plugin_Constants::BOOK_COMPANY_LEVEL);
        $select->order('displayname');
        $result = $table->getAdapter()->fetchAll($select);
        
        return $result;
    }
}
