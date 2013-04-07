<?php 
	echo $this->partial('_list_books_widget.tpl', 'book', array(
		'paginator' => $this->paginator,
		'identity' => $this->identity
	))
?>