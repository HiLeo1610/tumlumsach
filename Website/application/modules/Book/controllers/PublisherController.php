<?php
class Book_PublisherController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		$this->_helper->content->setEnabled();
		
		$userTable = Engine_Api::_()->getItemTable('user');
		$publisherSelect = $userTable->select()->where('verified = 1')->where('approved = 1')->where('enabled = 1');
		$publisherSelect->where('level_id = ?', Book_Plugin_Constants::PUBLISHER_LEVEL);
		$publisherSelect->order('RAND()');
		$this->view->publishers = $userTable->fetchAll($publisherSelect);
		
		$bookCompanySelect = $userTable->select()->where('verified = 1')->where('approved = 1')->where('enabled = 1');
		$bookCompanySelect->where('level_id = ?', Book_Plugin_Constants::BOOK_COMPANY_LEVEL);
		$bookCompanySelect->order('RAND()');
		$this->view->bookCompanies = $userTable->fetchAll($bookCompanySelect);	 		
	}
}