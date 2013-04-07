<?php
class Book_Plugin_Core {
	const COMMENT_POINT = 3;
	const LIKE_POINT = 2;
	const FAV_POINT = 3; 
	const VIEW_POINT = 1;
	const POST_POINT = 3;
	const RATING_COUNT_POINT = 2;
	const RATING_POINT = 3;
	
	private static $_statistic_types = NULL;
	
	private function _getModuleItems() {
		if (empty(self::$_statistic_types)) {
			$manifest = Zend_Registry::get('Engine_Manifest');
			$bookModule = $manifest['book'];
			self::$_statistic_types = $bookModule['items'];	
		}
		
		return self::$_statistic_types;
	}
	public function onCoreCommentCreateAfter($event) {
		$comment = $event->getPayload();
		if (isset($comment) && !empty($comment)) {
			
			if (in_array($comment->resource_type, $this->_getModuleItems())) {
				$popularityTbl = new Book_Model_DbTable_Popularities();
				$popularitySelect = $popularityTbl->select();
				$popularitySelect->where('resource_id = ?', $comment->resource_id);
				$popularitySelect->where('resource_type = ?', $comment->resource_type); 
				$item = $popularityTbl->fetchRow($popularitySelect);
				if (empty($item)) {
					$item = $popularityTbl->getObject($comment->resource_type, $comment->resource_id);
				}
				$item->comment_count = $item->comment_count + 1;
				
				$item->point = $item->point + self::COMMENT_POINT; 
				
				$item->save();
			}
		}
	}
	
	public function onCoreLikeCreateAfter($event) {
		$like = $event->getPayload();
		if (isset($like) && !empty($like)) {
			if (in_array($like->resource_type, $this->_getModuleItems())) {
				$popularityTbl = new Book_Model_DbTable_Popularities();
				$popularitySelect = $popularityTbl->select();
				$popularitySelect->where('resource_id = ?', $like->resource_id);
				$popularitySelect->where('resource_type = ?', $like->resource_type); 
				$item = $popularityTbl->fetchRow($popularitySelect);
				
				if (empty($item)) {
					$item = $popularityTbl->getObject($like->resource_type, $like->resource_id);
				}
				$item->like_count = $item->like_count + 1;
				
				$item->point = $item->point + self::LIKE_POINT; 
				
				$item->save();
			}
		}
	}
	
	public function onCoreLikeDeleteBefore($event) {
		$like = $event->getPayload();
		if (isset($like) && !empty($like)) {
			if (in_array($like->resource_type, $this->_getModuleItems())) {
				$popularityTbl = new Book_Model_DbTable_Popularities();
				$popularitySelect = $popularityTbl->select();
				$popularitySelect->where('resource_id = ?', $like->resource_id);
				$popularitySelect->where('resource_type = ?', $like->resource_type);
				 
				$item = $popularityTbl->fetchRow($popularitySelect);
				if (empty($item)) {
					$item = $popularityTbl->getObject($like->resource_type, $like->resource_id);
				}
				
				$item->like_count = $item->like_count - 1;
				
				$item->point = $item->point - self::LIKE_POINT; 
				
				$item->save();
			}
		}
	}
}