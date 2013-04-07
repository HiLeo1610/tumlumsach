<h3><?php echo $this->translate('Publishers and Book Companies')?></h3>
<?php
	$publisherCount = $this->publishers->count();
?>
<?php if ($publisherCount > 0) :?>
	<h4><?php echo $this->translate('Publisher')?></h4>	
	<div class="book_clear">
		<?php 
	        echo $this->translate(array('%1$s publisher', '%1$s publishers', $publisherCount), $publisherCount);
	    ?>
	    <div class="book_block_area">
			<ul class="book_list_publishers">
				<?php foreach($this->publishers as $publisher) : ?>
					<li>
						<?php echo $this->itemPhoto($publisher, 'thumb.icon')?>
						<div>
							<?php 
								echo $this->htmlLink($publisher->getHref(), 
									$this->string()->truncate($publisher->getTitle(), 25),
									array('title' => $publisher->getTitle()) 
								);
							?>
						</div>
					</li>
				<?php endforeach; ?>		
			</ul>
		</div>
	</div>
<?php endif; ?>

<?php
	$bookCompanyCount = $this->bookCompanies->count();
?>
<?php if ($bookCompanyCount > 0) :?>
	<div class="book_clear">
		<h4><?php echo $this->translate('Book Company')?></h4>
	    <div class="book_block_area">
			<ul class="book_list_publishers">
				<?php foreach($this->bookCompanies as $bookCompany) : ?>
					<li>
						<?php echo $this->itemPhoto($bookCompany, 'thumb.icon')?>
						<div>
							<?php
								echo $this->htmlLink($bookCompany->getHref(), 
									$this->string()->truncate($bookCompany->getTitle(), 25),
									array('title' => $bookCompany->getTitle())
								);
							?>
						</div>
					</li>
				<?php endforeach; ?>		
			</ul>
		</div>
	</div>
<?php endif; ?>