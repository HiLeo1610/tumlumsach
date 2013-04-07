<?php
	echo $this->partial('_profile_book.tpl', 'book', 
		array(
			'book' => $this->book, 
			'category' => $this->category, 
			'viewer' => $this->viewer,
			'rated' => $this->rated
		)
	);
?>