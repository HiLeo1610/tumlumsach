<?php
class Book_Form_Excerpt extends Engine_Form
{
    private $_postName;
    
    public function setPostName($value)
    {
        $this->_postName = $value;
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

        $view->headScript()->appendScript("
        	window.addEvent('domready', function() {
        		var viewBookFunc = function() {        			
	        		var isAboutBookChk = $('hasParent').get('checked');
	        		if (!isAboutBookChk) {
	        			$('book-wrapper').set('style','display:none');
	        		} else {
	        			$('book-wrapper').set('style','display:block');
	        			$('book').focus();
	        		}	
        		}
        		
        		$('hasParent').addEvent('click', viewBookFunc);
        		
        		viewBookFunc();
        	});
        ");	
        $view->headStyle()->appendStyle('#parentBookValue-wrapper{height:0}');

		$this->setMethod('POST')->setTitle($this->_postName)->setAttrib('class', 'global_form book_form');
		
		$filter = new Engine_Filter_Html();
		$allowed_tags = array_map('trim', explode(',', Book_Plugin_Constants::ALLOWED_HTML_TAGS));
		$filter->setAllowedTags($allowed_tags);

		$this->addElement('Checkbox', 'hasParent', array(
			'label' => 'This excerpt is about a specific book.',
			'value' => 0,
			'order' => 1	
		));
		
		$this->addElement('Text', 'book', array(
			'label' => 'Book',
			'autocomplete' => 'off',
			'order' => 2
		));
		
		$this->addElement('Hidden', 'parentBookValue', array(
            'allowEmpty' => true,
			'order' => 3,
        ));
        Engine_Form::addDefaultDecorators($this->parentBookValue);
		
		$this->addElement('Text', 'post_name', array(
			'label' => 'Title',
			'required' => true,
			'allowEmpty' => false,
			'filters' => array(
				new Engine_Filter_Censor(),
				new Engine_Filter_StringLength( array('max' => '127')),
			),
			'order' => 4
		));

		// init description
		$this->addElement('TinyMce', 'content', array(
			'order' => 5,
			'label' => 'Content',
			'editorOptions' => array(
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
				'width' => '800px',
				'height' => '450px',
				'content_css' => '/application/modules/Book/externals/styles/editor.css',
				'upload_url' => Zend_Controller_Front::getInstance()->getRouter()->assemble(
					array('module' => 'book', 'controller' => 'index', 'action' => 'upload'), 'default', true)
			),
			'required' => true,
			'allowEmpty' => false,
			'filters' => array(
				new Engine_Filter_Censor(),
				$filter,
				new Book_Filter_HTMLPurifier()
			)
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
			'order' => 12
		));
	}
}