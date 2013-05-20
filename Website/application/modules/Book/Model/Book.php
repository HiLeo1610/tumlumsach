<?php
class Book_Model_Book extends Book_Model_Base {
	protected $_owner_type = 'user';
	protected $_parent_is_owner = true;

	public function getHref($params = array()) {
		$params = array_merge(array('route' => 'book', 'reset' => true, 'id' => $this -> getIdentity(), 'slug' => $this -> getSlug()), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getTitle() {
		if (isset($this -> book_name)) {
			return $this -> book_name;
		}
		return null;
	}

	public function setPhoto($photo) {
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
		} else if (is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
		} else if (is_string($photo) && file_exists($photo)) {
			$file = $photo;
		} else {
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		if ($this -> photo_id) {
			$this -> removeOldPhoto();
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_id' => $this -> getIdentity(), 'parent_type' => $this -> getType());

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(400, 600) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;

		$image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> getIdentity();
		$this -> save();

		return $this;
	}

	/**
	 * get authors or translators of a book
	 * @param int $type ($type = 0 for author, $type = 1 for translator)
	 * @return Engine_Db_Table_Rowset of User_Model_User
	 */
	public function getAuthors($type = Book_Plugin_Constants::AUTHOR) {
		$bookAuthorTable = new Book_Model_DbTable_BookAuthor();
		$select = $bookAuthorTable -> select(array('author_id', 'author_name')) -> where('book_id = ?', $this -> getIdentity()) -> where('type = ?', $type);
		$authorIds = array();
		$authorNames = array();
		foreach ($bookAuthorTable->fetchAll($select) as $row) {
			if (!empty($row -> author_id)) {
				array_push($authorIds, $row -> author_id);
			} else {
				if (!empty($row -> author_name)) {
					array_push($authorNames, $row -> author_name);
				}
			}
		}

		$authors = array();
		if (!empty($authorIds)) {
			$authors = Engine_Api::_() -> user() -> getUserMulti($authorIds);
		}
		if (!empty($authorNames)) {
			foreach ($authorNames as $authorName) {
				array_push($authors, $authorName);
			}
		}
		return $authors;
	}

	public function getPublisher() {
		if (!empty($this -> publisher_id)) {
			return Engine_Api::_() -> user() -> getUser($this -> publisher_id);
		}
		return null;
	}

	public function getBookCompany() {
		if (!empty($this -> book_company_id)) {
			return Engine_Api::_() -> user() -> getUser($this -> book_company_id);
		}
		return null;
	}

	public function getConcernedUsers() {
		$authors = array(); 
		foreach ($this -> getAuthors(Book_Plugin_Constants::AUTHOR, TRUE) as $author) {
			array_push($authors, $author);
		}
		$translators = array();
		if ($this->is_foreign) {
			foreach ($this -> getAuthors(Book_Plugin_Constants::TRANSLATOR, TRUE) as $author) {
				array_push($translators, $author);
			}	
		}
		
		$arr = array_merge($authors, $translators);
		
		array_push($arr, $this -> getOwner());

		return $arr;
	}

	public function isBookAuthor($user) {
		$bookAuthorTable = new Book_Model_DbTable_BookAuthor;
		$select = $bookAuthorTable -> select() -> where('book_id = ?', $this -> getIdentity()) -> where('author_id = ?', $user -> getIdentity());
		return ($bookAuthorTable -> fetchRow($select) != NULL);
	}

	public function isPublisher($user) {
		return $this -> publisher_id == $user -> getIdentity();
	}

	public function isBookCompany($user) {
		return $this -> book_company_id == $user -> getIdentity();
	}

	protected function _postInsert() {
		parent::_postInsert();

		$signatureTbl = new Book_Model_DbTable_Signatures();
		$signature = $signatureTbl -> createRow(array('parent_object_id' => $this -> getIdentity(), 'parent_object_type' => $this -> getType(), 'favorite_count' => 0, 'view_count' => 0));
		$signature -> save();

		$popularityTbl = new Book_Model_DbTable_Popularities();
		$popularity = $popularityTbl -> createRow(
			array(
				'resource_id' => $this -> getIdentity(),
				'resource_type' => $this -> getType(), 
				'posted_date' => date('Y-m-d H:i:s')
			)
		);

		$popularity -> save();
	}

	protected function _postDelete() {
		parent::_postDelete();

		$bookPhotoTbl = new Book_Model_DbTable_Photos;
		$bookPhotoTbl -> delete(array('parent_object_type = ?' => $this -> getType(), 'parent_object_id = ?' => $this -> getIdentity()));

		$bookAuthorTbl = new Book_Model_DbTable_BookAuthor;
		$bookAuthorTbl -> delete(array('book_id = ?' => $this -> getIdentity()));

		$bookFavTbl = new Book_Model_DbTable_Favorites;
		$bookFavTbl -> delete(array('parent_object_type = ?' => $this -> getType(), 'parent_object_id = ?' => $this -> getIdentity()));

		$bookPostTbl = new Book_Model_DbTable_Posts;
		$bookPostTbl -> update(array('parent_id' => NULL, 'parent_type' => NULL), array('parent_id = ?' => $this -> getIdentity(), 'parent_type = ?' => $this -> getType()));

		$bookRatingTbl = new Book_Model_DbTable_Ratings;
		$bookRatingTbl -> delete(array('parent_object_type = ?' => $this -> getType(), 'parent_object_id = ?' => $this -> getIdentity()));

		$popularityTbl = new Book_Model_DbTable_Popularities();
		$popularity = $popularityTbl -> delete(array(
			'resource_id = ?' => $this -> getIdentity(),
			'resource_type = ?' => $this -> getType()
		));
	}
}