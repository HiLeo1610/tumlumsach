<?php
class Book_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  	{
    	parent::__construct($application);
    	$this->initViewHelperPath();
	}
  
	protected function _initBook() {
		$view = Zend_Registry::get('Zend_View');
		
		$view->headScript()->appendFile($view->layout()->staticBaseUrl 
			. 'application/modules/Book/externals/scripts/core.js');
		$view->headTranslate('click to rate');
		$view->headTranslate('you already rated');
		$view->headTranslate('please log in to rate');
		$view->headTranslate(array('%s rating', '%s ratings'));	
		$view->headTranslate('Thanks for rating');
		
		Zend_Loader::loadFile('Bootstrap.php', 
			$this->getModulePath() . '/libs/HTMLPurifier/', 
			true);
	}
}