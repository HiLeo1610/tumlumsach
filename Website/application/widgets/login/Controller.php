<?php
class Widget_LoginController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$form = $this -> view -> form = new User_Form_Login2();
		$form->setTitle(null)->setDescription(null);
		$form->removeDisplayGroup('buttons');
	}
}
