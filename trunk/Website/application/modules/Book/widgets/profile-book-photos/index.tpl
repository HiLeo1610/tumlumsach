<?php
	echo $this->partial('_profile_book_photos.tpl', 'book', array('book' => $this->book));
?>
<!--
<?php if ($this->viewer->getIdentity()) : ?>
	<div class="book_block_area">
		<a href="<?php 
			echo $this->url(array('action' => 'upload-photo', 'id' => $this->book->getIdentity(), 'format' => 'smoothbox'), 
				'book_specific')?>" class="buttonlink smoothbox book_upload_icon">
			<?php echo $this->translate('Upload photo')?>
		</a>
	</div>
<?php endif; ?>
-->