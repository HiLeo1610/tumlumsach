<?php

class Book_Widget_ListChaptersController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$subject = Engine_Api::_()->core()->getSubject();
		if ($subject->getType() != 'book_work') {
			if ($subject->getType() == 'book_chapter') {
				$this->view->chapter = clone $subject;
			}
			$subject = $subject->getParent();
		}
		
		if ($subject->getType() != 'book_work') {
			return $this->setNoRender();
		}
		
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->workOwner = $workOwner = $subject->getOwner();
		if (!$workOwner->isSelf($viewer)) {
			$chapters = $subject->getChapters(true);
		} else {
			$chapters = $subject->getChapters();
		}
		$this->view->chapters = $chapters;
	}
}