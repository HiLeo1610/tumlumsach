<ul>
	<?php foreach($this->paginator as $post) : ?>
		<li>
			<?php echo $this->partial('_post.tpl', 'book', array('post' => $post))?>
		</li>
	<?php endforeach; ?>
</ul>