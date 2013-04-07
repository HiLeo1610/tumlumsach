<?php
class Book_Form_Book_Delete extends Engine_Form {

    public function init() {
        $this->setTitle('Delete Book')
                ->setDescription('Are you sure you want to delete this book?')
                ->setMethod('POST');

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Delete',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
		
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }
}