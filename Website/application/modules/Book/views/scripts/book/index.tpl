<h2>
	<?php echo $this->translate('Books')?>
	<?php if (isset($this->category)) : ?>
		&nbsp;<?php echo $this->translate($this->category->category_name)?>
		<?php if (isset($this->subCategory)) : ?>
			&nbsp;<?php echo $this->translate($this->subCategory->category_name)?>
		<?php endif;?>
	<?php endif;?>
</h2>

<form method="get" action="<?php echo $this->url(array('action' => 'create-book'), 'book_general', true)?>">
	<button type="submit"><?php echo $this->translate('Post a book')?></button>
</form>

<?php
	$totalCount = $this->paginator->getTotalItemCount();
?>

<?php if ($totalCount > 0): ?>
	<div class="book-list-block">
		<h3>
			<?php
	
				echo $this->translate(array('%s book', '%s books', $totalCount),
					$this->locale()->toNumber($totalCount));
			?>
		</h3>
	
		<ul class="book_list">
			<?php foreach($this->paginator as $book) : ?>
				<li class="book_item"><?php echo $this->partial('_book.tpl', 'book', array('book' => $book));?></li>
			<?php endforeach;?>
	
			<li class="book_pages">
				<?php echo $this->paginationControl($this->paginator);?>
			</li>
		</ul>
	</div>
<?php else : ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There is no books found.'); ?>
		</span>
	</div>
<?php endif;?>