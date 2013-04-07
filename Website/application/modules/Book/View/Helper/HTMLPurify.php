<?php
class Book_View_Helper_Purify extends Zend_View_Helper_Abstract {
	protected $_purifier;

	public function __construct($options = null)
	{
		HTMLPurifier_Bootstrap::registerAutoload();
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Strict', true);
		$config->set('Attr.EnableID', true);
		$config->set('Attr.IDPrefix', 'MyPrefix_');
		$this->_purifier = new HTMLPurifier($config);

	}

	public function purify($name, $value = null, $attributes = array()) {
	{
		return $this->_purifier->purify($value);

	}
}