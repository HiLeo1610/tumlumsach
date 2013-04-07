<?php
class Book_ShopController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		$this->_helper->content->setEnabled();
	}
}