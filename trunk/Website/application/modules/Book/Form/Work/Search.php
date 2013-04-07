<?php
class Book_Form_Work_Search extends Zend_Form {
	public function init() {
		$this->setMethod('GET')
			->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'work_general', true));
		$this->addElement('Text', 'text', array(
			'label' => 'Search works',
			'decorators' => array(
				array('ViewHelper'),
				array('Label')
			)
		));
		
		$this->addElement('Text', 'author', array(
			'label' => 'Author\'s name',
			'decorators' => array(
				array('ViewHelper'),
				array('Label')
			)
		));

		$this->addElement('Button', 'search', array(
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array(
				array('ViewHelper')
			),
			'label' => 'Search'
		));
	}
}