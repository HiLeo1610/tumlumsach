<div id="global_wrapper">
	<div id="global_content">
		<div class="introduction_header">
			<div class="introduction_right">
				<?php
					echo $this->action('index', 'widget', 'core', array('name' => 'login'));
				?>
				<div class="introduction_join">
					<?php
						echo $this->htmlLink($this->url(array(), 'user_signup'), $this->translate('REGISTER !'));
					?>
				</div>
			</div>
			
			<div class="introduction_content">
				<?php 
					echo $this->action('index', 'widget', 'core', array('name' => 'introduction-block'));
				?>
			</div>
		
			<div class="layout_core_menu_main">
				<?php
					echo $this->navigation()
					    ->menu()
					    ->setContainer($this->navigation)
					    ->setPartial(null)
					    ->setUlClass('book_welcome_navigation')
					    ->render();
				?>
			</div>
		</div>
		
		<div class="introduction_body">
			<div class="introduction_books">
				<div class="heading"><?php echo $this->translate('Most Popular Books')?></div>
				<?php
					echo $this->action(
						'index', 
						'widget', 
						'core', 
						array(
							'name' => 'book.popular-books', 
							'itemCountPerPage' => 5, 
							'viewInfo' => false
						)
					);
				?>
			</div>
			
			<div class="introduction_works">
				<div class="heading">
					<?php
						echo $this->translate('Newest works'); 
					?>
				</div>
				<?php 
					echo $this->action(
						'index', 
						'widget', 
						'core', 
						array('name' => 'book.newest-works', 'thumbnailOnly' => '1', 'numberOfWorks' => 15)
					);
				?>
			</div>
			
			<div class="introduction_members">
				<div class="heading"><?php echo $this->translate('Members')?></div>
				<div class="introduction_book_companies">
					<div class="heading"><?php echo $this->translate('Authors, Publishers and Book Companies')?></div>	
					<div class="introduction_content">
						<?php
							echo $this->action(
								'index',
								'widget',
								'core',
								array(
									'name' => 'book.list-random-users',
									'levels' => array(
										Book_Plugin_Constants::AUTHOR_LEVEL, 
										Book_Plugin_Constants::PUBLISHER_LEVEL,
										Book_Plugin_Constants::BOOK_COMPANY_LEVEL
									)
								)	
							);
						?>	
					</div>				
				</div>
				
				<div class="introduction_users">
					<div class="heading"><?php echo $this->translate('Readers')?></div>
					<div class="introduction_content">
						<?php
							echo $this->action(
								'index',
								'widget',
								'core',
								array(
									'name' => 'book.list-random-users',
									'levels' => array(
										Book_Plugin_Constants::READER_LEVEL, 
										
									)
								)	
							);
						?>
					</div>
				</div>
				<br />			
			</div>
		</div>
		
		<div class="introduction_footer">
			<?php
				//core.menu-footer
				echo $this->action(
					'index', 
					'widget', 
					'core', 
					array('name' => 'core.menu-footer')
				);
			?>
		</div>
	</div>
</div>