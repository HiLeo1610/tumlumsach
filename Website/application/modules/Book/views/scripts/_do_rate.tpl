<script type="text/javascript">
	en4.core.runonce.add(function() {
		en4.book.rate('<?php echo $this->item->getGuid()?>', {
			rating : '<?php echo $this->item->rating?>',
			rated : '<?php echo $this->rated?>',
			total_votes : '<?php echo $this->item->rating_count?>',
			viewer : '<?php echo $this->viewer->getIdentity()?>',
			url : '<?php echo $this->rating_url?>'
		});
	});
</script>

<div id="<?php echo $this->item->getGuid()?>_rating" class="book_rating_stars">
	<span id="<?php echo $this->item->getGuid()?>_rate_1" 
		class="book_rating_star_big book_rating_star_big_generic">		 
	</span>

	<span id="<?php echo $this->item->getGuid()?>_rate_2" 
		class="book_rating_star_big book_rating_star_big_generic"> 
	</span>

	<span id="<?php echo $this->item->getGuid()?>_rate_3" 
		class="book_rating_star_big book_rating_star_big_generic">
	</span>

	<span id="<?php echo $this->item->getGuid()?>_rate_4" 
		class="book_rating_star_big book_rating_star_big_generic">
	</span>

	<span id="<?php echo $this->item->getGuid()?>_rate_5" 
		class="book_rating_star_big book_rating_star_big_generic">
	</span>

	<span id="<?php echo $this->item->getGuid()?>_rating_text" 
		class="rating_text book_rating_text"><?php echo $this->translate('click to rate'); ?>
	</span>
</div>