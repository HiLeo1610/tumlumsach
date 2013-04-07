<?php
class Book_Model_Author extends Core_Model_Item_Abstract {
	protected $_type = 'author';

	public function getHref($params = array()) {
		$params = array_merge(array(
				'route' => 'book_author',
				'reset' => true,
				'action' => 'view',
				'author_id' => $this->getIdentity(),
				'slug' => $this->getSlug()
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}

	public function getTitle()
	{
		if(isset($this->author_name))
		{
			return $this->author_name;
		}
		return null;
	}
}