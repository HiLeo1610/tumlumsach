<ul class="book_manage_photos">
	<?php foreach ($this->photos as $photoItem) : ?>
		<?php
			$storageApi = Engine_Api::_()->storage();
			$photo = $storageApi->get($photoItem->file_id, 'thumb.profile');
		?>
		<?php if (!empty($photo)) : ?>
			<li class="book_manage_photo">
				<img src="<?php echo $photo->getHref()?>" />
				<div class="book_manage_photos_control">
					<?php if (!$photoItem->default) : ?>
						<span><a href="javascript:void(0)" class="book_photo_delete_link" photo_id="<?php echo $photoItem->getIdentity()?>"><?php echo $this->translate('delete')?></a></span>
					<?php endif; ?>
					
					<span>
						<?php if (!$photoItem->default) : ?>
							<a href="javascript:void(0)" class="book_photo_set_default_link" photo_id="<?php echo $photoItem->getIdentity()?>"><?php echo $this->translate('set default')?></a>
						<?php else : ?>
							<a href="javascript:void(0)" class="book_photo_default" photo_id="<?php echo $photoItem->getIdentity()?>"><?php echo $this->translate('default')?></a>
						<?php endif; ?>	
					</span>
				</div>
			</li>			
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
<div class="book_clear"></div>

<script language="javascript" type="text/javascript">
	en4.book.setDefaultPhoto = function(event) {
		var photoId = $(this).getProperty('photo_id');
		var ele = this;
		if (photoId) {
			var reqSetDefault = new Request.JSON({
				url : '<?php
					echo $this->url(
						array(
							'action' => 'set-default-photo',
							'id' => $this->book->getIdentity(),
							'format' => 'json'
						),
						'book_specific'
					); 
				?>',
				data : {
					'photo_id' : photoId
				},
				onSuccess : function(res, responseText) {
					if (res && res.status == 1) {
						var defaultEles = $$('.book_photo_default');
						if (defaultEles.length > 0) {
							var defaultEle = defaultEles[0];
							$(defaultEle).removeClass('book_photo_default')
								.addClass('book_photo_set_default_link')
								.addEvent('click', en4.book.setDefaultPhoto)
								.set('text', '<?php echo $this->translate('set default')?>');
							var spanDeleteEle = new Element('span');
							var linkDeleteEle = new Element('a', {
								'href' : 'javascript:void(0)',
								'photo_id' : defaultEle.getProperty('photo_id'),
								'class' : 'book_photo_delete_link',
								'html' : '<?php echo $this->translate('delete')?>',
								'events' : {
									'click' : en4.book.deletePhoto
								}
							}).inject(spanDeleteEle);
							spanDeleteEle.inject(defaultEle.getParent(), 'before');
						}
							
						$(ele).removeClass('book_photo_set_default_link')
							.addClass('book_photo_default')
							.removeEvents('click')
							.addEvent('click', en4.book.setDefaultPhoto)
							.set('text', '<?php echo $this->translate('default')?>')
							.getParent().getPrevious().destroy();	
					}
				}
			});
			reqSetDefault.send();
		}
	}
	
	en4.book.deletePhoto = function(event) {
		var elePhotoDiv = $(this).getParent('.book_manage_photo');
		if (elePhotoDiv) {
			var photoId = $(this).getProperty('photo_id');
			if (photoId) {
				var reqDelete = new Request.JSON({
					url : '<?php 
						echo $this->url(
							array(
								'action' => 'delete-photo', 
								'id' => $this->book->getIdentity(), 
								'format' => 'json'
							), 
							'book_specific'
						)
							?>',
					data : {
						'photo_id' : photoId
					},
					onSuccess : function(res, responseText) {
						if (res && res.status == 1) {
							$(elePhotoDiv).destroy();				
						}
					}
				});
				reqDelete.send();				
			}
		}
	}
	
	en4.core.runonce.add(function(){
		$$('a.book_photo_set_default_link').removeEvents('click').addEvent('click', en4.book.setDefaultPhoto);
		$$('a.book_photo_delete_link').removeEvents('click').addEvent('click', en4.book.deletePhoto);
	});
</script>
