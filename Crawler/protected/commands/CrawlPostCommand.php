<?php

class CrawlPostCommand extends CConsoleCommand {
	public function run($args) {		
		$receiver = $args[0];
		
		if ($receiver == 'vnexpress.net')
		{
			$provider = new Vnexpress();		
		}
		
		
	}
}