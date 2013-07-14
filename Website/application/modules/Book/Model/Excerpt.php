<?php
class Book_Model_Excerpt extends Book_Model_Post
{
	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'post',
			'reset' => true,
			'id' => $this->getIdentity(),
			'slug' => $this->getSlug(),
			'action' => 'view'
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}
	
}