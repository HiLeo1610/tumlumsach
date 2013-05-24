<?php
	$currentPage = $this->paginator->getCurrentPageNumber();
	$totalItem = $this->paginator->getTotalItemCount();
	$itemCountPerPage = $this->paginator->getItemCountPerPage();
	$pages = ceil(($totalItem - 1) / $itemCountPerPage);
	
?>
<ul class="book_list">
	<?php if (isset($this->viewInfo) && $this->viewInfo == false) : ?>
		<?php if ($currentPage > 1) : ?>
			<li class="book_previous_arrow book_arrow" nextPage="<?php echo $currentPage - 1?>"></li>
		<?php else : ?>
			<li class="book_arrow_empty"></li>			
		<?php endif; ?>
	<?php endif; ?>
		
	<?php foreach($this->paginator as $book) : ?>
		<li class="book_item">
            <?php echo $this->partial('_book.tpl', 'book', array('book' => $book, 'viewInfo' => $this->viewInfo))?>
        </li>
	<?php endforeach; ?>
	
	<?php if (isset($this->viewInfo) && $this->viewInfo == false) : ?>
		<?php if ($currentPage < $pages) : ?>
			<li class="book_next_arrow book_arrow" nextPage="<?php echo $currentPage + 1?>"></li>			
		<?php endif; ?>
	<?php endif; ?>	
</ul>
<?php if (!isset($this->viewInfo) ||  $this->viewInfo) : ?>
	<div class="book_paginator paginator_content_<?php echo $this->identity?>">
		<?php
			echo $this->paginationControl($this->paginator);
		?>
	</div>
	
	<script language="javascript" type="text/javascript">
		en4.core.runonce.add(function(){
			$$('.paginator_content_<?php echo $this->identity?> a').addEvent('click', function(event) {
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
<?php else : ?>
	<script language="javascript" type="text/javascript">
		en4.core.runonce.add(function(){
			$$('ul.book_list .book_arrow').addEvent('click', function(event) {
				var ele = event.target;
				var page = ele.getProperty('nextPage');
				var element = $$('.introduction_books > .generic_layout_container')[0];
				en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index',
                    data : {
                    	name : 'popular-books',
                    	mod : 'book',
                        format : 'html',
                        page : page,
                        viewInfo : 0,
                        itemCountPerPage : <?php echo $itemCountPerPage?>
                    }
                }), {
                    'element' : element
                });					
			});
		});
	</script>	
<?php endif; ?>