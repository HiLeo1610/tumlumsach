<?php
	$chapterCount = count($this->chapters);
?>
<ul class="book_chapters">
	<li class="book_chapters_count">
		<?php 
			echo $this->translate(array('%s chapter', '%s chapters', $chapterCount), $this->locale()->toNumber($chapterCount));
		?>
	</li>
	<?php foreach ($this->chapters as $index => $chapter) : ?>
		<?php if (!$chapter->isSelf($this->chapter)) : ?>
			<li class="book_chapter">
				<div class="book_chapter_title">
					<?php 
						echo $this->htmlLink(
							$chapter->getHref(), 
							$this->translate('Chapter %s : %s', $index + 1, $chapter->getTitle()) , 
							array(
								'class' => 'buttonlink item_icon_book_chapter', 
								'title' => $this->string()->stripTags($chapter->getTitle())
							)
						);
					?>
					<?php if (!$chapter->published) :?>
						<span class="book_mark">
							<?php echo $this->translate('Unpublished')?>
						</span>
					<?php endif; ?>	
				</div>
				<div class="book_chapter_date">
					<?php
						echo $this->translate('Posted on %1$s',	$this->timestamp($chapter->creation_date));
					?>
				</div>
				<div class="book_chapter_description">
					<?php
						echo $this->string()->truncate($this->string()->stripTags($chapter->content), 100);
					?>
				</div>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
