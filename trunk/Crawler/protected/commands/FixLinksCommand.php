<?php

class FixLinksCommand extends CConsoleCommand
{
	public function run($args)
	{
		$criteria = new CDbCriteria();
		$criteria->condition = 'isfixed = 0';
		$criteria->limit = 250;
		foreach (Link::model()->findAll($criteria) as $link) {
			$href = trim($link->href);
			
			echo 'Fetching the URL ' . $href . PHP_EOL;
			try {
				$content = @file_get_contents($href);
				$link->content = $content;
				$link->href = $href;
				
				/*$otherLink = Link::model()->findBySql("href LIKE $href% AND link_id != '$link->link_id'");
				if (!empty($otherLink)) {
					$otherLink->delete();
				}*/ 	
				
				$link->isfixed = 1;
				$link->save();
			} catch (Exception $e) {
				$link->delete();
			}
		}
		
		echo 'DONE' . PHP_EOL;
	}
}