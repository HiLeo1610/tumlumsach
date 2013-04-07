<?php
class Book_Model_Photo extends Book_Model_Base
{
	protected function _postDelete()
	{
		$file = Engine_Api::_()->storage()->get($this->file_id);
		$file->delete();
	}

	/**
	 * Gets a url to the current photo representing this item. Return null if none
	 * set
	 *
	 * @param string The photo type (null -> main, thumb, icon, etc);
	 * @return string The photo url
	 */
	public function getPhotoUrl($type = null)
	{
		if (empty($this->file_id))
		{
			return null;
		}

		$file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
		if (!$file)
		{
			return null;
		}

		return $file->map();
	}

}
