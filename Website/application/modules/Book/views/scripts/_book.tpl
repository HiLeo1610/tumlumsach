<div class="book" title="<?php echo $this->book->getTitle()?>">
	<div class="book_photo">
		<a href="<?php echo $this->book->getHref()?>">
			<?php echo $this->itemPhoto($this->book);?>
		</a>
	</div>
	<?php if (!(isset($this->viewInfo) && $this->viewInfo == false)) : ?>
		<div class="book_info">
			<div class="book_title">
				<?php 
					echo $this->htmlLink(
						$this->book->getHref(), 
						$this->string()->truncate($this->book->getTitle(), 30),
						array('title' => $this->book->getTitle())
					);
				?>
			</div>
			<div class="book_author">
				<span class="arrow">&rsaquo;</span>
				<span class="name">
					<?php
						// TODO [DangTH]: this point will be changed to have a better performance
						echo $this->fluentList($this->book->getAuthors());
					?>
				</span>
			</div>
			<div class="book_cover">
				<?php 
					$type = Book_Plugin_CoverType::getBookType($this->book->type);
					if ($type) {
						echo $this->translate($type);
					}
				?>
			</div>
			<div class="book_additional_info">
				<?php if (isset($this->book->view_count)) : ?>
					<span>
						<?php
							echo $this->translate(array('%s view', '%s views', $this->book->view_count),
									$this->locale()->toNumber($this->book->view_count));
						?>
					</span>
					|
				<?php endif;?>
	
				<?php if (isset($this->book->favorite_count)) : ?>
					<span>
					<?php
						echo $this->translate(array('%s favorite', '%s favorites', $this->book->favorite_count),
								$this->locale()->toNumber($this->book->favorite_count));
					?>
					</span>
				<?php endif;?>
			</div>
			<div class="book_rating_info">
				<?php
					echo $this->partial('_rating_big.tpl', 'book', array(
						'item' => $this->book,
					));
				?>
				<?php if ($this->book->rating_count > 0) : ?>
					<span>
						&nbsp;
						<?php
							echo $this->translate(array('(%s rate)', '(%s rates)', $this->book->rating_count),
								$this->locale()->toNumber($this->book->rating_count));
						?>
					</span>
				<?php endif;?>
			</div>
		</div>
	<?php endif; ?>
</div>

