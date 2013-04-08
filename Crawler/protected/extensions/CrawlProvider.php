<?php
abstract class CrawlProvider {
	protected $_url;
	protected $_providerName;
	
	function __construct() {}

	public abstract function getLinks();
	public abstract function parseContent($href);
	
	public function storeHref($href) {
		$model = Link::model()->find('href = ?', $href);
		if ($model == NULL) {
			$model = new Link();
			$model->href = $href;
			$model->content = file_get_contents($href);
			$model->provider = $this->_providerName;
			
			if ($model->validate()) {
				echo 'store URL : ' . $href . PHP_EOL;
				$model->save();
			}
		}
		
		return $model;
	}
}