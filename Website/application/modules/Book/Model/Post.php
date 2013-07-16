<?php
class Book_Model_Post extends Book_Model_Base
{
	public function getHref($params = array('type' => 'book_post'))
	{
	    if ($params['type'] == 'book_post')
	    {
    		$params = array_merge(array(
    			'route' => 'post',
    			'reset' => true,
    			'id' => $this->getIdentity(),
    			'slug' => $this->getSlug(),
    			'action' => 'view'
    		), $params);
	    }
	    else
	    {
	        $params = array_merge(array(
	                'route' => 'post',
	                'reset' => true,
	                'id' => $this->getIdentity(),
	                'slug' => $this->getSlug(),
	                'action' => 'view-excerpt'
	        ), $params);
	    }
	    $route = $params['route'];
	    $reset = $params['reset'];
	    unset($params['route']);
	    unset($params['reset']);	    
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}

	public function getTitle()
	{
		if (isset($this->post_name))
		{
			return $this->post_name;
		}
		return null;
	}

	protected function _insert()
	{
	    $this->type = $this->getType();    
	}
	
	public function getParentObject()
	{
		if (!empty($this->parent_id) && !empty($this->parent_type))
		{
			if (Engine_Api::_()->hasItemType($this->parent_type))
			{
				$table = Engine_Api::_()->getItemTable($this->parent_type);
				$tableName = $table->info(Zend_Db_Table_Abstract::NAME);
				$tablePrimaryKey = current($table->info(Zend_Db_Table_Abstract::PRIMARY));

				$signatureTable = new Book_Model_DbTable_Signatures();
				$signatureTableName = $signatureTable->info(Zend_Db_Table_Abstract::NAME);

				$select = $table->select()->setIntegrityCheck(false)->from($tableName);
				$select->join($signatureTableName, "$tableName.$tablePrimaryKey = $signatureTableName.parent_object_id", array(
					"$signatureTableName.view_count",
					"$signatureTableName.favorite_count",
					"$signatureTableName.rating_count",
					"$signatureTableName.rating"
				));
				$select->where("$signatureTableName.parent_object_type = ?", $this->parent_type);
				$select->where("$tableName.$tablePrimaryKey = ?", $this->parent_id);

				return $table->fetchRow($select);
			}
		}
		return NULL;
	}

	public function getTaggedUsers()
	{
		$tagTbl = new Book_Model_DbTable_Tags;
		$tagSelect = $tagTbl->select()->where('object_type = ?', 'user')->where('post_id = ?', $this->getIdentity());
		$userIds = array();
		foreach ($tagTbl->fetchAll($tagSelect) as $row)
		{
			array_push($userIds, $row->object_id);
		}
		if (!empty($userIds))
		{
			if (is_array($userIds))
			{
				return Engine_Api::_()->user()->getUserMulti($userIds);
			}
			return array(Engine_Api::_()->user()->getUser($userIds));
		}
		return array();
	}

	public function getTaggedBooks()
	{
		$tagTbl = new Book_Model_DbTable_Tags;
		$tagSelect = $tagTbl->select()->where('object_type = ?', 'book')->where('post_id = ?', $this->getIdentity());
		$bookIds = array();
		foreach ($tagTbl->fetchAll($tagSelect) as $row)
		{
			array_push($bookIds, $row->object_id);
		}
		if (!empty($bookIds))
		{
			$bookTbl = new Book_Model_DbTable_Books;
			$bookTblName = $bookTbl->info(Zend_Db_Table_Abstract::NAME);
			$bookSelect = $bookTbl->getSelect();
			$bookSelect->where("$bookTblName.book_id in (?)", $bookIds);

			return $bookTbl->fetchAll($bookSelect);
		}
		return array();
	}

	protected function _postDelete()
	{
		parent::_postDelete();

		$tagTbl = new Book_Model_DbTable_Tags;
		$tagTbl->delete(array('post_id = ?' => $this->getIdentity()));

		$signatureTbl = new Book_Model_DbTable_Signatures;
		$signatureTbl->delete(array(
			'parent_object_type = ?' => $this->getType(),
			'parent_object_id = ?' => $this->getIdentity()
		));
	}
	
	public function getKeywords($separator = ' ')
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

	/**
	 * Gets the description of the item. This might be about me for users (todo
	 *
	 * @return string The description
	 */
	public function getDescription()
	{
		if (isset($this->content))
		{
			return $this->content;
		}
		return '';
	}
	
	protected function _postInsert()
    {
    	parent::_postInsert();
		
    	$signatureTbl = new Book_Model_DbTable_Signatures();
		$signature = $signatureTbl->createRow(array(
			'parent_object_id' => $this->getIdentity(),
			'parent_object_type' => $this->getType(),
			'favorite_count' => 0,
			'view_count' => 0
		));
		$signature->save();
		
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
    
    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File)
        {
            $file = $photo->getFileName();
        }
        elseif (is_array($photo) && !empty($photo['tmp_name']))
        {
            $file = $photo['tmp_name'];
        }
        elseif (is_string($photo) && file_exists($photo))
        {
            $file = $photo;
        }
        else
        {
            throw new Exception('invalid argument passed to setPhoto');
        }

        if ($this->photo_id)
        {
            $this->removeOldPhoto();
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => $this->getType()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)->resize(800, 600)->write($path . '/m_' . $name)->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)->resize(400, 400)->write($path . '/p_' . $name)->destroy();

        // Resize image (featured)
        $imageFeatured = Engine_Image::factory();
        $imageFeatured->open($file);

        $size = min($imageFeatured->height, $imageFeatured->width);
        $x = ($imageFeatured->width - $size) / 2;
        $y = ($imageFeatured->height - $size) / 2;

        $imageFeatured->resample($x, $y, $size, $size, 200, 150)->write($path . '/f_' . $name)->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)->write($path . '/is_' . $name)->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/is_' . $name, $params);
        $iFeatured = $storage->create($path . '/f_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.icon');
        $iMain->bridge($iFeatured, 'thumb.featured');

        // Remove temp files
        @unlink($path . '/m_' . $name);
        @unlink($path . '/p_' . $name);
        @unlink($path . '/is_' . $name);
        @unlink($path . '/f_' . $name);

        // Update row
        $this->photo_id = $iMain->getIdentity();
        $this->save();

        return $this;
    }
}