<?php
	return array(
		'package' => array(
			'type' => 'widget',
			'name' => 'introduction-block',
			'version' => '4.01',
			'path' => 'application/widgets/introduction-block',
			'title' => 'Introduce Block',
			'description' => 'Displays the introduction block.',
			'author' => 'Hai Dang',
			'directories' => array('application/widgets/introduction-block', ),
		),

		// Backwards compatibility
		'type' => 'widget',
		'name' => 'introduction-block',
		'version' => '4.01',
		'title' => 'Introduction Block',
		'description' => 'Displaythe introduction block.',
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