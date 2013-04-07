<div class="book_container">
	<div class="book_photo">
		<?php echo $this->itemPhoto($this->book, 'thumb.big');?>
	</div>
	<div class="book_info_detail">
		<div class="book_title">
			<span><?php echo $this->book->getTitle()?></span>
		</div>
		<div class="book_stat">
			<span>
				<?php
					echo $this->translate(
							array('%s view', '%s views', $this->signature->view_count),
							$this->locale()->toNumber($this->signature->view_count))
				?>
			</span>
			|
			<span>
				<?php
					echo $this->translate(
							array('%s favorite', '%s favorites', $this->signature->favorite_count),
							$this->locale()->toNumber($this->signature->favorite_count))
				?>
			</span>
		</div>
		<div>
			<label><?php echo $this->translate('Author')?></label>
			<span>
				<?php
					echo $this->fluentList($this->authors);
				?>
			</span>
		</div>
		<?php if ($this->book->is_foreign) : ?>
			<div>
				<label><?php echo $this->translate('Translator')?></label>
				<span>
					<?php
						$translators = $this->book->getAuthors(1);
						echo $this->fluentList($translator);
					?>
				</span>
			</div>
		<?php endif;?>
		<div>
			<label><?php echo $this->translate('Posted by')?></label>
			<span><?php echo $this->user($this->book->user_id)->__toString()?></span>
		</div>
		<div>
			<label><?php echo $this->translate('Category')?></label>
			<span><?php echo $this->htmlLink($this->category->getHref(), $this->translate($this->category->category_name));?></span>
		</div>
		<div>
			<label><?php echo $this->translate('Published Date')?></label>
			<span><?php echo $this->locale()->toDate($this->book->published_date) ?></span>
		</div>
		<div>
			<label><?php echo $this->translate('Price')?></label>
			<span><?php echo $this->priceVnd($this->book->price)?></span>
		</div>
		<div>
			<label><?php echo $this->translate('Publisher')?></label>
			<span>
				<?php
					$publisher = $this->book->getPublisher();
					if (!empty($publisher)) {
						echo $publisher->__toString();
					}
				?>
			</span>
		</div>
		<div>
			<label><?php echo $this->translate('Size')?></label>
			<span><?php echo $this->book->size?></span>
		</div>
		<div>
			<label><?php echo $this->translate('Number of page')?></label>
			<span><?php echo $this->locale()->toNumber($this->book->num_page)?></span>
		</div>
		<div>
			<label><?php echo $this->translate('ISB')?></label>
			<span><?php echo $this->book->isbn?></span>
		</div>
		
		<?php 
			echo $this->partial('_do_rate.tpl', 'book', array(
				'item' => $this->book, 
				'viewer' => $this->viewer,
				'rated' => $this->rated,
				'rating_url' => $this->url(array('action' => 'rate', 'id' => $this->book->getIdentity()), 'book_specific', true)		
			));
		?>
	</div>
	
	<div class="book_description">
		<h3><?php echo $this->translate('Description')?></h3>
		<div>
			<?php echo $this->book->description?>
		</div>
	</div>
</div>
