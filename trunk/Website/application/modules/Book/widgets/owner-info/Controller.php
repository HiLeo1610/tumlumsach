<?php
class Book_Widget_OwnerInfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
	$this->view->user = $user = $subject->getOwner();
	if (!$user)
	{
		return $this->setNoRender();
	}

    // Get subject and check auth
    if( !$user->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Member type
    $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);

    if( !empty($fieldsByAlias['profile_type']) )
    {
      $optionId = $fieldsByAlias['profile_type']->getValue($user);
      if( $optionId ) {
        $optionObj = Engine_Api::_()->fields()
          ->getFieldsOptions($user)
          ->getRowMatching('option_id', $optionId->value);
        if( $optionObj ) {
          $this->view->memberType = $optionObj->label;
        }
      }
    }

    // Networks
    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($user)
      ->where('hide = ?', 0);
    $this->view->networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);
    
    // Friend count
    $this->view->friendCount = $user->membership()->getMemberCount($user);
  }
}