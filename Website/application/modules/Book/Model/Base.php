<?php
class Book_Model_Base extends Core_Model_Item_Abstract
{
	protected function removeOldPhoto() {
		if ($this -> photo_id) {
			$item = Engine_Api::_() -> storage() -> get($this -> photo_id);

			$table = Engine_Api::_() -> getItemTable('storage_file');
			$select = $table -> select() -> where('parent_type = ?', $this -> getType()) -> where('parent_id = ?', $this -> getIdentity());

			foreach ($table->fetchAll($select) as $file) {
				try {
					$file -> delete();
				} catch (Exception $e) {
					if (!($e instanceof Engine_Exception)) {
						$log = Zend_Registry::get('Zend_Log');
						$log -> log($e -> __toString(), Zend_Log::WARN);
					}
				}
			}
		}
	}
	
	public function setRating($user_id, $rating)
	{
		$ratingTable = new Book_Model_DbTable_Ratings();
		$select = $ratingTable->select()->where('parent_object_type = ?', $this->getType())
			->where('parent_object_id = ?', $this->getIdentity())
			->where('user_id = ?', $user_id);

		$row = $ratingTable->fetchRow($select);

		if (empty($row))
		{
			$row = $ratingTable->createRow(array(
				'parent_object_type' => $this->getType(),
				'parent_object_id' => $this->getIdentity(),
				'user_id' => $user_id,
				'rating' => $rating
			));
			$row->save();
			
			$signatureTable = new Book_Model_DbTable_Signatures;
			$select = $signatureTable->select()
				->where('parent_object_id = ?', $this->getIdentity())
				->where('parent_object_type = ?', $this->getType());
			$signatureRow = $signatureTable->fetchRow($select);
			if ($signatureRow == NULL) {
				$signatureRow = $signatureTable->createRow(array(
					'parent_object_id' => $this->getIdentity(),
					'parent_object_type' => $this->getType()
				));	
			}	 
	
			$signatureRow->rating_count = $signatureRow->rating_count + 1;
			$ratingSum = $this->getRatingSum();
			if ($ratingSum != 0) {
				$signatureRow->rating = $ratingSum/$signatureRow->rating_count;
			} else {
				$signatureRow->rating = $rating;
			}
			
			$signatureRow->save();
		}
	}

	public function getRatingCount()
	{
		$ratingTable = new Book_Model_DbTable_Ratings();
		
		$ratingCount = $ratingTable->select()
			->from($ratingTable->info('name'), new Zend_Db_Expr('COUNT(*)'))
			->where("parent_object_id = ?", $this->getIdentity())
			->where("parent_object_type = ?", $this->getType())
			->query()
			->fetchColumn(0);
			
		return $ratingCount;
	}
	
	public function getRatingSum() {
        $table = new Book_Model_DbTable_Ratings();
        $ratingSum = $table->select()
            ->from($table->info('name'), new Zend_Db_Expr('SUM(rating)'))
            ->where('parent_object_id = ?', $this->getIdentity())
			->where('parent_object_type = ?', $this->getType())
            ->query()
            ->fetchColumn(0);

        return $ratingSum;
    }

	/**
	 *  Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
	}

	/**
	 *  Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function tags()
	{
		return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
	}

	public function addPhoto($photo, $viewer) {
		$file = $photo->getFileName();
		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id'   => $viewer->getIdentity(),
			'parent_type' => $viewer->getType()
		);
		// Save
		$storage = Engine_Api::_()->storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image->open($file)->write($path . '/m_' . $name)->destroy();

		// Store
		$iMain = $storage->create($path . '/m_' . $name, $params);

		// Remove temp files
		@unlink($path . '/m_' . $name);
		
		$photos = new Book_Model_DbTable_Photos();
		$photo = $photos->createRow(array(
			'parent_object_type' => $this->getType(),
			'parent_object_id' => $this->getIdentity(),
			'file_id' => $iMain->getIdentity(),
			'user_id' => $viewer->getIdentity() 
		));
		$photo->save();
		
		return $photo;
	}
	
	// TODO [DangTH] : check again the performance of this function
	public function getAllApprovedPhotos() {
		$photoTbl = new Book_Model_DbTable_Photos;
		$select = $photoTbl->select();
		$select->where("parent_object_type = ?", $this->getType())
			->where("parent_object_id = ?", $this->getIdentity())
			->where("approved = ?", 1);
		$select->order('default DESC');	
		$photos = array();
		$storageApi = Engine_Api::_()->storage();
		foreach($photoTbl->fetchAll($select) as $photo) {
			$file = $storageApi->get($photo->file_id);
			if (!empty($file)) {
				array_push($photos, $file->storage_path);
			}
		}				
			
		return $photos;
	}
	
	public function getKeywords($separator = ' ' )
	{
		$keywords = array();
		foreach( $this->tags()->getTagMaps() as $tagmap ) {
			$tag = $tagmap->getTag();
			$keywords[] = $tag->getTitle();
		}

		if( null === $separator ) {
			return $keywords;
		}

		return join($separator, $keywords);
	}
	
	public function isUserFavorite($user) {
		$favoriteTbl = new Book_Model_DbTable_Favorites();
		$select = $favoriteTbl->select();
		$select->where('user_id = ?', $user->getIdentity());
		$select->where('parent_object_id = ?', $this->getIdentity());
		$select->where('parent_object_type = ?', $this->getType()); 
		$favorite = $favoriteTbl->fetchRow($select);
		
		return ($favorite != NULL);
	}
}	