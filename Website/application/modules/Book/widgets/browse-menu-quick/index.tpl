<ul>
    <li class="book-btn-new book-btn-book-new">
    	<a href="<?php echo $this->url(array('action' => 'create'), 'book_general')?>" target="">
    		<span class="btn-arrow"></span>
    		<?php echo $this->translate('Post a book')?>
    	</a>    
    </li>
	<li class="book-btn-new book-btn-post-new">
		<?php
			$subject = $this->subject();
			if (!empty($subject)) {
				$urlNewPost = $this->url(
					array('action' => 'create', 'parent_type' => $subject->getType(), 'parent_id' => $subject->getIdentity()), 
					'post_general' 
				);
			} else {
				$urlNewPost = $this->url(array('action' => 'create'), 'post_general'); 
			}
		?>
    	<a href="<?php echo $urlNewPost?>" target="">
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