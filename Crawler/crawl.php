<?php
	

	$url = 'http://www.tiki.vn';
	$content = file_get_contents($url);
	$dom = new DOMDocument();
	$dom->loadHTML($content);
	
	$aTags = $dom->getElementsByTagName('a');
	foreach ($aTags as $tag) {
	    $href = $tag->getAttribute('href');
	    if (isset($href) && !empty($href)) {
	    	if (preg_match('/^(http\:\/\/tiki.vn)\/([\da-z\.-]+)(.html\?ref)/', $href)) {
	    		echo $href . PHP_EOL;
	    	}
	    }
	}
	
?>