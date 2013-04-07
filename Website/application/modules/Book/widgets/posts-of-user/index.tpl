<ul>
	<?php foreach($this->paginator as $post) : ?>
		<li>
			<?php echo $this->partial('_post.tpl', 'book', array('post' => $post))?>
		</li>
	<?php endforeach; ?>
</ul>

<div class="book_pages book_paginator paginator_content_<?php echo $this->identity?>">
	<?php
		echo $this->paginationControl($this->paginator);
	?>
</div>

<script language="javascript" type="text/javascript">
	en4.core.runonce.add(function(){
		$$('.paginator_content_<?php echo $this->identity?> a').removeEvents('click').addEvent('click', function(event) {
			event.stop();
			var page = 1;
			var href = $(this).getProperty('href');
			if (href) {
				var hrefData = href.split('/');
				for (var i = 0; i < hrefData.length; i++) {
					if (hrefData[i] == 'page') {
						page = hrefData[i+1];
						break;
					}					
				}
			}			
			if (page) {
				var bookPaginatorEle = $(this).getParent('.book_paginator');
				if (bookPaginatorEle) {
					var element = bookPaginatorEle.getParent(); 
					en4.core.request.send(new Request.HTML({
	                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%s', $this->identity) ?>,
	                    data : {
	                        format : 'html',
	                        page : page,
	                        subject : en4.core.subject.guid,
	                    }
	                }), {
	                    'element' : element
	                })
               	}
			}
		});
	});
</script>