<?php
class Book_ChapterController extends Book_Controller_Base
{
	protected function _getSubjectType()
	{
		return 'book_chapter';
	}

	public function indexAction()
	{
		$this->_helper->content->setEnabled();
	}

	protected function _initActions()
	{
		$chapterId = $this->_getParam('id', 0);
		if ($chapterId)
		{
			$chapter = Engine_Api::_()->book()->getObject($this->_getSubjectType(), $chapterId);
			if (isset($chapter) && $chapter->getType() == $this->_getSubjectType())
			{
				if ($chapter->isPublished()) {
					Engine_Api::_()->core()->setSubject($chapter);
				} else {
					$viewer = Engine_Api::_()->user()->getViewer();
					$owner = $chapter->getOwner();
					if ($viewer->isSelf($owner) || $viewer->isAdmin()) {
						Engine_Api::_()->core()->setSubject($chapter);	
					} 
				}
			}
		}
	}

	protected function _checkAuthorization($action = 'view')
	{
		parent::_checkAuthorization('view');

		if ($action != 'view')
		{
			$subject = $this->_getSubject();

			$viewer = Engine_Api::_()->user()->getViewer();
			if (!$subject->isBookAuthor($viewer) 
				&& !$subject->isPublisher($viewer) 
				&& !$subject->isBookCompany($viewer) 
				&& !$viewer->isAdmin()
				&& !$subject->getOwner()->isSelf($viewer))
			{
				return !$this->_helper->requireAuth()->forward();
			}
		}
	}

	public function viewAction()
	{
		parent::viewAction();
		
		$subject = $this->_getSubject();
		$popularity = Engine_Api::_()->getItemTable('book_popularity')->getObject($subject->getType(), $subject->getIdentity());
		$popularity->view_count = $popularity->view_count + 1;
		$popularity->point = $popularity->point + Book_Plugin_Core::VIEW_POINT;
		$popularity->resource_id = $subject->getIdentity();
		$popularity->resource_type = $subject->getType();
		$popularity->save();
		
		$this->view->work = $subject->getParent(); 
		
		$this->_helper->content->setEnabled();
	}

	public function createAction()
	{
		if (!$this->_helper->requireUser()->isValid())
		{
			return;
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$workId = $this->_getParam('work_id');
		if (isset($workId)) {
			$work = Engine_Api::_()->getItem('book_work', $workId);
		}
		if (isset($work) && $work instanceof Book_Model_Work) {
			$this->view->form = $form = new Book_Form_Chapter( array('chapterTitle' => 'Post a chapter'));
	
			if (!$this->getRequest()->isPost())
			{
				return;
			}
	
			if (!$form->isValid($this->getRequest()->getPost()))
			{
				return;
			}
	
			$values = $form->getValues();
			$chapterTable = new Book_Model_DbTable_Chapters();
	
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
	
			try
			{
				$chapter = $chapterTable->createRow($values);
				$chapter->creation_date = date('Y-m-d H:i:s');
				$chapter->modified_date = date('Y-m-d H:i:s');
				$chapter->work_id = $workId;
				
				$chapter->save();				
				
				// CREATE AUTH STUFF HERE
	            $auth = Engine_Api::_()->authorization()->context;
	          	$roles = array('owner', 'parent_member', 'registered', 'everyone');
	            foreach ($roles as $i => $role) {
	                $auth->setAllowed($chapter, $role, 'view', true);
	            }
	
				$db->commit();
			}
			catch (Exception $e)
			{
				$db->rollBack();
				throw $e;
			}
	
			if ($chapter->published && $work->published) {
				$actionTbl = Engine_Api::_()->getDbTable('actions', 'activity');
				$action = $actionTbl->addActivity($viewer, $work, 'chapter_new');
				if ($action != null)
				{
					$actionTbl->attachActivity($action, $chapter);
				}
			}
	
			$this->_redirectCustom($chapter->getHref());
		}
	}

	public function editAction()
	{
		$this->_initActions();
		$subject = $this->_getSubject();
		$this->_checkSubject();
		$this->_checkAuthorization('view');

		$this->view->form = $form = new Book_Form_Chapter( array('chapterTitle' => 'Edit the chapter'));
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		if (!$this->getRequest()->isPost())
		{
			$form->populate($subject->toArray());
			return;
		}

		if ($form->isValid($this->getRequest()->getPost()))
		{
			$values = $form->getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();

			try
			{
				$values = $form->getValues();
				$subject->setFromArray($values);
				$subject->save();

				$db->commit();
			}
			catch(Exception $e)
			{
				$db->rollBack();
				throw $e;
			}
			
			$work = $subject->getWork();
			if (!empty($work)) {
				if ($subject->published && $work->published) {
					$actionTbl = Engine_Api::_()->getDbTable('actions', 'activity');
					$actions = $actionTbl->getActionsByAttachmentAndType($subject, 'chapter_new');
					if(empty($actions)) {
						$action = $actionTbl->addActivity($viewer, $work, 'chapter_new');
						if ($action != null)
						{
							$actionTbl->attachActivity($action, $chapter);
						}
					} 
				}
			}
			
			$this->_redirectCustom($subject);
		}
	}
	
	public function deleteAction()
	{
		$this->_initActions();
		$chapter = $this->_getSubject();
		
		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');

		$this->view->form = $form = new Book_Form_Chapter_Delete();
		
		if (!$this->getRequest()->isPost())
		{
			return;
		}
		
		$work = $chapter->getParent();

		$chapter->delete();

		return $this->_forward('success', 'utility', 'core', array(
			'layout' => 'default-simple',
			'messages' => array(Zend_Registry::get('Zend_Translate')->_('The chapter is deleted successfully.')),
			'parentRedirect' => $work->getHref()
		));
	}
}
