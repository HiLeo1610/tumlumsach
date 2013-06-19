<?php
class Book_Model_Work extends Book_Model_Base
{
	protected $_owner_type = 'user';
	protected $_parent_is_owner = true;
	
	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'work',
			'reset' => true,
			'id' => $this->getIdentity(),
			'slug' => $this->getSlug()
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}
	
	protected function _update() 
	{
		if (isset($this->_data['content'])) {
			unset($this->_data['content']);
		}	
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
    }
	
	public function getChapters($published = NULL) 
	{
		$chapterTbl = new Book_Model_DbTable_Chapters();
		$select = $chapterTbl->select()->where('work_id = ?', $this->getIdentity());
		if ($published != NULL) {
			$select->where('published = ?', $published);
		}
		
		return $chapterTbl->fetchAll($select); 
	}
	
	public function setContent($content) {
		$this->_data['content'] = $content;
	}
	
	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo->getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
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