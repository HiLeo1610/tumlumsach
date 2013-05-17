<?php if (isset($this->category)) : ?>
	<h3>
		<a href="<?php echo $this->category->getHref()?>">
			<?php echo $this->category->category_name?>
		</a>
	</h3>
	<?php 
		$bookItemCount = $this->bookPaginator->getTotalItemCount();		
	?>
    <?php if ($bookItemCount > 0) : ?>
        <h4>
            <?php 
                echo $this->translate(array('%1$s book', '%1$s books', $bookItemCount), $bookItemCount);
            ?>
        </h4>
        <ul class="book_list">
            <?php foreach ($this->bookPaginator as $book) : ?>
                <li class="book_item">
                    <?php echo $this->partial('_book.tpl', 'book', array('book' => $book))?>
                </li>
            <?php endforeach; ?>
            
            <li class="book_pages">
                <?php
                	echo $this->paginationControl($this->bookPaginator, null, null, array('query' => $this->params));
                ?>
            </li>
        </ul>
    <?php else : ?>
        <div class="tip">
            <span>
                <?php echo $this->translate('There is no books found.'); ?>
            </span>
        </div>
    <?php endif; ?>
<?php else : ?>
	<h4 class="book_list_total">
		<?php
			echo $this->translate(array('%s book', '%s books', $this->numberOfBooks), $this->numberOfBooks);
		?>
	</h4>
	<ul>	
		<?php foreach($this->categoryPaginator as $category) : ?>
			<li>
				<h3>
					<a href="<?php echo $category->getHref()?>">
						<?php 
							echo $category->category_name;
						?>
					</a>
				</h3>
				<div class="book_list_hd">
					<h4>
						<?php 
							$booksCount = $category->getUsedCount();
							echo $this->translate(array('%1$s book', '%1$s books', $booksCount), $booksCount);
						?>
					</h4>
					<div class="book_list_view_more">
						<a href="<?php echo $category->getHref()?>"><?php echo $this->translate('View More') . ' » ';?></a>
					</div>
					<div class="book_clear"></div>
				</div>
				<ul class="book_list">
					<?php foreach($this->booksByCategory[$category->getIdentity()] as $book) : ?>
						<li class="book_item">
		                    <?php echo $this->partial('_book.tpl', 'book', array('book' => $book))?>
		                </li>		
					<?php endforeach; ?>
				</ul>
				<div class="book_list_view_more">
					<a href="<?php echo $category->getHref()?>"><?php echo $this->translate('View More') . ' » ';?></a>
				</div>
			</li>
		<?php endforeach; ?>
		<li class="book_pages">
            <?php
            	echo $this->paginationControl($this->categoryPaginator, null, null, array('query' => $this->params));
            ?>
        </li>
	</ul>
<?php endif; ?>