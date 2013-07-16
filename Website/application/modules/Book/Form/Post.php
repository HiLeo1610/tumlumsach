<?php
class Book_Form_Post extends Engine_Form
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
			'label' => 'This post is about a specific book.',
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

		// init photo
		$this->addElement('File', 'photo', array(
	        'label' => 'Post Image',
	        'description' => 'The image should have the size with the portion 200px * 150px',
		));
		$this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		
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

		// init tag
		$this->addElement('Text', 'tags', array(
			'label' => 'Tags (Keywords)',
			'description' => 'Separate tags with commas.',
			'filters' => array(
				'StripTags',
				new Engine_Filter_Censor(),
			),
			'order' => 6
		));
		$this->tags->getDecorator("Description")->setOption("placement", "append");

		// init author autocomplete
		$this->addElement('Text', 'tags_user', array(
			'label' => 'Tag Users',
			'autocomplete' => 'off',
			'filters' => array(
				'StripTags',
				new Engine_Filter_Censor(),
			),
			'order' => 7,
			'description' => 'You can tag your friends here'
		));
		$this->tags_user->getDecorator("Description")->setOption("placement", "append");

		// Init to Values
		$this->addElement('Hidden', 'toValues', array(
			'allowEmpty' => true,
			'order' => 8,
			'filters' => array('HtmlEntities'),
			'required' => false
		));
		Engine_Form::addDefaultDecorators($this->toValues);

		// init author autocomplete
		$this->addElement('Text', 'tags_book', array(
			'label' => 'Tag Books',
			'autocomplete' => 'off',
			'filters' => array(
				'StripTags',
				new Engine_Filter_Censor(),
			),
			'order' => 9,
			'description' => 'You can tag the books that having the similar content with this one'
		));
		$this->tags_book->getDecorator("Description")->setOption("placement", "append");

		// Init to Values
		$this->addElement('Hidden', 'toBookValues', array(
			'allowEmpty' => true,
			'filters' => array('HtmlEntities'),
			'order' => 10,
		));
		Engine_Form::addDefaultDecorators($this->toBookValues);

		$this->addDisplayGroup(array(
			'tags',
			'tags_user',
			'toValues',
			'tags_book',
			'toBookValues'
		), 'tag_group', array('order' => 11));

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