<ul>
    <li class="book-btn-new book-btn-book-new">
    	<a href="<?php echo $this->url(array('action' => 'create'), 'book_general')?>" target="">
    		<span class="btn-arrow"></span>
    		<?php echo $this->translate('Post a book')?>
    	</a>    
    </li>
	<li class="book-btn-new book-btn-post-new">
    	<a href="<?php echo $this->url(array('action' => 'create'), 'post_general')?>" target="">
    		<span class="btn-arrow"></span>
    		<?php echo $this->translate('Write a post')?>
    	</a>    
    </li>
    <li class="book-btn-new book-btn-work-new">
    	<a href="<?php echo $this->url(array('action' => 'create'), 'work_general')?>" target="">
    		<span class="btn-arrow"></span>
    		<?php echo $this->translate('Post a work')?>	
    	</a>    
    </li>
</ul>