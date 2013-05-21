<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'threecolors',
    'version' => NULL,
    'revision' => '$Revision: 9378 $',
    'path' => 'application/themes/threecolors',
    'repository' => 'socialengine.com',
    'title' => 'Three colors',
    'thumb' => 'theme.jpg',
    'author' => 'Hai Dang',
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/clean',
    ),
    'description' => '',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>