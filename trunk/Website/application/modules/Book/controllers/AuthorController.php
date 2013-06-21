<?php
class Book_AuthorController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		$this->_helper->content->setEnabled();
		
		$page = $this->_getParam('page', 1);
		
		$userTable = Engine_Api::_()->getItemTable('user');
		$userSelect = $userTable->select()->where('verified = 1')->where('approved = 1')->where('enabled = 1');
		$userSelect->where('level_id = ?', Book_Plugin_Constants::AUTHOR_LEVEL);
		$userSelect->order('RAND()');
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($userSelect);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(40); 
	}
	
	public function suggestAction()
	{
		$text = $this->_getParam('value');

		if (!empty($text))
		{
			$userTable = Engine_Api::_()->getItemTable('user');
			$select = $userTable->select()->where('username LIKE ? OR displayname LIKE ?', "%$text%");
			$select->where('enabled = 1')->where('verified = 1')->where('approved = 1')->where('level_id = ?', Book_Plugin_Constants::AUTHOR_LEVEL);
			
			$data = array();
			foreach ($userTable->fetchAll($select) as $user)
			{
				$record = array(
					'id' => $user->getIdentity(),
					'label' => $user->displayname,
					'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
					'url' => $user->getHref(),
					'type' => $user->getType(),
					'id' => $user->getIdentity(),
					'guid' => $user->getGuid(),
				);
				array_push($data, $record);
			}
			return $this->_helper->json($data);
		}
	}
}