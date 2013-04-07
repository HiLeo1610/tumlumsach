<ul class="book_list_small_widget">
	<?php foreach($this->authors as $author) : ?>
		<li>
			<?php 
				echo $this->htmlLink($author->getHref(), $this->itemPhoto($author, 'thumb.icon'), 
				array('class' => 'book-user-photo')); 
			?>
			<div class="book_users">
				<div><?php echo $author;?></div>
				<div class="book_stat">
					<?php 
						echo $this->translate(array('%1$s book', '%1$s books', $author->num_books),$author->num_books);
					?>
				</div>				
			</div>
		</li>
	<?php endforeach; ?>
</ul>