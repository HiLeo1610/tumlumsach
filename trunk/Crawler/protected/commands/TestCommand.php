<?php

class TestCommand extends CConsoleCommand {
	public function run($args) {		
		$href = 'http://tiki.vn/chan-dung-ac-ma-p57297.html';
		$content = file_get_contents($href);
		
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
		libxml_use_internal_errors(false);

		$xpath = new DOMXPath($dom);
		$nodes = $xpath->query('//link[@rel="canonical"]');
		$node = $nodes->item(0);
		echo $node->getAttribute('href');		
	}
}