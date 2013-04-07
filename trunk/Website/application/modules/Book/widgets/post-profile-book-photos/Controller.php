<?php

class Book_Widget_PostProfileBookPhotosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$post = Engine_Api::_()->core()->getSubject();

		if (!empty($post->parent_type) && !empty($post->parent_id)) {
			if ($post->parent_type == 'book') {
				$this->view->book = $post->getParentObject();
				return;
			}
		}

		return $this->setNoRender();
	}
}
