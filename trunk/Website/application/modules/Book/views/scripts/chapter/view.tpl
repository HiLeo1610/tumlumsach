<div class="book_chapter_header">
	<h2>
		<?php 
			$order = $this->subject->getOrder();
			echo $this->translate("Chapter %s : %s", $order, $this->subject->getTitle());
		?>
	</h2>
	<div class="book_chapter_controls">
		<?php 
			if ($this->work->user_id == $this->viewer->getIdentity() || $this->viewer->isAdmin()) {
				echo $this->htmlLink(
					$this->url(array('action' => 'edit', 'id' => $this->subject->getIdentity()), 'chapter', true),
					$this->translate('Edit'),
					array('class' => 'buttonlink post_edit_icon')
				);		
				echo $this->htmlLink(
					$this->url(array('action' => 'delete', 'id' => $this->subject->getIdentity()), 'chapter', true),
					$this->translate('Delete'),
					array('class' => 'buttonlink post_delete_icon smoothbox')
				);		
			} 
		?>
	</div>	
</div>
<div class="book_clear">
	<span>
		<?php
			echo $this->translate('Posted on %1$s',	$this->timestamp($this->subject->creation_date));
		?>
	</span>
</div>

<div class="book_chapter_info_detail">
	<div class="book_stat">
		<span>
			<?php
				echo $this->translate(
					array('%s view', '%s views', $this->signature->view_count),
					$this->locale()->toNumber($this->signature->view_count))
			?>
		</span>
	</div>
</div>

<div class="book_chapter_thumbnail">
    <?php echo $this->itemPhoto($this->subject)?>
</div>

<div class="book_post_content book_clear">
	<?php echo $this->subject->content?>
</div>
