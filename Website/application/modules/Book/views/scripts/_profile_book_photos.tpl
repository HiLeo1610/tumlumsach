<?php
	$this->headScript()->appendFile($this->layout()->staticBaseUrl 
		. 'application/modules/Book/externals/scripts/_class.noobSlide.packed.js');
?>
<h3><?php echo $this->translate('Book Photos')?></h3>

<?php
	$photos = $this->book->getAllApprovedPhotos();	
?>

<div class="sample">
	<div class="mask6">
		<div id="box7">
			<?php foreach($photos as $photo) : ?>
				<span><img src="<?php echo $photo?>" alt="Photo" /></span>
			<?php endforeach; ?>
		</div>
	</div>

	<div id="thumbs7">
		<div class="thumbs">
			<?php foreach($photos as $photo) : ?>
				<div class="thumbs_hanlder"><img src="<?php echo $photo?>" alt="Photo Thumb" /></div>
			<?php endforeach; ?>
		</div>

		<div id="thumbs_mask7"></div>

		
		<!--<p id="thumbs_handles7">
			<?php foreach($photos as $photo) : ?>
				<span></span>
			<?php endforeach; ?>
		</p>-->
	</div>
</div>

<script language="javascript">
	en4.core.runonce.add(function() {
		var nItems = <?php echo count($photos)?>;
		var arrItems = [];
		for (var i = 0; i < nItems; i++) {
			arrItems.push(i);
		} 
		var startItem = 0; //or   0   or any
		var thumbs_mask7 = $('thumbs_mask7').set('opacity',0.8);
		var fxOptions7 = {property:'left',duration:1000, transition:Fx.Transitions.Back.easeOut, wait:false}
		var thumbsFx = new Fx.Tween(thumbs_mask7,fxOptions7);
		var nS7 = new noobSlide({
			box: $('box7'),
			items: arrItems,
			handles: $$('#thumbs7 .thumbs_hanlder'),
			fxOptions: fxOptions7,
			onWalk: function(currentItem){
				thumbsFx.start(currentItem*47-580);
			},
			startItem: startItem,
			size : 182,
			autoPlay : true
			// size : 190
		});
		//walk to first with fx
		nS7.walk(startItem);
	});
</script>