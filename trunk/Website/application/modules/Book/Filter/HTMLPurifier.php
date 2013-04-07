<?php
class Book_Filter_HTMLPurifier implements Zend_Filter_Interface
{
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

	public function filter($value)
	{
		return $this->_purifier->purify($value);

	}
}