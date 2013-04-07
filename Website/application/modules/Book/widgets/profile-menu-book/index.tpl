<?php if(count($this->menu) > 0) :?>
	<div class="quicklinks">
		<?php
			// Render the menu
			echo $this->navigation()->menu()->setContainer($this->menu)->render();
		?>
	</div>
<?php endif; ?>