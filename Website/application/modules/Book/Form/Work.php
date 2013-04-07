<?php
class Book_Form_Work extends Engine_Form
{
	private $_workTitle;
	
	public function setWorkTitle($value) {
		$this->_workTitle = $value;
	}
	
    public function init()
    {
    	$view = $this->getView();
		$baseUrl = $view->layout()->staticBaseUrl;
		$view->headScript()
        	->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        	->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        	->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        	->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js');
		
        $this->setMethod('POST')->setTitle($this->_workTitle)
        	->setAttrib('class', 'global_form book_form');

        //init name textfield
        $this->addElement('Text', 'title', array(
            'label' => 'Name',
            'maxlength' => '256',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength( array('max' => '256')),
                /*new Book_Filter_HTMLPurifier()*/
            )
        ));
        
        // is published
        $this->addElement('Checkbox', 'published', array(
            'label' => 'Publish work',
            'value' => 1
        ));

        // init photo
        $this->addElement('File', 'photo', array(
            'label' => 'Book Image',
            'description' => 'The image should have the size with the portion 200px * 150px',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        // init description
        $this->addElement('TinyMce', 'description', array(
            'label' => 'Description',
            'editorOptions' => array(
            	'width' => '800px',
				'height' => '450px',
				'content_css' => '/application/modules/Book/externals/styles/editor.css',
                'theme_advanced_buttons1' => array(
                    'undo',
                    'redo',
                    'cleanup',
                    'removeformat',
                    'pasteword',
                    '|',
                    'media',
                    'image',
                    'fullscreen',
                    'preview',
                    'emotions'
                ),
                'theme_advanced_buttons2' => array(
                    'fontselect',
                    'fontsizeselect',
                    'bold',
                    'italic',
                    'underline',
                    'strikethrough',
                    'forecolor',
                    'backcolor',
                    '|',
                    'justifyleft',
                    'justifycenter',
                    'justifyright',
                    'justifyfull',
                    '|',
                    'outdent',
                    'indent',
                    'blockquote',
                ),
                'upload_url' => Zend_Controller_Front::getInstance()->getRouter()->assemble(
					array('module' => 'book', 'controller' => 'index', 'action' => 'upload'), 'default', true)
            ),
            'allowEmpty' => false,
            'decorators' => array('ViewHelper'),
            'filters' => array(
            	new Engine_Filter_Censor(),
            	new Book_Filter_HTMLPurifier()
			),
        ));
		
		Engine_Form::addDefaultDecorators($this->getElement('description'));

        // Element: submit
        $this->addElement('Button', 'submit', array(
                'label' => 'Post',
                'type' => 'submit',
                'decorators' => array('ViewHelper'),
        ));
		
		$this->addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
		    'link' => true,
		    'prependText' => ' or ',
		    'decorators' => array('ViewHelper'),
		));
		 
		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
		    'decorators' => array('FormElements', 'DivDivDivWrapper'),
		));
    }
}