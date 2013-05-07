<?php
class Vnexpress extends CrawlPostProvider {
	private $_links = NULL;
	private $_arrXPath = NULL;
	
	function __construct()
	{
		parent::__construct();
		
		$this->_urls = array(
			'http://giaitri.vnexpress.net/tin-tuc/sach/diem-sach',
		);
		$this->_providerName = 'vnexpress.net';
		$this->_arrXPath = array(
			'name' => '//*[@id="fck_container"]/div[1]/h1',
			'content' => '//*[@id="fck_container"]/div[3]',
			'book_name' => "//p[contains(text(), 'Tên sách:')]/em"
		);
	}
	
	public function getLinks($url)
	{
		$links = array();
	
		$content = file_get_contents($url);

		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($content);
		libxml_use_internal_errors(false);

		$aTags = $dom->getElementsByTagName('a');
		foreach ($aTags as $tag)
		{					
			$href = $tag->getAttribute('href');
			if (isset($href) && !empty($href))
			{
				if (preg_match('/^(http\:\/\/giaitri.vnexpress.net\/tin-tuc\/sach\/diem-sach)\/([\da-z\.-]+)(.html)/', $href))
				{
					if (!in_array($href, $links))
					{
						array_push($links, $href);
					}
				}
			}
		}
	
		return $links;
	}
	
	private function _isValidContent($arrContent) 
	{
		return (!empty($arrContent['name']) && !empty($arrContent['content']));
	}
	
	private function _normalizeContent($arrContent) 
	{
		if (isset($arrContent['name'])) {
			$book = Book::model()->find('LCASE(book_name) LIKE LCASE(:name)', array('name' => '%' . $arrContent['name'] . '%'));
			if (isset($book) && !empty($book)) {
				$arrContent['book_link_id'] = $book->link_id;
			}
		}
		
		return $arrContent;
	}
	
	public function parseContent($href)
	{
		$model = Link::model()->find('href = :href AND type = :type', array('href' => $href, 'type' => $this->getType()));
	
		if ($model == NULL) {
			$model = $this->storeHref($href);
		}
	
		$arrContent = array();
	
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($model->content);
		libxml_use_internal_errors(false);
	
		$xpath = new DOMXPath($dom);
		foreach ($this->_arrXPath as $key => $value)
		{
			$content = $xpath->query($value);
			if ($content->length > 0)
			{
				if ($key != 'content') 
				{
					$arrContent[$key] = $content->item(0)->nodeValue;
				} 
				else 
				{
					$html = '';
					$node = $content->item(0);
					libxml_use_internal_errors(true);
					$d = new DOMDocument("1.0", "UTF-8");
					foreach ($node->childNodes as $child)
					{
						$no = $d->importNode($child,true);
						$d->appendChild($no);						
					}	
					$html .= $d->saveXML();
                                        $xmlStr = '<?xml version="1.0" encoding="UTF-8"?>';
                                        $p = strpos($html, $xmlStr);
                                        if ($p !== false) {
                                            $html = substr($html, strlen($xmlStr));
                                        }
					$arrContent[$key] = trim($html);
				}
			}
		}
		if ($this->_isValidContent($arrContent))
		{
			return $this->_normalizeContent($arrContent);
		}
	
		return NULL;
	}
}