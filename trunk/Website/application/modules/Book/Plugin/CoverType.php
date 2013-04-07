<?php
class Book_Plugin_CoverType {
	private static $_coverTypes = array(
		'1' => array('name' => 'hardcover', 'title' => 'Hardcover', 'id' => 1),
		'2' => array('name' => 'paperback', 'title' => 'Paperback', 'id' => 2),
	);

	/**
	 *
	 * @param int $type
	 * @return string
	 */
	public static function getBookType($type, $return_key = 'title') {
		$types = self::$_coverTypes;

        if (array_key_exists($type, $types)) {
            $bookType = $types[$type];
            if (array_key_exists($return_key, $bookType)) {
            	return $bookType[$return_key] ;
            }
        }

        return null;
	}
    
    public static function getAllBookTypes($col = 'name') {
        $arrTypes = array();
        foreach(self::$_coverTypes as $type) {
            $arrTypes[$type[$col]] = $type['title'];
        }
        return $arrTypes;
    }
}