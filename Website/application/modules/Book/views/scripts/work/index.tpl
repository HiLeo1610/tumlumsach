<?php
	$totalItemCount = $this->paginator->getTotalItemCount(); 
?>
<div class="book_work_header">
	<!--<h4>
		<?php 
			echo $this->translate(array('%1$s work', '%1$s works', $totalItemCount), $totalItemCount);
		?>
	</h4>-->
	<div class="book_work_controls">
		<?php if ($this->viewer->getIdentity()) : ?>
			<?php 
				echo $this->htmlLink(
					$this->url(array('action' => 'create'), 'work_general', true), 
					$this->translate('Create a new work'),
					array('class' => 'buttonlink icon_work_new')
				) 
			?>
		<?php endif; ?>
	</div>	
</div>
<ul class="books_list book_works book_clear">
	<?php foreach($this->paginator as $work) : ?>
		<li>
			<div class="book_photo">
				<?php 
					echo $this->htmlLink($work->getHref(), $this->itemPhoto($work, 'thumb.profile', $work->getTitle()), array('title'=>$work->getTitle())) 
				?>
			</div>
			
			<div class="book_info">
				<div class="book_post_title">
					<?php echo $this->htmlLink($work->getHref(), $work->title)?>
				</div>
				<div class="book_post_author">
					<?php
						$user = $work->getParent();
						if ($user && $user->getIdentity()) {
							echo $this->translate('Posted by %s', $user);
						}
					?>
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
				<div class="book_briefdescription">
					<?php echo $this->string()->truncate(strip_tags($work->getDescription()), 512)?>
				</div>
				<div class="book_work_chapters">
					<div>
						<?php
							$chapters = $work->getChapters(); 
							echo $this->translate(array('%1$s chapter', '%1$s chapters', count($chapters)), count($chapters));
						?>
					</div>
					<?php if (!empty($chapters) && (count($chapters) > 0)) : ?>
						<ul class="book_work_chapters_list">
							<?php foreach ($chapters as $idx => $chapter) : ?>
								<li>
									<?php
										$order = $idx + 1;
									?>
									<span>
										<a href="<?php echo $chapter->getHref()?>" title="<?php echo $chapter->title?>"> 
											<?php
												echo sprintf(
													Zend_Registry::get('Zend_Translate')->translate('Chapter %d : %s'), 
													$order, 
													$this->string()->truncate($chapter->title, 128)
												);
											?>
										</a>
									</span>
									<span class="book_chapter_datetime">
										(<?php echo $this->timestamp($chapter->creation_date)?>)
									</span>
								</li>
							<?php endforeach; ?>				
						</ul>
					<?php endif; ?>
				</div>
			</div>			
		</li>
	<?php endforeach; ?>	
</ul>
<div class="book_pages">
	<?php 
		echo $this->paginationControl($this->paginator);
	?>
</div>