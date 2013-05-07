<?php

class CrawlCommand extends CConsoleCommand
{
	public function run($args)
	{
		$receiver = $args[0];
		
		if ($receiver == 'tiki.vn') {
			$provider = new Tiki();
		} elseif ($receiver == 'vnexpress.net') {			
			$provider = new Vnexpress();
		}
		
		foreach ($provider->getUrls() as $url) {
 			foreach ($provider->getLinks($url) as $link) {
 				echo $link . PHP_EOL;
 				$provider->storeHref($link);
 			}
			echo 'Parse Content-------------------------' . PHP_EOL; 
			foreach (Link::model()->findAll('provider = :provider', array('provider' => $provider->getProviderName())) as $model)
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
					} else {
						echo PHP_EOL;
					}
				} 
			}
		}
	}
}