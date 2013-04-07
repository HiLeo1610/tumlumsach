<ul class="book_list_users_widget">
	<?php foreach($this->users as $user) : ?>
		<li>			
			<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));?>
			<?php echo $user;?>
		</li>
	<?php endforeach; ?>
</ul>