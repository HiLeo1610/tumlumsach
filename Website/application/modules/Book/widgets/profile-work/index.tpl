<div class='book_work_title'>
	<h2>
		<?php
			echo $this->htmlLink(
				$this->subject->getHref(), 
				$this->subject->getTitle(), 
				array('title' => $this->string()->stripTags($this->subject->getTitle()))
			) 
		?>
	</h2>
</div>	
<div class='book_work_photo'>
  <?php echo $this->itemPhoto($this->subject) ?>
</div>
<div class="book_work_info">
	<?php
		echo $this->translate(
			'Posted on %1$s by %2$s', 
			$this->timestamp($this->subject->creation_date), 
			$this->subject->getOwner()
		);
	?>
</div>
