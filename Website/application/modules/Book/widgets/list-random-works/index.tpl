<ul class="book_work_list">
	<?php foreach ($this->works as $work) : ?>
		<?php
			$owner = $work->getOwner();
		?>
		<li class="book_work">
			<div class="book_work_photo">
				<?php
					echo $this->htmlLink($work->getHref(), $this->itemPhoto($work, 'thumb.icon', $work->getTitle()), array('title'=>$work->getTitle()))
				?>
			</div>
			<div class="book_work_info">
				<div class="book_work_title">
					<?php 
						echo $this->htmlLink(
							$work->getHref(), 
							$this->string()->truncate(
								$this->string()->stripTags($work->getTitle()), 
								20
							)
						);
					?>
				</div>
				<div class="book_work_stat">
					<?php
						echo $this->translate('Posted on %s by %s', $this->timestamp($work->creation_date), $owner); 
					?>
				</div>
				<?php
					echo $this->partial('_rating_big.tpl', 'book', array(
						'item' => $work,
					));
				?>
			</div>
		</li>
	<?php endforeach; ?>
</ul>