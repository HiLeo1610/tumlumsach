<div class="book_post">
	<div class="book_post_icon_title_wrapper">
		<div class="book_post_img book_icons"></div>
		<div class="book_post_title">
			<?php echo $this->htmlLink($this->post->getHref(), $this->post->getTitle());?>
		</div>		
		<div class="book_clear"></div>
	</div>
	<div class="book_post_time">
		<?php 
			echo $this->translate('Posted by %1$s on %2$s', 
				$this->htmlLink($this->post->getOwner(), htmlspecialchars ($this->post->getOwner()->getTitle())),  
				$this->timestamp($this->post->creation_date));?>
	</div>
	<div class="book_post_rating">
		<?php echo $this->partial('_rating_big.tpl', 'book', array('item' => $this->post))?>
	</div>
	<div class="book_post_briefdescription">
		<?php echo $this->string()->truncate($this->string()->stripTags($this->post->content), 200)?>
	</div>
</div>