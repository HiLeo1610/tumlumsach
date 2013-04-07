<?php
class Book_Form_Book_Search extends Zend_Form {
	public function init() {
		$this->setMethod('GET')
			->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'book_general', true));
		$this->addElement('Text', 'text', array(
				'label' => 'Search books',
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