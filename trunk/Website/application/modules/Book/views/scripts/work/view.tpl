<div class="book_work_header">
	<h2><?php echo $this->work->getTitle()?></h2>
	<div class="book_work_controls">
		<?php 
			if ($this->work->user_id == $this->viewer->getIdentity() || $this->viewer->isAdmin()) {
				echo $this->htmlLink(
					$this->url(array('action' => 'edit', 'id' => $this->work->getIdentity()), 'work', true),
					$this->translate('Edit'),
					array('class' => 'buttonlink post_edit_icon')
				);		
				echo $this->htmlLink(
					$this->url(array('action' => 'delete', 'id' => $this->work->getIdentity()), 'work', true),
					$this->translate('Delete'),
					array('class' => 'buttonlink post_delete_icon smoothbox')
				);		
			} 
			if ($this->viewer->getIdentity()) {
				if ($this->work->isUserFavorite($this->viewer)) {
					echo $this->htmlLink(
						$this->url(array('action' => 'remove-favorite', 'id' => $this->work->getIdentity()), 'work', true),
						$this->translate('Remove favorite'),
						array('class' => 'buttonlink work_remove_favorite_icon smoothbox')
					);	
				} else {
					echo $this->htmlLink(
						$this->url(array('action' => 'favorite', 'id' => $this->work->getIdentity()), 'work', true),
						$this->translate('Add to favorite'),
						array('class' => 'buttonlink work_favorite_icon smoothbox')
					);	
				}
			}
		?>
	</div>	
</div>
<div class="book_clear">
	<span>
		<?php
			$owner = $this->work->getOwner();
			echo $this->translate('Posted on %1$s by %2$s',	$this->timestamp($this->work->creation_date), $owner);
		?>
	</span>
</div>

<div class="book_work_info_detail">
	<div class="post_stat">
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
</div>

<?php 
	echo $this->partial('_do_rate.tpl', 'book', array(
		'item' => $this->work, 
		'viewer' => $this->viewer,
		'rated' => $this->rated,
		'rating_url' => $this->url(array('action' => 'rate', 'id' => $this->work->getIdentity()), 'work', true)		
	));
?>

<?php if ($this->viewer->getIdentity()) : ?>
	<div>
		<?php if ($this->work->isUserFavorite($this->viewer)) : ?>
			<span class="work_favorite_icon book_icon">
				<?php echo $this->translate('I am favorite with this work')?>
			</span>
		<?php endif; ?>
	</div>
<?php endif; ?>

<div class="book_clear book_work_photo">
	<?php echo $this->itemPhoto($this->work, NULL) ?>
</div>

<div class="book_post_content book_clear book_work_content">	
	<?php echo $this->string()->stripTags($this->work->description)?>
</div>

<ul class="book_work_chapters">
	<li>
		<?php if ($this->work->isOwner($this->viewer)) : ?>
			<div class="book_work_chapters_control">
				<?php 
					echo $this->htmlLink(
						$this->url(array('action' => 'create', 'work_id' => $this->work->getIdentity()), 'chapter_general', true), 
						$this->translate('Create a new chapter'),
						array('class' => 'buttonlink icon_chapter_new')
					);
				?>
			</div>
		<?php endif; ?>	
	</li>
	<?php
		$chapterCount = $this->paginator->getTotalItemCount();
		$itemCountPerPage = $this->paginator->getItemCountPerPage();
		$page = $this->paginator->getCurrentPageNumber();
	?>
	<?php if (isset($chapterCount) && $chapterCount > 0) : ?>
		<li>
			<h3 class="book_chapter_stat">
				<?php
					echo $this->translate(array('%s chapter', '%s chapters', $chapterCount), $this->locale()->toNumber($chapterCount))
				?>
			</h3>
		</li>
		<?php foreach ($this->paginator as $idx => $chapter) : ?>
			<?php
				$index = ($page - 1) * $itemCountPerPage + $idx;
			?>
			<li class="book_chapter">
				<?php echo $this->partial('_chapter.tpl', 'book', array('idx' => $index, 'chapter' => $chapter)) ?>
			</li>
		<?php endforeach; ?>
		<li class="book_paginator book_pages">
			<?php
				echo $this->paginationControl($this->paginator);
			?>
		</li>
	<?php endif; ?>	
</ul>