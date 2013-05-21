<?php
class Book_Form_Book_Search extends Zend_Form {
	public function init() {
		$this->setMethod('GET')
			->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'book_general', true))
			->setAttrib('class', 'frm-search');
			
			
		$this->addElement('Text', 'text', array(
				'label' => 'Search books',
				'decorators' => array(
						array('ViewHelper'),
						array('Label')
				)
		));
		$textElement = $this->getElement('text'); 
		$textElement->setAttrib('alt', Zend_Registry::get('Zend_Translate')->_('Which boooks are you looking for ?'));
		$this->addDisplayGroup(
			array('text'), 
			'elements', 
			array(
				'decorators' => array(
					'FormElements', 
					array('HtmlTag', array('class' => 'book-search-wrapper'))
				),
			)
		);
		$this->addElement('Button', 'search', array(
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array(
				array('ViewHelper', array('separator' => '<span class="btn-arrow-icon"></span>', 'placement' => Zend_Form_Decorator_Abstract::PREPEND)),
				array('HtmlTag', array('class' => 'btn-search'))
			),
			'label' => 'Search'
		));
	}
}