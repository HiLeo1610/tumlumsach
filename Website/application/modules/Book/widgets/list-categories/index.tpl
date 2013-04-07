<h3>
	<?php echo $this->translate('Categories') ?>
</h3>
<ul class="generic_list_widget">
	<?php foreach($this->categories as $category) : ?>
		<li>
			<?php echo $this->htmlLink($category->getHref(), $category->category_name)?>
		</li>
	<?php endforeach;?>
</ul>
