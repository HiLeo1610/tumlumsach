<div class="book_info_detail">
	<?php if (Engine_Api::_()->book()->isFavorite($this->viewer, $this->book)): ?>
		<div class="book_favorite">
			<?php echo $this->translate('This is my favorite book')?>
		</div>
	<?php endif; ?>
	<div class="book_title">
		<span>
			<a href="<?php echo $this->book->getHref()?>">
				<?php echo $this->book->getTitle()?>
			</a>
		</span>
	</div>
	<div class="book_stat">
		<span> 
			<?php
			echo $this->translate(array(
				'%s view',
				'%s views',
				$this->book->view_count
			), $this->locale()->toNumber($this->book->view_count));
			?>
		</span> 
		| 
		<span> 
			<?php
			echo $this->translate(array(
				'%s favorite',
				'%s favorites',
				$this->book->favorite_count
			), $this->locale()->toNumber($this->book->favorite_count));
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
			<label><?php echo $this->translate('Translator')?> </label> 
			<span>
				<?php
				$translators = $this->book->getAuthors(Book_Plugin_Constants::TRANSLATOR);
				echo $this->fluentList($translators);
				?>
			</span>
		</div>
	<?php endif; ?>
	
	<div>
		<label><?php echo $this->translate('Posted by')?></label>
		<span><?php echo $this->user($this->book->user_id)->__toString()?></span>
	</div>
	
	<div>
		<label><?php echo $this->translate('Category')?></label>
		<span><?php echo $this->htmlLink($this->category->getHref(), $this->translate($this->category->category_name)); ?>	</span>
	</div>
	
	<div>
		<label><?php echo $this->translate('Published Date')?></label>
		<span>
			<?php
				if (!empty($this->book->published_date) && !($this->book->published_date == '0000-00-00')) {
					echo $this->locale()->toDate($this->book->published_date);				
				} 
			?>
		</span>
	</div>
	
	<div>
		<label><?php echo $this->translate('Price')?> </label>
		<span>
			<?php 
				echo $this->locale()->toCurrency(
					$this->book->price, 
					Book_Plugin_Constants::CURRENCY_CODE, 
					array('precision' => 0, 'position' => Zend_Currency::RIGHT))
			?>
		</span>
	</div>
	
	<div>
		<label><?php echo $this->translate('Publisher')?></label>
		<span>
			<?php
			$publisher = $this->book->getPublisher();
			if (!empty($publisher))
			{
				echo $publisher->__toString();
			}
			?>
		</span>
	</div>
	
	<div>
		<label><?php echo $this->translate('Book Company')?></label>
		<span>
			<?php
			$company = $this->book->getBookCompany();
			if (!empty($company))
			{
				echo $company;
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
		<span>
			<?php
			 	if (!empty($this->book->num_page)) { 
					echo $this->locale()->toNumber($this->book->num_page);
				}
			?>	
		</span>
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
		'rating_url' => $this->url(array(
			'action' => 'rate',
			'id' => $this->book->getIdentity()
		), 'book_specific', true)
	));
	?>
</div>
<div class="book_description">
	<h4>
		<?php echo $this->translate('Description')?>
	</h4>
	<div>
		<?php echo $this->book->description?>
	</div>
</div>