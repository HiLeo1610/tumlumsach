<?php
	$totalItemCount = $this->paginator->getTotalItemCount(); 
?>
<h4>
	<?php 
		echo $this->translate(array('%1$s post', '%1$s posts', $totalItemCount), $totalItemCount);
	?>
</h4>
<ul class="books_list book_posts">
	<?php foreach($this->paginator as $post) : ?>
		<li>
			<div class="book_post_title">
				<?php echo $this->htmlLink($post->getHref(), $post->post_name)?>
			</div>
			<div>
				<span class="book_post_date book_date">
					<?php echo $this->translate('Posted on %1$s', $this->timestamp($post->creation_date)) ?>
				</span>				
				<span class="book_post_stat book_stat">
					|
					<span>
						<?php
							echo $this->translate(array('%s view', '%s views', $post->view_count),
								$this->locale()->toNumber($post->view_count));
						?>
					</span>
					|
					<span>
						<?php
							echo $this->translate(array('%s favorite', '%s favorites', $post->favorite_count),
									$this->locale()->toNumber($post->favorite_count));
						?>
					</span>
				</span>
			</div>
			<div class="book_rate">
				<?php echo $this->partial('_rating_big.tpl', 'book', array('item' => $post));?>
			</div>
			
			<div class="book_briefdescription">
				<?php echo $this->string()->truncate(strip_tags($post->content), 512)?>
			</div>
			
			<?php
				$parent = $post->getParentObject();
			?>
			<?php if ($parent) : ?>
				<div class="book_parent_information">
					<div class="book_post_parent_thumbnail">
						<?php
							echo $this->itemPhoto($parent);
						?>
					</div>
					<div class="book_post_parent_other_information">
						<div class="book_post_parent_title">
							<?php echo $parent;?>
						</div>
						
						<div class="book_post_parent_post_user_date">
							<?php 
								echo $this->translate('Posted by %1$s on %2$s', 
									$parent->getOwner(), 
									$this->timestamp($parent->creation_date));
							?>							
						</div>
						
						<div class="book_post_parent_post_stat">
							<span>
								<?php
									echo $this->translate(array('%s view', '%s views', $parent->view_count),
											$this->locale()->toNumber($parent->view_count));
								?>
							</span>
							|
							<span>
								<?php
									echo $this->translate(array('%s favorite', '%s favorites', $parent->favorite_count),
											$this->locale()->toNumber($parent->favorite_count));
								?>
							</span>
						</div>
						
						<div class="book_post_parent_rating">
							<?php 
								echo $this->partial('_rating_big.tpl', 'book', array('item' => $parent));
							?>
						</div>	
					</div>
				</div>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>	
</ul>
<div class="book_pages">
	<?php 
		echo $this->paginationControl($this->paginator);
	?>
</div>