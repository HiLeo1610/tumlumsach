<?php
class Book_Form_Post_Search extends Zend_Form {
	public function init() {
        $this->setMethod('GET')
        	->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'post_general', true));
        
        $this->addElement('Text', 'text', array(
            'label' => 'Search posts',
            'decorators' => array(
				array('ViewHelper'),
				array('Label')
			)
        ));

        // $this->addElement('Text', 'book', array(
            // 'label' => 'Book',
        // ));
		
        // Buttons
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
				array('ViewHelper')
			),
        ));
    }
}