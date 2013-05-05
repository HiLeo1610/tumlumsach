<?php
return array(
	'package' => array(
		'type' => 'module',
		'name' => 'book',
		'version' => '4.01',
		'path' => 'application/modules/Book',
		'title' => 'Book',
		'description' => 'Book',
		'author' => 'Dang Tran Hai',
		'callback' => array('class' => 'Engine_Package_Installer_Module', ),
		'actions' => array(
			0 => 'install',
			1 => 'upgrade',
			2 => 'refresh',
			3 => 'enable',
			4 => 'disable',
		),
		'directories' => array(0 => 'application/modules/Book', ),
		'files' => array(0 => 'application/languages/en/book.csv', ),
	),

	'items' => array(
		'book',
		'book_author',
		'book_post',
		'book_category',
		'book_photo',
		'book_popularity',
		'book_work',
		'book_chapter',
		'book_rawbook',
		'book_link',
		'book_rawpost',
	),
	'hooks' => array(
		array(
		  	'event' => 'onCoreCommentCreateAfter',
		  	'resource' => 'Book_Plugin_Core',
		),
		array(
			'event' => 'onCoreLikeCreateAfter',
			'resource' => 'Book_Plugin_Core',
		),
		array(
			'event' => 'onCoreLikeDeleteBefore',
			'resource' => 'Book_Plugin_Core',
		)
	),
	'routes' => array(
		'book_general' => array(
			'route' => 'books/:action/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'book',
				'action' => 'index',
			),
			'reqs' => array('action' => '(index|create)')
		),
		'book' => array(
			'route' => 'books/:id/:slug/',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'book',
				'action' => 'view',
				'slug' => '-'
			),
			'reqs' => array('id' => '\d+')
		),
		'book_specific' => array(
			'route' => 'book/:action/:id/:slug/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'book',
				'action' => 'view',
				'slug' => '-'
			),
			'reqs' => array(
				'id' => '\d+',
				'action' => '(rate|upload-photo|delete-photo|set-default-photo|favorite|remove-favorite|edit|delete)',
			)
		),
		'book_list' => array(
			'route' => 'books/list/:category_id/:slug/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'index',
				'action' => 'list',
				'category_id' => '-',
				'slug' => '-'
			)
		),
		'book_author' => array(
			'route' => 'books/author/:action/:author_id/:slug/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'author',
				'action' => 'index',
				'slug' => '-'
			)
		),
		'author_general' => array(
			'route' => 'authors/:action/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'author',
				'action' => 'index',
			),
		),
		'publisher_general' => array(
			'route' => 'publishers/:action/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'publisher',
				'action' => 'index',
			),
		),
		'shop_general' => array(
			'route' => 'shops/:action/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'shop',
				'action' => 'index',
			),
		),
		'post_general' => array(
			'route' => 'posts/:action/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'post',
				'action' => 'index',
			),
		),
		'post' => array(
			'route' => 'posts/:action/:id/:slug/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'post',
				'slug' => '-',
				'id' => '-',				
			),		
			'reqs' => array('action' => '(view|create|rate|edit|delete)')	
		),
		'work_general' => array(
			'route' => 'works/:action/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'work',
				'action'=> 'index' 
			),		
		),
		'work' => array(
			'route' => 'works/:action/:id/:slug/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'work',
				'action' => 'view',
				'slug' => '-'
			),
			'reqs' => array('id' => '\d+')
		),
		'chapter_general' => array(
			'route' => 'chapters/:action/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'chapter',
				'action'=> 'index' 
			),		
		),
		'chapter' => array(
			'route' => 'chapters/:action/:id/:slug/*',
			'defaults' => array(
				'module' => 'book',
				'controller' => 'chapter',
				'action' => 'view',
				'slug' => '-'
			),
			'reqs' => array('id' => '\d+')
		),
	)
);
?>