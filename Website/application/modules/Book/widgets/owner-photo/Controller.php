<?php
class Book_Widget_OwnerPhotoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$subject = Engine_Api::_()->core()->getSubject();
		$user = $subject->getOwner();
		if (!$user)
		{
			return $this->setNoRender();
		}
		$this->view->user = $user;
	}

}
