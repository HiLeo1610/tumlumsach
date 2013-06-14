<?php
	if (isset($this->parentBook)) {
		echo $this->partial('_autosuggest_post.tpl', 'book', array('parentBook' => $this->parentBook));
	} else {
		echo $this->partial('_autosuggest_post.tpl', 'book');
	}
?>

<?php
	echo $this->form->render($this);
?>