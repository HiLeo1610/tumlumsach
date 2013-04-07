<?php
class Book_Form_Book extends Engine_Form
{
	private $_bookTitle;
	
	public function setBookTitle($value) {
		$this->_bookTitle = $value;
	}
	
    public function init()
    {
    	$view = $this->getView();
		
		$baseUrl = $view->layout()->staticBaseUrl;
		$view->headScript()
        	->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        	->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        	->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        	->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js')
			->appendScript("
			window.addEvent('domready', function() {
				en4.book.onCreateEditBook();				
			});
		");
		
        $this->setMethod('POST')->setTitle($this->_bookTitle)
        	->setAttrib('class', 'global_form book_form');

        //init name textfield
        $this->addElement('Text', 'book_name', array(
            'label' => 'Name',
            'maxlength' => '256',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength( array('max' => '256')),
            )
        ));
		
		// init photo
        $this->addElement('File', 'photo', array(
            'label' => 'Book Image',
            'description' => 'The image should have the size (140px * 230px)',
            'required' => true
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		
		$this->addDisplayGroup(array('book_name', 'photo'), 'basic_information');
		$this->basic_information->setLegend('Basic information (required)');
        
        // init author autocomplete
        $this->addElement('Text', 'authors', array(
            'label' => 'Author',
            'autocomplete' => 'off',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
		
		// Init to Values
        $this->addElement('Hidden', 'toValues', array(
            'allowEmpty' => true,
            'order' => 3,
            'filters' => array('HtmlEntities'),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);
		
		// Is foreigner
        $this->addElement('Checkbox', 'is_foreign', array(
            'label' => 'Is Foreigner',
            'value' => '0'
        ));

		// init author autocomplete
        $this->addElement('Text', 'translators', array(
            'label' => 'Translator',
            'autocomplete' => 'off',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        ));
		
		// Init to Values
        $this->addElement('Hidden', 'toTranslatorValues', array(
            'allowEmpty' => true,
            'order' => 4,
            'filters' => array('HtmlEntities'),
        ));
        Engine_Form::addDefaultDecorators($this->toTranslatorValues);

        // int category and sub category
        $categoriesOptions = Engine_Api::_()->getDbTable('categories', 'book')->getMultiOptions();

        $categoryElement = new Engine_Form_Element_Select('category_id', array(
            'label' => 'Category',
            'multiOptions' => $categoriesOptions,
        ));
        $this->addElement($categoryElement);

        // Published date
        $publishedDate = new Engine_Form_Element_Birthdate('published_date', array('yearMax' => intval(date("Y")) + 1));
        $publishedDate->setLabel("Published Date");
        $publishedDate->setAllowEmpty(true);
        $this->addElement($publishedDate);
        
        // Price
        $this->addElement('Text', 'price', array(
            'label' => 'Price',                
            'description' => Book_Plugin_Constants::CURRENCY_CODE,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
            'filters' => array(
            	new Book_Filter_HTMLPurifier()
			)                     
        ));
        
        // Size
        $this->addElement('Text', 'size', array(
            'label' => 'Size',    
            'maxlength' => '256',
            'filters' => array(
                new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength( array('max' => '64')),
            )
        ));
        
        // Publisher
        $this->addElement('Select', 'publisher_id', array(
            'label' => 'Publisher',
            'multiOptions' => array_merge(array('0' => ''), Book_Plugin_Utilities::getPublishers())  
        ));
		
		// Book company
        $this->addElement('Select', 'book_company_id', array(
            'label' => 'Book Company',
            'multiOptions' => array_merge(array('0' => ''), Book_Plugin_Utilities::getBookCompanies())  
        ));
        
        // Type
        $this->addElement('Select', 'type', array(
            'label' => 'Cover Type',
            'multiOptions' => array_merge(array('0' => ''), Book_Plugin_CoverType::getAllBookTypes('id')) 
        ));
		
        // Number of page
        $this->addElement('Text', 'num_page', array(
            'label' => 'Number of page',
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
            'filters' => array(new Book_Filter_Null()),
        ));
        
        // ISBN
        $this->addElement('Text', 'isbn', array(
            'label' => 'ISBN',
            'maxlength' => '32',
            'filters' => array(
                    new Engine_Filter_HtmlSpecialChars(),
                    'StripTags',
                    new Engine_Filter_Censor(),
                    new Engine_Filter_StringLength( array('max' => '32')),
            )
        ));
        
		$this->addDisplayGroup(array('authors', 'is_foreign', 'translators', 'category_id', 'published_date', 'price', 'size', 'publisher_id', 'book_company_id', 'type', 'num_page', 'isbn'), 'advanced_information');
		$this->advanced_information->setLegend(Zend_Registry::get('Zend_Translate')->_('Advanced information') 
			. ' <span id="book_arrow_control" class="book_arrow book_view_less">&nbsp;</span>');
		$this->advanced_information->removeDecorator('Fieldset');
		$this->advanced_information->addDecorator(new Book_Form_Decorator_Fieldset());
		$view->headScript()->appendScript("
			window.addEvent('domready', function() {
				$('advanced_information-wrapper').setStyle('display','none');			
				
				$('fieldset-advanced_information').getElement('legend').removeEvent('click').addEvent('click', function() {
					$(this).getElement('span').toggleClass('book_view_less');
					$(this).getElement('span').toggleClass('book_view_more');
					if ($(this).getElement('span').hasClass('book_view_less')) {
						$('advanced_information-wrapper').setStyle('display','none');	
					} else {
						$('advanced_information-wrapper').setStyle('display','block');
					}				
				});	
			});
		");
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