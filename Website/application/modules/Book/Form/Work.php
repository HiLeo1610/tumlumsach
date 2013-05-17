<?php
class Book_Form_Work extends Engine_Form
{
	private $_workTitle;
	
	public function setWorkTitle($value) {
		$this->_workTitle = $value;
	}
	
	public function isValid($data) {
		$isValid = parent::isValid($data);
		if ($isValid) {
			if ($data['is_long'] !== '1') {
				if (trim($data['content']) == '') {
					$this->addErrorMessage('The content is required. Please input this field.');
					return;
				}
			}
		}
		return $isValid;
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
        $view->headScript()->appendScript('
        	window.addEvent("domready", function() {
        		var showOrHideContent = function() {
        			if (!$("is_long").get("checked")) {
        				$("content-wrapper").set("style", "display:block");
        			} else {
        				$("content-wrapper").set("style", "display:none");
        			}
        		}
        		showOrHideContent();
        		$("is_long").addEvent("click", showOrHideContent);
        	});
        ');	
        	
		
        $this->setMethod('POST')->setTitle($this->_workTitle)->setAttrib('class', 'global_form book_form');

        //init name textfield
        $this->addElement('Text', 'title', array(
            'label' => 'Work name',
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
        		'editor_selector' => 'editor_description',
            	'width' => '850px',
				'height' => '150px',
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
		$this->getElement('description')->setAttrib('class', 'editor_description');
		Engine_Form::addDefaultDecorators($this->getElement('description'));
		
		$this->addElement('Checkbox', 'is_long', array(
			'label' => 'This is a story with many chapters',
			'value' => 1,
			'checked' => false
		));
		
		// init description
        $this->addElement('TinyMce', 'content', array(
            'label' => 'Content',
            'editorOptions' => array(        		
        		'editor_selector' => 'editor_content',
            	'width' => '850px',
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
		$this->getElement('content')->setAttrib('class', 'editor_content');
		Engine_Form::addDefaultDecorators($this->getElement('content'));

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