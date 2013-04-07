<?php
class Book_Form_Book_UploadPhoto extends Engine_Form
{
	public function init()
	{
		$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
			'action' => 'upload-photo', 
			'module' => 'book',
		    'controller' => 'book'
		)))->setAttribs(array('enctype' => 'multipart/form-data'));

		$this->addElement('file', 'photo', array(
			'label'       => 'Book Photo',
			'description' => 'Choose an image from your computer'
		));
		
		$this->getElement('photo')->removeDecorator('HtmlTag');

		$this->addElement('Button', 'submit', array(
            'label' => 'Upload',
            'type' => 'submit',
        ));      
        
        $this->getElement('submit')->removeDecorator('DtDdWrapper')->removeDecorator('HtmlTag'); 
	}
}