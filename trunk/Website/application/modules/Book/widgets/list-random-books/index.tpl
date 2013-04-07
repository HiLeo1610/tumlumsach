<ul class="book_list_small_widget">
	<?php foreach($this->books as $book) : ?>
		<li>
			<?php 
				echo $this->htmlLink($book->getHref(), $this->itemPhoto($book, 'thumb.icon'), array('class' => 'book-photo')); 
			?>
			<div class="book_book">
				<div><?php echo $book;?></div>
				<div class="book_stat">
					<span>
						<?php
							echo $this->translate(array('%s view', '%s views', $book->view_count),
								$this->locale()->toNumber($book->view_count));
						?>
					</span>
					|
					<span>
						<?php
							echo $this->translate(array('%s favorite', '%s favorites', $book->favorite_count),
									$this->locale()->toNumber($book->favorite_count));
						?>
					</span>
				</div>				
				<div class="book_date">
					<?php echo $this->translate('Posted on %1$s', $this->timestamp($book->creation_date)) ?>
				</div>
				<div class="book_rate">
					<?php echo $this->partial('_rating_big.tpl', 'book', array('item' => $book));?>
				</div>	
				<div class="book_clear"></div>
				<div class="book_stat">
					<?php echo $this->translate(array('%s rating', '%s ratings', $book->rating_count), $book->rating_count)?>
				</div>			
			</div>
		</li>
	<?php endforeach; ?>
</ul>