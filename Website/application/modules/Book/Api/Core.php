<?php
class Book_Api_Core extends Core_Api_Abstract
{
	public function getBooksSelect($params = array(), $order_by = true)
	{
		$table = new Book_Model_DbTable_Books;
		$select = $table->getSelect();

		if (!empty($params['orderby']))
		{
			$select->order($params['orderby']);
		}
		if (!empty($params['text']))
		{
			$select->where("book_name LIKE ?", "%{$params['text']}%");
		}
		
		return $select;
	}

	public function getBooksPaginator($params = array(), $order_by = true)
	{
		$paginator = Zend_Paginator::factory($this->getBooksSelect($params, $order_by));
		if (!empty($params['page']))
		{
			$paginator->setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator->setItemCountPerPage($params['limit']);
		}

		return $paginator;
	}

	public function getRatingCount($item)
	{
		$table = new Book_Model_DbTable_Ratings();
		$select = $table->select()->where('parent_object_id = ?', $item->getIdentity())->where('parent_object_type', $item->getType());
		$row = $table->fetchAll($select);
		$total = count($row);
		
		return $total;
	}

	public function checkRated($object_id, $object_type, $user_id)
	{
		if (!empty($user_id))
		{
			$ratingTable = new Book_Model_DbTable_Ratings();
			$select = $ratingTable->select()->where('parent_object_type = ?', $object_type)->where('parent_object_id = ?', $object_id)->where('user_id = ?', $user_id);
			return ($ratingTable->fetchRow($select) != NULL);
		}
		
		return NULL;
	}

	// public function getBook($bookId)
	// {
		// $bookTbl = new Book_Model_DbTable_Books;
		// $select = $bookTbl->getSelect();
		// $select->where("book_id = ?", $bookId);
		// return $bookTbl->fetchRow($select);
	// }
	
	public function getObject($type, $id) {
		$object = Engine_Api::_()->getItem($type, $id);
		if ($object) {
			$signatureTbl = new Book_Model_DbTable_Signatures;
			$signatureSel = $signatureTbl->select();
			$signatureSel->where('parent_object_type = ?', $type)->where('parent_object_id = ?', $id);
			$signature = $signatureTbl->fetchRow($signatureSel);
			if ($signature == NULL) {
				$signature = $signatureTbl->createRow(array(
					'parent_object_id' => $id,
					'parent_object_type' => $type,
					'favorite_count' => 0,
					'view_count' => 0
				));
				$signature->save(); 
			} 
			
			$itemTbl = Engine_Api::_()->getItemTable($type);
			$select = $itemTbl->getSelect();
			$tablePrimaryKey = current($itemTbl->info(Zend_Db_Table_Abstract::PRIMARY));
			$select->where("$tablePrimaryKey = ?", $id);
			return $itemTbl->fetchRow($select);
		}
	}
	
	public function getTotalBookCount($params = NULL) {
		$bookTbl = new Book_Model_DbTable_Books;
		return $bookTbl->getTotalCount($params);
	}
	
	public function isFavorite($user, $object) {
		$favTbl = new Book_Model_DbTable_Favorites;
		$select = $favTbl->select()->where('user_id = ?', $user->getIdentity());
		$select->where('parent_object_id = ?', $object->getIdentity());
		$select->where('parent_object_type = ?', $object->getType());
		
		return $favTbl->fetchRow($select); 
	}
}
