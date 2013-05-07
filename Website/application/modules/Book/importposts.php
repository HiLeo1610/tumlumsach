<?php
if (Engine_Api::_()->book()->importRawPosts()) {
	echo 'The posts are imported successfully !';
	die;
}