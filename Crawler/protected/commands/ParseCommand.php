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

		switch ($receiver)
		{
		    case 'tiki.vn':
		        $provider = new Tiki();
		        break;
		    case 'vnexpress.net':
		        $provider = new Vnexpress();
		        break;
		    case 'phunuonline.com.vn':
		        $provider = new Phunuonline();
		        break;
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
					    //$value = mb_convert_encoding($value, "UTF-8");
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