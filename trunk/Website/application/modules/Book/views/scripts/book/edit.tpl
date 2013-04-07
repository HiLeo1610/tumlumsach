<?php
	echo $this->partial(
		'_autosuggest_book.tpl', 
		'book',
		array(
			'isPopulated' => $this->isPopulated, 
			'toObjects' => $this->toObjects,
			'toTranslators' => $this->toTranslators
		)
	);
?>
<?php echo $this->form->render($this);?>