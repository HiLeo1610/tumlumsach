<?php

class UpdateCommand extends CConsoleCommand {
	public function run($args)
	{
		foreach (Link::model()->findAll('provider = :provider', array('provider' => 'tiki.vn')) as $model) {
			$href = $model->href;
			echo $href . PHP_EOL;
			$pos = strpos($href, '?ref');
			if ($pos) {
				$model->href = substr($href, 0, $pos) . PHP_EOL;
				$model->save();
			}
		}
	}
}