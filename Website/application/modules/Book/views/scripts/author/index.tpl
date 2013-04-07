<?php
	$totalItemCount = $this->paginator->getTotalItemCount();	
?>
<h3><?php echo $this->translate('Authors')?></h3>
<?php if ($totalItemCount > 0) :?>
	<?php 
        echo $this->translate(array('%1$s author', '%1$s authors', $totalItemCount), $totalItemCount);
    ?>
    <div class="book_block_area book_clear">
		<ul class="book_list_authors">
			<?php foreach($this->paginator as $author) : ?>
				<li>
					<?php echo $this->itemPhoto($author, 'thumb.icon')?>
					<div>
						<?php
							echo $this->htmlLink(
								$author->getHref(), 
								$this->string()->truncate($author->getTitle(), 25),
								array(
									'title' => $author->getTitle()
								)
							) 
						?>
					</div>
				</li>
			<?php endforeach; ?>
			<li>		
				<?php
					echo $this->paginationControl($this->paginator);
				?>
			</li>
		</ul>
	</div>
<?php endif; ?>