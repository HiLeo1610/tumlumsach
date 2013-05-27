<?php

class CrawlCommand extends CConsoleCommand
{
	public function run($args)
	{
		$receiver = $args[0];

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

		$operation = '';
		if (isset($args[1])) {
			$operation = $args[1];
		}

		$count = 0;
		if ($operation != 'parse-only') {
			foreach ($provider->getUrls() as $url) {
	 			foreach ($provider->getLinks($url) as $link) {
	 				echo $link . PHP_EOL;
	 				if ($provider->storeHref($link)) {
	 					$count++;
	 				}
	 			}
			}
		}

		echo 'Crawl successfully with ' . $count . ' new links' . PHP_EOL;
	}
}