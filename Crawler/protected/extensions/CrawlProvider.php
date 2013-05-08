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
	
	public function getCanonicalUrl($content) {
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
		libxml_use_internal_errors(false);

		$xpath = new DOMXPath($dom);
		$nodes = $xpath->query('//link[@rel="canonical"]');
		if ($nodes->length > 0) {
			$node = $nodes->item(0);
			return $node->getAttribute('href'); 
		}
	} 
	
	public function storeHref($href) {
		$model = Link::model()->find('href = ?', $href);
		if ($model == NULL) {
			$model = new Link();
			$model->href = $href;
			$model->content = file_get_contents($href);
			$model->provider = $this->_providerName;
			$model->type = $this->getType();
			
			$canonicalUrl = $this->getCanonicalUrl($model->content);
			if (!empty($canonicalUrl)) {
				$model->href = $canonicalUrl;
			}
			
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