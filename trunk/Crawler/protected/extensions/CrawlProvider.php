<?php
abstract class CrawlProvider {	
	protected $_urls;
	protected $_providerName;
	
	function __construct() {}
	
	public abstract function getLinks($url);
	public abstract function parseContent($href);
	public abstract function getType();
	
	public function getUrls() {
		return $this->_urls;
	}
	
	public function storeHref($href) {
		$model = Link::model()->find('href = ?', $href);
		if ($model == NULL) {
			$model = new Link();
			$model->href = $href;
			$model->content = file_get_contents($href);
			$model->provider = $this->_providerName;
			$model->type = $this->getType();
				
			if ($model->validate()) {
				echo 'store URL : ' . $href . PHP_EOL;
				$model->save();
			}
		}
	
		return $model;
	}
	
	public static function createNewObject($type) {
		if ($type == 0) {
			return new Book();
		}
		return new Post();
	}
	
	public static function getObjClassName($type) {
		if ($type == 0) {
			return 'Book';
		}
		return 'Post';
	} 
	
	public function getProviderName() {
		return $this->_providerName;
	}
}