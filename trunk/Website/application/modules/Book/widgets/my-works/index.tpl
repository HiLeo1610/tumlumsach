<h3>
	<?php 
		$workCount = count($this->works);
		echo $this->translate(array('%1$s work', '%1$s works', $workCount), $workCount);
	?>
</h3>

<ul class="book_work_list book_work_thumbnail_list">
	<?php foreach ($this->works as $work) : ?>
		<li>
			<div class="book_work">
				<div class="book_photo">
					<?php echo $this->itemPhoto($work, 'thumb.profile')?>				
				</div>
				
				<div class="book_info">
					<div class="book_post_title">
						<?php echo $this->htmlLink($work->getHref(), $work->title)?>
					</div>
					<div>
						<span class="book_post_date book_date">
							<?php echo $this->translate('Posted on %1$s', $this->timestamp($work->creation_date)) ?>
						</span>				
						<span class="book_post_stat book_stat">
							|
							<span>
								<?php
									echo $this->translate(array('%s view', '%s views', $work->view_count),
										$this->locale()->toNumber($work->view_count));
								?>
							</span>
							|
							<span>
								<?php
									echo $this->translate(array('%s favorite', '%s favorites', $work->favorite_count),
											$this->locale()->toNumber($work->favorite_count));
								?>
							</span>
						</span>
					</div>
					<div class="book_rate">
						<?php echo $this->partial('_rating_big.tpl', 'book', array('item' => $work));?>
					</div>
				</div>
			</div>	
		</li>
	<?php endforeach; ?>
</ul>