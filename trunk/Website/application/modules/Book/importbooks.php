<?php
if (Engine_Api::_()->book()->importRawBooks()) {
	echo 'The books are imported successfully !';
	die;
}