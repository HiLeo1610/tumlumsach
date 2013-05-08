<?php
class Tiki extends CrawlBookProvider
{
	private $_links = NULL;
	private $_arrXPath = NULL;

	function __construct()
	{
		$this->_urls = array(
			'http://tiki.vn/new-products/sach-truyen-tieng-viet.html',			
		);
		$this->_providerName = 'tiki.vn';

		$this->_arrXPath = array(
				'book_name' => '//*[@id="product_addtocart_form"]/div[2]/h1',
				'published_date' => "//th[text()='Ngày xuất bản']/following-sibling::*",
				'price' => array(
					'//*[@class="product-shop"]//*[@class="price-box"]//*[@class="old-price"]/span[2]', 
					'//*[@class="product-shop"]//*[@class="regular-price"]/span'),
				'size' => "//th[text()='Kích thước']/following-sibling::*",
				'publisher' => "//th[text()='Nhà xuất bản']/following-sibling::*",
				'book_company' => "//th[text()='Công ty phát hành']/following-sibling::*",
				'num_page' => "//th[text()='Số trang']/following-sibling::*",
				'description' => '//*[@id="fragment-1"]',
				'photo' => '//*[@class="MagicToolboxContainer"]/*',
				'author' => '//*[@id="product_addtocart_form"]/div[2]/a[1]'
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
				if (preg_match('/^(http\:\/\/tiki.vn)\/([\da-z\.-]+)(.html\?ref)/', $href))
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

	private function _normalizeContent($content)
	{
		if (!empty($content['published_date']))
		{
			$content['published_date'] = CDateTimeParser::parse(trim($content['published_date']), 'dd-MM-yyyy');
		}
		if (!empty($content['price'])) {
			$price = explode(' ', trim($content['price']));
			if (count($price) > 0) {
				$content['price'] = intval(floatval($price[0]) * 1000);
			}
		}
		if (!empty($content['size'])) {
			$content['size'] = trim($content['size']);
		}
		if (!empty($content['publisher'])) {
			$content['publisher'] = trim($content['publisher']);
		}
		if (!empty($content['book_company'])) {
			$content['book_company'] = trim($content['book_company']);
		}
		if (!empty($content['book_name'])) {
			$name = trim($content['book_name']);
			$matches = preg_match('/\((.*?)\)/', $name);
			if (count($matches) > 0) {
				if (strtolower($matches[1]) == 'bìa mềm' || strtolower($matches[1]) == 'bìa cứng') {
					$content['type'] = $matches[1];
				}
				$content['book_name'] = trim(preg_replace('/\((.*?)\)/', '', $name));
			}
		}
		if (!empty($content['num_page'])) {
			$content['num_page'] = intval(trim($content['num_page']));
		}		
		if (!empty($content['description'])) {
			$description = trim($content['description']);
			$arrRemoveStr = array('TIKI KHUYÊN ĐỌC', 'ĐÁNH GIÁ TỪ TIKI.VN:', 'Mời các bạn đón đọc!');
			foreach ($arrRemoveStr as $str) {
				$description = str_replace($str, '', $description);
			}
			$content['description'] = $description;
		}
		if (!empty($content['author'])) {
			$content['author'] = trim($content['author']);
		}
		
		return $content;
	}

	private function _isValidContent($content)
	{
		return 
		!empty($content['book_name'])
// 		&& !empty($content['published_date'])
		&& !empty($content['price'])
// 		&& !empty($content['size'])
// 		&& !empty($content['publisher'])
// 		&& !empty($content['book_company'])
		//&& !empty($content['num_page'])
		&& !empty($content['description'])
		&& !empty($content['photo']);
// 		&& !empty($content['author']);
	}

	public function parseContent($href)
	{
		$model = Link::model()->find("href LIKE '" . $href . "'");
		
		if ($model == null || empty($model->content)) {
			echo 'store href ' . $href . PHP_EOL;
			$model = $this->storeHref($href);
		}
		if (!empty($model) && !empty($model->content)) {
			$arrContent = array();
	
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($model->content);
			libxml_use_internal_errors(false);
	
			$xpath = new DOMXPath($dom);
			foreach ($this->_arrXPath as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $val) {
						$content = $xpath->query($val);
						if ($content->length > 0) {
							break;
						}
					}
				} else {
					$content = $xpath->query($value);
				}
				
				if ($content->length > 0) {
					if ($key == 'photo') {
						foreach ($content as $node) {
							$href = $node->getAttribute('href');
							if (is_string($href) && !empty($href)) {
								$arrContent[$key] = $node->getAttribute('href');
							}
						}
					}
					elseif ($key == 'description') {
						$html = '';
						$node = $content->item(0);
						$d = new DOMDocument("1.0", "UTF-8");
						foreach ($node->childNodes as $child)
						{
							$d->appendChild($d->importNode($child,true));
						}
						$html .= $d->saveXML();
						$xmlStr = '<?xml version="1.0" encoding="UTF-8"?>';
	                    $p = strpos($html, $xmlStr);
	                    if ($p !== false) {
	                    	$html = substr($html, strlen($xmlStr));
	                    }
						$arrContent[$key] = trim($html);
					} else {
						$arrContent[$key] = $content->item(0)->nodeValue;
					}
				}			
			}
			
			if ($this->_isValidContent($arrContent)) {
				return $this->_normalizeContent($arrContent);
			}
	
			return null;
		}
	}
}