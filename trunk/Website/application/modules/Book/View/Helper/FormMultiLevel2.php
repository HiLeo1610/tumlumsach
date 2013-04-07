<?php

class Book_View_Helper_FormMultiLevel2 extends Zend_View_Helper_Abstract {

	public function formMultiLevel2($name, $value = null, $attributes = array()) {

		$xhtml = array();
		// CODE HERE
		$xhtml[] = '<input type="hidden" name="' . $name . '" value="' . $value . '" id="id_'.$name.'" />

		';
		$model_class = $attributes['model'];
		$module = $attributes['module'];
		if (!$model_class) {
			throw new Exception('model is requirement');
		}

		$isSearch = isset($attributes['isSearch'])?(int)$attributes['isSearch']:0;

		$model = new $model_class;

		$item = $model -> find((int)$value) -> current();

		$level = 0;
		$lastname = '';


		$onchange = isset($attributes['onchange'])?$attributes['onchange']:"en4.book.changeCategory($(this),'{$name}','{$model_class}','{$module}',$isSearch,0)";

		if (!is_object($item)) {
			$options = $model -> getMultiOptions(0);
			$i = 0;
			$lastname =  sprintf("%s_%s", $name, 0);
			$element = new Zend_Form_Element_Select($lastname, array('multiOptions' => $options, 'onchange' => $onchange, ));
			$xhtml[] = '<div id="id_wrapper_' . $name . '_' . $i . '">' . $element -> renderViewHelper() . '</div>';
			$i = 1;
		} else {
			$nodes = $item -> getBreadCrumNode();
			$i  = 0;
			foreach ($nodes as $node) {

				$lastname = sprintf("%s_%s", $name, $i);
				$options = $model -> getMultiOptions($node -> parent_id);
				$element = new Zend_Form_Element_Select($lastname, array(
					'multiOptions' => $options,
					'onchange' => $onchange,
					'value' => $node -> getIdentity()));
				$style = 'style="margin-top: 8px;"';
				$xhtml[] = '<div ' . $style . ' id="id_wrapper_' . $name . '_' . $i . '" >' . $element -> renderViewHelper() . '</div>';
				++$i;

			}
		}

		$level = $model -> getMaxLevel();
		for (; $i < $level; ++$i) {
			$xhtml[] = '<div id="id_wrapper_' . $name . '_' . $i . '" style = "display: none">' . '<!-- wrapper at level ' . $i . '-->' . '</div>';
		}

		$xhtml[] = '<script type="text/javascript">'
				   .'window.addEvent("domready",function(){'
				   . "en4.$module.changeCategory($('$lastname'),'$name','$model_class','$module',$isSearch,1)});"
				   . '</script>';

		$xhtml = implode(PHP_EOL, $xhtml);
		return $xhtml;
	}

}
