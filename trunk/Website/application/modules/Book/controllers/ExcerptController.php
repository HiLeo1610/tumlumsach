<?php
class Book_ExcerptController extends Book_Controller_Base
{
    protected function _getSubjectType()
    {
        return 'book_excerpt';
    }
    
    public function indexAction()
    {
        
    }
    
    public function createAction()
    {
        if (!$this->_helper->requireUser->isValid())
        {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        // Create form
        $this->view->form = $form = new Book_Form_Excerpt(array('postName' => 'Write an excerpt'));
        if (!$this->getRequest()->isPost())
		{
			$parent_id = $this->_getParam('parent_id');
			$parent_type = $this->_getParam('parent_type');
			if (!empty($parent_id) && !empty($parent_type)) {
				if ($parent_type == 'book') {
					$this->view->parentBook = Engine_Api::_()->getItem('book', $parent_id);
					$form->getElement('hasParent')->setChecked(true);
					$form->getElement('parentBookValue')->setValue($parent_id);
				}				
			} else {
				$form->getElement('hasParent')->setChecked(false);
			}
			return;
		}
		
		if ($form->isValid($this->getRequest()->getPost()))
		{
		    $values = $form->getValues();
		    if ($values['hasParent'] != '1') {
		        unset($values['parentBookValue']);
		    }
		    if (isset($values['parentBookValue'])) {
		        $parent_id = $values['parentBookValue'];
		        $parent_type = 'book';
		    }
		    $postTable = new Book_Model_DbTable_Posts();
		    $notifyApi = Engine_Api::_()->getDbTable('notifications', 'activity');
		    $activityApi = Engine_Api::_()->getDbTable('actions', 'activity');
		    
		    $db = Engine_Db_Table::getDefaultAdapter();
		    $db->beginTransaction();
		    
		    try
		    {
		        $excerpt = $postTable->createRow(array(
		                'content' => $values['content'],
		                'post_name' => $values['post_name'],
		                'parent_type' => $parent_type,
		                'parent_id' => $parent_id,
		                'creation_date' => date('Y-m-d H:i:s'),
		                'modified_date' => date('Y-m-d H:i:s'),
		                'user_id' => $viewer->getIdentity(),
		        ));
		        $excerpt->save();
		    
		        // CREATE AUTH STUFF HERE
		        $auth = Engine_Api::_()->authorization()->context;
		        $roles = array('owner', 'parent_member', 'registered', 'everyone');
		        foreach ($roles as $i => $role) {
		            $auth->setAllowed($excerpt, $role, 'view', true);
		        }
		        	
		        $db->commit();
		    }
		    catch(Exception $e)
		    {
		        $db->rollBack();
		        throw $e;
		    }
		    
		    // add activity
		    if ($excerpt->parent_type == 'book' && !empty($excerpt->parent_id)) {
		        $action = $activityApi->addActivity($viewer, $excerpt, 'post_new_parent');
		        $book = $excerpt->getParent();
		        $actionForBook = $activityApi->addActivity($viewer, $book, 'book_new_post');
		    } else {
		        $action = $activityApi->addActivity($viewer, $excerpt, 'excerpt_new');
		    }
		    if ($action)
		    {
		        $action->attach($excerpt);
		        if (isset($actionForBook)) {
		            $actionForBook->attach($excerpt);
		        }
		    }
		    
		    // add popularity for the book
		    if (isset($book) && !empty($book)) {
		        $popularity = Engine_Api::_()->getItemTable('book_popularity')->getObject($book->getType(), $book->getIdentity());
		        $popularity->post_count = $popularity->post_count + 1;
		        $popularity->point = $popularity->point + Book_Plugin_Core::POST_POINT;
		        $popularity->save();
		    }
		    return $this->_redirectCustom($excerpt);
		}
		
    }
    
    public function viewExcerptAction()
    {
        parent::viewAction();
        
        /*$subject = $this->_getSubject();
         $this->view->postTags = $subject->tags()->getTagMaps();*/
        $this->_helper->content->setEnabled()->setNoRender();
    }
}