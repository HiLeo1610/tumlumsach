<?php
	return array(
		'package' => array(
			'type' => 'widget',
			'name' => 'welcome',
			'version' => '4.01',
			'path' => 'application/widgets/welcome',
			'title' => 'Introduce Block',
			'description' => 'Displays the welcome page.',
			'author' => 'Hai Dang',
			'directories' => array('application/widgets/welcome', ),
		),

		// Backwards compatibility
		'type' => 'widget',
		'name' => 'welcome',
		'version' => '4.01',
		'title' => 'Welcome',
		'description' => 'Displaythe welcome page.',
		'category' => 'Widgets',
		'adminForm' => array(
			'elements' => array( 
				array(
					'Textarea',
					'content',
					array('label' => 'Content')
				)
			)
		)
	);
?>