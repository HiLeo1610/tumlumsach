<?php

class Book_Widget_BrowseMenuQuickController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (empty($viewer)) {
			return $this->setNoRender();
		}
	}
}