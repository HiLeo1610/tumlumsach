<?php
class CopyCommand extends CConsoleCommand {
	const DEFAULT_COPIED_LINKS = 500;
	
	public function run($args)
	{
		$criteria = new CDbCriteria();
		$criteria->limit = self::DEFAULT_COPIED_LINKS;
		$criteria->condition = 'iscopied = 0';
		$links = Link::model()->findAll($criteria);
		
		$dataPath = Yii::app()->params['data_path'];
		
		foreach ($links as $link) {
			echo 'COPY content for the link #' . $link->link_id . ' with URL ' . $link->href . PHP_EOL;
			
			$contentFile = $dataPath . $link->link_id . '.html';
			$handle = fopen($contentFile, 'w') or die('Cannot open file:  '.$contentFile);
			fwrite($handle, $link->content);
			fclose($handle);
			$link->iscopied = 1;
			$link->save();
		}
	}
}