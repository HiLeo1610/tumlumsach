<form method="get" action="<?php echo $this->url(array(
		'action' => 'create',
		'parent_type' => $this->book->getType(),
		'parent_id' => $this->book->getIdentity()), 'post')?>">
	<button type="submit">
		<?php echo $this->translate('Write New Post')?>
	</button>
</form>

<ul id="book_readers_feelings" class="book_feelings_browse">
	<?php foreach ($this->paginator as $index => $item): ?>
		<li>
			<div class="book_user_feeling">
				<div class="book_user_photo">
					<?php if (!empty($this->users[$index])) : ?>
						<div>
							<?php 
								echo $this->htmlLink($this->users[$index]->getHref(), $this->itemPhoto($this->users[$index], 'thumb.icon'));
							?>
						</div>
						<div>
							<?php 
								echo $this->users[$index]->__toString();
							?>
						</div>
					<?php endif;?>
				</div>
				<div class="book_brief_content">
					<div class="book_feeling_title">
						<?php
							echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->post_name, 40),
								array('title' => $item->post_name))
						?>
					</div>
					<div class="book_feeling_stat">
						<?php
							echo $this->translate('Posted at %s', $this->timestamp($item->creation_date));
						?>
					</div>
					<div>
						<?php echo $this->string()->truncate($this->string()->stripTags($item->content))?>
					</div>
					<div>
						<?php echo $this->htmlLink($item->getHref(), $this->translate('View More'))?>
					</div>
				</div>
				<div class="book_clear_both"></div>
			</div>
		</li>
	<?php endforeach;?>
</ul>

<div class="book_clear"></div>
