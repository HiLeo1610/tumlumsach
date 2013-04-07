<?php
class Book_View_Helper_PriceVnd extends Zend_View_Helper_Abstract {
	private static $_VND_CURRENCY = 'VNĐ';

	public function priceVnd($price)
	{
		return $price . ' ' . self::$_VND_CURRENCY;
	}
}