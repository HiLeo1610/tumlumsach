<?php
	echo $this->partial(
		'_autosuggest_post.tpl', 
		'book', 
		array(
			'isPopulated' => $this->isPopulated, 
			'toTaggedUsers' => $this->toTaggedUsers, 
			'toTaggedBooks' => $this->toTaggedBooks
		)
	);
?>

<?php
	echo $this->form->render($this);
?>