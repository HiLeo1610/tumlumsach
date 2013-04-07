<?php if (isset($this->thumbnailOnly) && $this->thumbnailOnly == '1') : ?>
	<ul class="book_list">
		<?php foreach ($this->works as $work) : ?>
			<li>
				<div class="work_info">
					<div class="work_photo">
						<?php
							echo $this->htmlLink($work->getHref(), $this->itemPhoto($work, 'thumb.featured', $work->getTitle()), array('title'=>$work->getTitle()))
						?>
					</div>
					<div class="work_brief_info">
						<div>
							<?php
								$owner = $work->getOwner();
								echo $this->translate('Posted on %1$s by %2$s',	$this->timestamp($work->creation_date), $owner);
							?>
						</div>
						<div class="work_stat">
							<span>
								<?php
									echo $this->translate(array('%s view', '%s views', $work->view_count), $this->locale()->toNumber($work->view_count))
								?>
							</span>
							|
							<span>
								<?php
									echo $this->translate(array('%s favorite', '%s favorites', $work->favorite_count), $this->locale()->toNumber($work->favorite_count))
								?>
							</span>
						</div>
						<div class="work_rating_info">
							<?php
								echo $this->partial('_rating_big.tpl', 'book', array('item' => $work));
							?>
							<?php if ($work->rating_count > 0) : ?>
								<span>
									&nbsp;
									<?php
										echo $this->translate(array('(%s rate)', '(%s rates)', $work->rating_count), $this->locale()->toNumber($work->rating_count));
									?>
								</span>
							<?php endif;?>
						</div>
					</div>
				</div>					
				<div class="work_title">
					<a href="<?php echo $work->getHref()?>" title="<?php echo $work->getTitle()?>">
						<?php echo $this->string()->truncate($work->getTitle(), 30)?>
					</a>
				</div>
			</li>
		<?php endforeach; ?>	
	</ul>
	<?php
		$this->headScript()->appendScript("
			window.addEvent('domready', function() {
				$$('.introduction_works ul.book_list > li > .work_info').addEvent('mouseenter', function(event) {
					var elePhoto = this.getFirst('div');
					var eleInfo = this.getLast('div');
					elePhoto.set('tween', {transition: Fx.Transitions.linear, duration:'long'});
					elePhoto.tween('opacity', 0.3);
					eleInfo.setStyle('display', 'block');
				});
				$$('.introduction_works ul.book_list > li > .work_info').addEvent('mouseleave', function(event) {
					var elePhoto = this.getFirst('div');
					var eleInfo = this.getLast('div');
					elePhoto.set('tween', {transition: Fx.Transitions.linear, duration:'long'});
					elePhoto.tween('opacity', 1);
					eleInfo.setStyle('display', 'none');
				});
			});
		");
	?>
<?php else : ?>	
	<?php 
		$this->headScript()
			->appendFile($this->layout()->staticBaseUrl . 'application/modules/Book/externals/scripts/Loop.js')
			->appendFile($this->layout()->staticBaseUrl . 'application/modules/Book/externals/scripts/Tips.js')
	        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Book/externals/scripts/SlideShow.js');
	?>
	        
	<div id="work-slideshow" class="slideshow">
		<?php foreach ($this->works as $work) : ?>
			<div id="<?php echo $work->getGuid()?>" class="book_work">
				<div class="work_photo">
					<?php
						echo $this->htmlLink($work->getHref(), $this->itemPhoto($work, 'thumb.profile', $work->getTitle()), array('title'=>$work->getTitle())) 
					?>
				</div>
				<div class="work_info">
					<div class="work_title">
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
						<?php echo $this->string()->truncate(strip_tags($work->getDescription()), 450)?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
			
		<ul>
			<?php
				$index = 0; 
			?>
			<?php foreach ($this->works as $work) : ?>
				<li>
					<a class="<?php echo ($index == 0)?'current':'' ?> work_navigation" href="#<?php echo $work->getGuid()?>" 
						title="<?php echo $this->string()->stripTags($work->getTitle())?>">
					</a>
				</li>
				<?php
					$index++;
				?>
			<?php endforeach; ?>
		</ul>
	</div>
	
	<script language="javascript" type="text/javascript">
		var navSlideShow;
		document.addEvent('domready', function(){
			// cache the navigation elements
			var navs = $('work-slideshow').getElements('a.work_navigation');
		
			// create a basic slideshow
			navSlideShow = new SlideShow('work-slideshow', {
				selector: 'div.book_work', // only create slides out of the images
				onShow: function(data){
					// update navigation elements' class depending upon the current slide
					navs[data.previous.index].removeClass('current');
					navs[data.next.index].addClass('current');
				}
			});
		
			navs.each(function(item, index){
				// click a nav item ...
				item.addEvent('click', function(event){
					event.stop();
					navSlideShow.show(index);
				});
			});
		
			// tips, for pretty
			new Tips(navs, {
				fixed: true,
				text: '',
				offset: {
					x: -100,
					y: 20
				}
			});
		});
	</script>
<?php endif; ?>