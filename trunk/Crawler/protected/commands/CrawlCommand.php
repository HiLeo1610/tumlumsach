<?php

class CrawlCommand extends CConsoleCommand
{
	const DEFAULT_LIMIT = 500;
	
	public function run($args)
	{
		$receiver = $args[0];
		
		if ($receiver == 'tiki.vn') {
			$provider = new Tiki();
		} elseif ($receiver == 'vnexpress.net') {			
			$provider = new Vnexpress();
		}
		
		$operation = '';
		if (isset($args[1])) {
			$operation = $args[1];
		}
		
		foreach ($provider->getUrls() as $url) {
			if ($operation != 'parse-only') {
	 			foreach ($provider->getLinks($url) as $link) {
	 				echo $link . PHP_EOL;
	 				$provider->storeHref($link);
	 			}
			}
			
			echo 'Parse Content for ' . $url . PHP_EOL;
			 
			$criteria = new CDbCriteria();
			$criteria->addCondition(array('provider = :provider', 'fetched = :fetched'));
			$criteria->params = array(
				':provider' => $provider->getProviderName(),
				':fetched' => 0
			);
			$criteria->limit = self::DEFAULT_LIMIT;
			foreach (Link::model()->findAll($criteria) as $model)
			{
				echo $model->href . PHP_EOL;
				
				$objClsName = CrawlProvider::getObjClassName($provider->getType());
				$obj = $objClsName::model()->find('link_id = ' . $model->link_id);
				if ($obj == NULL) 
				{
					echo 'Parse Content : ' . $model->href . ' ';
					$arrContent = $provider->parseContent($model->href);
					if ($arrContent != NULL && !empty($arrContent)) {
						echo 'Parsed Ok' . PHP_EOL;
						$newObj = CrawlProvider::createNewObject($provider->getType());
						foreach ($arrContent as $key => $value) {
							$newObj->$key = $value;
						}
						$newObj->link_id = $model->link_id;
						$newObj->save();
						
						$model->fetched = 1;
						$model->save();
					} else {
						echo PHP_EOL;
					}
				} 
			}
		}
	}
}