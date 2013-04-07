<?php

abstract class Book_Controller_Base extends Core_Controller_Action_Standard
{
	protected $_subject = NULL;

	protected abstract function _getSubjectType();
	
	protected function _getAddFavoriteMsg() {
		return "Add this book to your favorite books successfully.";
	}
	
	protected function _getRemoveFavoriteMsg() {
		return "Remove this book from your favorite books successfully.";
	}
	
	protected function _initActions()
	{
		$objectId = $this->_getParam('id', 0);
		if ($objectId)
		{
			$object = Engine_Api::_()->book()->getObject($this->_getSubjectType(), $objectId);
			if ($object)
			{
				Engine_Api::_()->core()->setSubject($object);
			}
		}
	}

	protected function _checkSubject()
	{
		if (!$this->_helper->requireSubject($this->_getSubjectType())->isValid())
		{
			$this->redirectAndExit();
		}
		if ($this->_subject == NULL)
		{
			$this->_subject = Engine_Api::_()->core()->getSubject();
		}
	}

	protected function _getSubject()
	{
		if (Engine_Api::_()->core()->hasSubject())
		{
			if ($this->_subject == NULL)
			{
				$this->_subject = Engine_Api::_()->core()->getSubject();
			}
			return $this->_subject;
		}

		return NULL;
	}

	protected function _checkAuthorization($action = 'view')
	{
		if ($action != 'view') {
			if (!$this->_helper->requireUser()->isValid())
			{
				$this->redirectAndExit();
			}
		}
		
		if (!$this->_helper->requireAuth()->setAuthParams($this->_getSubjectType(), null, $action)->isValid())
		{
			$this->redirectAndExit();
		}
	}

	private function _getSignature()
	{
		$subject = $this->_getSubject();
		if ($subject) {
			$signatureTable = new Book_Model_DbTable_Signatures();
			$select = $signatureTable->select()->where('parent_object_id = ?', $subject->getIdentity())->where('parent_object_type = ?', $subject->getType());
			$signature = $signatureTable->fetchRow($select);
			if ($signature == null)
			{
				$signature = $signatureTable->createRow(array(
					'parent_object_id' => $subject->getIdentity(),
					'parent_object_type' => $subject->getType(),
					'favorite_count' => 0,
					'view_count' => 0
				));
			}
	
			return $signature;
		}
	}

	public function viewAction()
	{
		$this->_initActions();
		$subject = $this->_getSubject();
		
		$this->_checkSubject();
		$this->_checkAuthorization('view');
		$signature = $this->_getSignature();

		$signature->view_count = $signature->view_count + 1;
		$signature->save();

		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->signature = $signature;
		$this->view->subject = $subject;

		$this->view->rated = Engine_Api::_()->book()->checkRated($subject->getIdentity(), $subject->getType(), $viewer->getIdentity());
	}

	public function favoriteAction()
	{
		$this->_initActions();
		$subject = $this->_getSubject();
		$this->_checkSubject();

		$viewer = Engine_Api::_()->user()->getViewer();

		if (Engine_Api::_()->book()->isFavorite($viewer, $subject) == NULL)
		{
			$actionTbl = Engine_Api::_()->getDbTable('actions', 'activity');
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			try
			{
				$favTable = new Book_Model_DbTable_Favorites;
				$fav = $favTable->createRow(array(
					'parent_object_id' => $subject->getIdentity(),
					'parent_object_type' => $subject->getType(),
					'user_id' => $viewer->getIdentity()
				));
				$fav->save();
				
				$action = $actionTbl->addActivity($viewer, $subject, 'add_favorite');
				if ($action) {
					$actionTbl->attachActivity($action, $subject);
				}

				$signature = $this->_getSignature();
				$signature->favorite_count = $signature->favorite_count + 1;
				$signature->save();
				
				$popularity = Engine_Api::_()->getItemTable('book_popularity')->getObject($subject->getType(), $subject->getIdentity());
				$popularity->favorite_count = $popularity->favorite_count + 1;
				$popularity->point = $popularity->point + Book_Plugin_Core::FAV_POINT;
				$popularity->save();  

				$db->commit();
			}
			catch (Exception $e)
			{
				$db->rollBack();
				throw $e;
			}

			return $this->_forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->_getAddFavoriteMsg()))
			));
		}
	}

	public function removeFavoriteAction()
	{
		$this->_initActions();
		$subject = $this->_getSubject();
		$this->_checkSubject();

		$viewer = Engine_Api::_()->user()->getViewer();

		if (Engine_Api::_()->book()->isFavorite($viewer, $subject))
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			try
			{
				$favTable = new Book_Model_DbTable_Favorites;
				$fav = $favTable->delete(array(
					'parent_object_id = ?' => $subject->getIdentity(),
					'parent_object_type = ?' => $subject->getType(),
					'user_id = ?' => $viewer->getIdentity()
				));

				$signature = $this->_getSignature();
				if ($signature->favorite_count > 0)
				{
					$signature->favorite_count = $signature->favorite_count - 1;
					$signature->save();
				}
				
				if ($subject->getType() == 'book') {
					$popularity = Engine_Api::_()->getItem('book_popularity', $subject->getIdentity());
					$popularity->favorite_count = $popularity->favorite_count - 1;
					$popularity->point = $popularity->point - Book_Plugin_Core::FAV_POINT;
					$popularity->save();  
				}

				$db->commit();
			}
			catch (Exception $e)
			{
				$db->rollBack();
				throw $e;
			}

			return $this->_forward('success', 'utility', 'core', array(
				'layout' => 'default-simple',
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->_getRemoveFavoriteMsg()))
			));
		}
	}

	public function rateAction()
	{
		if (!$this->_helper->requireUser()->isValid())
		{
			return;
		}
		$object_id = (int)$this->_getParam('id');
		if ($object_id)
		{
			$object = Engine_Api::_()->getItem($this->_getSubjectType(), $object_id);
			if ($object)
			{
				Engine_Api::_()->core()->setSubject($object);
			}
		}
		if (!$this->_helper->requireSubject($this->_getSubjectType())->isValid())
		{
			return;
		}

		if (!$this->_helper->requireAuth()->setAuthParams($this->_getSubjectType(), null, 'view')->isValid())
		{
			return;
		}

		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();

		$rating = (int)$this->_getParam('rating');
		$object->setRating($user_id, $rating);
		$total = $object->getRatingCount();
		
		if (!empty($object)) {
			$popularity = Engine_Api::_()->getItemTable('book_popularity')->getObject($object->getType(), $object->getIdentity());
			$previousRating = $popularity->rating_point; 
			$popularity->rating_count = $popularity->rating_count + 1;
			$popularity->rating_point = $rating;
			$popularity->point = $popularity->point + Book_Plugin_Core::RATING_COUNT_POINT + ($rating - $previousRating) * Book_Plugin_Core::RATING_POINT;
			$popularity->save();	
		}

		$data = array();
		$data[] = array(
			'total' => $total,
			'rating' => $rating,
		);

		return $this->_helper->json($data);
	}
}
