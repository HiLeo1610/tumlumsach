<?php
	echo $this->partial(
		'_autosuggest_post.tpl', 
		'book', 
		array(
			'isPopulated' => $this->isPopulated, 
			'toTaggedUsers' => $this->toTaggedUsers, 
			'toTaggedBooks' => $this->toTaggedBooks,
			'parentBook' => $this->parentBook
		)
	);
?>

<?php
	echo $this->form->render($this);
?>