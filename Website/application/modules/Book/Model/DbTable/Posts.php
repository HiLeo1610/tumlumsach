<?php
class Book_Model_DbTable_Posts extends Book_Model_DbTable_Bases
{
    protected $_rowClass = "Book_Model_Post";

    protected $_selectedColumns =
        array('post_id', 'post_name', 'parent_type', 'parent_id', 'creation_date', 'user_id', 'rating', 'rating_count', 'content', 'type', 'photo_id');

    protected function _getType() {
        return 'book_post';
    }
}