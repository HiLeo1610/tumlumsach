<?php
class Book_Form_Chapter extends Engine_Form
{
	private $_chapterTitle;
	
	public function setChapterTitle($value) {
		$this->_chapterTitle = $value;
	}
	
    public function init()
    {
    	$view = $this->getView();
		$baseUrl = $view->layout()->staticBaseUrl;
		
        $this->setMethod('POST')->setTitle($this->_chapterTitle)
        	->setAttrib('class', 'global_form book_form');

        //init name textfield
        $this->addElement('Text', 'title', array(
            'label' => 'Name',
            'maxlength' => '256',
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength( array('max' => '256')),
            )
        ));
        
        // is published
        $this->addElement('Checkbox', 'published', array(
            'label' => 'Publish chapter',
            'value' => 1
        ));

        // init description
        $this->addElement('TinyMce', 'content', array(
            'label' => 'Content',
            'editorOptions' => array(
            	'width' => '900px',
				'height' => '800px',
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