<?php
class Book_Filter_Null implements Zend_Filter_Interface
{
	public function filter($value)
	{
		if (isset($value) && (empty($value))) {
			return NULL;
		}
		return $value;

	}
}