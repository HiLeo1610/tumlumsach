<div class="book_chapter_title">
	<?php
		echo $this->htmlLink(
			$this->chapter->getHref(), 
			$this->translate('Chapter %d : %s', $this->idx + 1, $this->chapter->title?$this->chapter->title:''), 
			array('title' => $this->string()->stripTags($this->chapter->title))
		);
	?>
	<?php if (!$this->chapter->published) : ?>
		<span class="book_mark"><?php echo $this->translate('Unpublished')?></span>
	<?php endif; ?>
</div>	
<div class="book_chapter_controls">
	<?php
		if ($this->chapter->authorization()->isAllowed($this->viewer, 'edit')) {
			echo $this->htmlLink(
				$this->chapter->getHref(array('action' => 'edit')), 
				$this->translate('Edit'), 
				array('class' => 'buttonlink post_edit_icon smoothbox')
			);
		}
	?>
	<?php
		if ($this->chapter->authorization()->isAllowed($this->viewer, 'delete')) {
			echo $this->htmlLink(
				$this->chapter->getHref(array('action' => 'delete')), 
				$this->translate('Delete'),
				array('class' => 'buttonlink post_delete_icon smoothbox')
			);
		}
	?>
</div>
<div class="book_chapter_created_date book_stat book_clear">
	<?php
		echo $this->timestamp($this->chapter->creation_date);
	?>
</div>
<div class="book_chapter_content">
	<?php
		echo $this->string()->truncate($this->string()->stripTags($this->chapter->content), 255);
	?>
</div>