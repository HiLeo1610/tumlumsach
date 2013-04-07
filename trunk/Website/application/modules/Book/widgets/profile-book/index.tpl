<?php
	$data = array(
		'book' => $this->book,
		'viewer' => $this->viewer,
		'authors' => $this->authors,
		'category' => $this->category,
		'rated' => $this->rated,
	);

	if (isset($this->translators)) {
		$data['translators'] = $this->translator;
	}
	echo $this->partial('_profile_book.tpl', 'book', $data);
?>

<div class="book_comments">
	<?php
		echo $this->action(
			'index', 
			'widget', 
			'core', 
			array(
				'name' => 'core.comments', 
				'subject' => $this->book->getGuid()
			)
		);
	?>
</div>