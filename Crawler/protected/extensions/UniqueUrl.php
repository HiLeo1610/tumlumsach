<?php
class UniqueURL extends CValidator {
	protected function validateAttribute($object,$attribute)
	{
		$value = $object->$attribute;
		$link = Link::model()->findAll(array(
			'condition' => 'href LIKE :href1 OR :href1 LIKE href',
			'params' => array(
				':href1' => '%' . $value . '%'					
			)
		));
		if ($link != NULL) {
			$this->addError($object,$attribute,'This URL has already existed !');
		}
	}
}