<?php echo $this->formBookSearch->render($this)?>
<div class="book_clear"></div>
<script type="text/javascript">
	en4.core.runonce.add(function(){
    	if($('text')){
      		new OverText($('text'), {
        		positionOptions: {
		        	position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          	edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          	offset: {
		            	x: ( en4.orientation == 'rtl' ? -11 : 11 ),
		            	y: 8
		          	}
        		}
      		});
    	}
	});
</script>

<style>
	form dl.zend_form { position: relative;}
</style>