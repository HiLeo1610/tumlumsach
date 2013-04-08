<?php

class CrawlCommand extends CConsoleCommand
{
	public function run($args)
	{
		$receiver = $args[0];

		if ($receiver == 'tiki.vn') 
		{
			$provider = new Tiki();
		}

		foreach ($provider->getLinks() as $link) {
			$provider->storeHref($link);
		}
		
		foreach (Link::model()->findAll() as $model) 
		{
			$book = Book::model()->find('link_id = ' . $model->id);
			if ($book == NULL) 
			{
				echo 'Parse Content : ' . $model->href . PHP_EOL;
				$arrContent = $provider->parseContent($model->href);
				if ($arrContent != NULL && !empty($arrContent)) {
					$newBook = new Book();
					foreach ($arrContent as $key => $value) {
						$newBook->$key = $value;
					}
					$newBook->link_id = $model->id;
					$newBook->save();
				}
			} 
		}
	}
}