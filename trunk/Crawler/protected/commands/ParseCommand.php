<?php
class ParseCommand extends CConsoleCommand {
	const DEFAULT_PARSE_LIMIT = 300;
	
	public function run($args)
	{
		$receiver = $args[0];
		$isForceFix = false;
		if (isset($args[1]) && $args[1] == 'force-fix') {
			$isForceFix = true;	
		}
		
		if ($receiver == 'tiki.vn') {
			$provider = new Tiki();
		} elseif ($receiver == 'vnexpress.net') {			
			$provider = new Vnexpress();
		}
		
		$criteria = new CDbCriteria();
		$criteria->addCondition(array('provider = :provider', 'fetched = :fetched'));
		$criteria->params = array(
			':provider' => $provider->getProviderName(),
			':fetched' => 0
		);
		$criteria->limit = self::DEFAULT_PARSE_LIMIT;
		
		foreach (Link::model()->findAll($criteria) as $model)
		{
			echo $model->href . PHP_EOL;
			
			$objClsName = CrawlProvider::getObjClassName($provider->getType());
			$obj = $objClsName::model()->find('link_id = ' . $model->link_id);
			if ($obj == null) 
			{
				echo 'Parse Content : ' . $model->href . ' ';
				$arrContent = $provider->parseContent($model->href, $isForceFix);
				if ($arrContent != null && !empty($arrContent)) {
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