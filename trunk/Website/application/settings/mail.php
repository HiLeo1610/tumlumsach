<?php defined('_ENGINE') or die('Access Denied'); return array (
  'class' => 'Zend_Mail_Transport_Smtp',
  'args' => 
  array (
    0 => 'smtp.live.com',
    1 => 
    array (
      'port' => 587,
      'ssl' => 'tls',
      'auth' => 'login',
      'username' => 'no-reply@tumlumsach.com',
      'password' => 'tumlum123',
    ),
  ),
); ?>