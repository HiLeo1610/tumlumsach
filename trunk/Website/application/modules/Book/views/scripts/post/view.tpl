<div class="book_post_header">
	<h3><?php echo $this->subject->getTitle()?></h3>
	<div class="book_post_control">
		<?php 
			if ($this->subject->user_id == $this->viewer->getIdentity() || $this->viewer->isAdmin()) {
				echo $this->htmlLink(
					$this->url(array('action' => 'edit', 'id' => $this->subject->getIdentity()), 'post', true),
					$this->translate('Edit'),
					array('class' => 'buttonlink post_edit_icon')
				);		
				echo $this->htmlLink(
					$this->url(array('action' => 'delete', 'id' => $this->subject->getIdentity()), 'post', true),
					$this->translate('Delete'),
					array('class' => 'buttonlink post_delete_icon smoothbox')
				);		
			} 
		?>
	</div>	
</div>

<div>
	<?php
		$user = $this->user($this->subject->user_id);
	?>
	<span>
		<?php
			echo $this->translate('Posted by %1$s on %2$s',
				$user->__toString(),
				$this->timestamp($this->subject->creation_date));
		?>
	</span>
</div>
<div class="post_info_detail">
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
	
	<?php if (count($this->postTags) > 0) :?>
		<div class="post_tag">
			<span class="post_tag_label">
				<?php echo $this->translate('Tags')?>
			</span>
			<?php
				$tags = array();
				foreach ($this->postTags as $tag) {
					$url = $this->url(array(), 'post_general', true) . '?tag=' . $tag->getTag()->tag_id;
					$html = "<a href='$url'>#" . $tag->getTag()->text . '</a>';
					array_push($tags, $html); 
				}				
			?>
			<span class="post_tags">
				<?php 
					echo $this->fluentList($tags);
				?>
			</span>
		</div>
	<?php endif; ?>
</div>

<?php 
	echo $this->partial('_do_rate.tpl', 'book', array(
		'item' => $this->subject, 
		'viewer' => $this->viewer,
		'rated' => $this->rated,
		'rating_url' => $this->url(array('action' => 'rate', 'id' => $this->subject->getIdentity()), 'post', true)		
	));
?>

<?php
	$taggedUsers = $this->subject->getTaggedUsers();
	$taggedBooks = 	$this->subject->getTaggedBooks();
?>

<?php if (!empty($taggedUsers) && count($taggedUsers) > 0): ?>
	<div class="book_post_tagged_users">
		<div class="book_post_label"><?php echo $this->translate('Tagged Users')?></div>
		<div class="book_post_users">
			<?php foreach ($taggedUsers as $taggedUser) : ?>
				<div class="book_post_user">
					<div>
						<?php
							echo $this->htmlLink(
								$taggedUser->getHref(), 
								$this->itemPhoto($taggedUser, 'thumb.icon'),
								array('title' => $this->string()->stripTags($taggedUser->getTitle()))
							);					
						?>
					</div>
					<div>
						<?php
							echo $this->htmlLink(
								$taggedUser->getHref(), 
								$this->string()->truncate($taggedUser->getTitle(), 15),
								array('title' => $taggedUser->getTitle())
							)
						?>	
					</div>				
				</div>
			<?php endforeach; ?>
		</div>
	</div>	
<?php endif; ?>
<?php if (!empty($taggedBooks) && count($taggedBooks) > 0): ?>
	<div class="book_post_tagged_books">
		<div class="book_post_label"><?php echo $this->translate('Tagged Books')?></div>
		<div class="book_post_books">
			<?php foreach ($taggedBooks as $taggedBook): ?>
				<div class="book_post_book">
					<div>
						<?php
							echo $this->htmlLink(
								$taggedBook, 
								$this->itemPhoto($taggedBook, 'thumb.icon'),
								array('title' => $this->string()->stripTags($taggedBook->getTitle()))
							);					
						?> 
					</div>
					<div>
						<?php
							echo $this->htmlLink(
								$taggedBook->getHref(), 
								$this->string()->truncate($taggedBook->getTitle(), 30),
								array('title' => $taggedBook->getTitle())
							)
						?>	
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>	
<?php endif; ?>

<div class="book_post_content book_clear">
	<?php echo $this->subject->content?>
</div>

<script language="javascript" type="text/javascript">
	en4.core.runonce.add(function(){
		var eles = $$('.generic_layout_container.layout_left');
		if (eles.length == 1) {
			var ele = eles[0];
			if (ele.get('html').trim() == '') {
				ele.destroy();
			}
		}
	});
</script>
