<?php
class Tiki extends CrawlBookProvider
{
	private $_links = NULL;
	private $_arrXPath = NULL;

	function __construct()
	{
		$this->_urls = array(
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=1',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=2',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=3',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=4',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=5',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=6',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=7',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=8',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=9',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=10',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=11',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=12',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=13',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=14',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=15',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=16',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=17',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=18',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=19',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=20',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=21',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=22',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=23',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=24',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=25',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=26',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=27',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=28',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=29',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=30',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=31',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=32',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=33',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=34',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=35',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=36',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=37',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=38',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=39',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=40',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=41',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=42',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=43',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=44',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=45',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=46',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=47',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=48',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=49',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=50',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=51',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=52',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=53',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=54',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=55',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=56',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=57',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=58',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=59',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=60',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=61',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=62',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=63',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=64',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=65',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=66',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=67',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=68',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=69',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=70',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=71',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=72',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=73',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=74',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=75',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=76',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=77',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=78',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=79',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=80',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=81',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=82',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=83',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=84',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=85',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=86',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=87',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=88',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=89',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=90',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=91',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=92',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=93',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=94',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=95',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=96',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=97',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=98',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=99',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=100',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=101',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=102',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=103',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=104',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=105',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=106',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=107',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=108',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=109',
				'http://tiki.vn/sach-truyen-tieng-viet.html?dir=asc&limit=48&order=entity_id&p=110',
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
				'description' => '//*[@id="fragment-1"]/div',
				'photo' => '//*[@class="MagicToolboxContainer"]/*',
				'author' => '//*[@id="product_addtocart_form"]/div[2]/a[1]'
		);
	}

	public function getLinks($url)
	{
		/*if ($this->_links == NULL || empty($this->_links))
		{
			$this->_links = array();
		}
		
		foreach ($this->_urls as $url) {*/
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
		//}

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
		$model = Link::model()->find('href = ?', $href);

		if ($model == NULL) {
			$model = $this->storeHref($href);
		}

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
					foreach ($node->childNodes as $child)
					{
						$d = new DOMDocument();
						$d->appendChild($d->importNode($child,true));
						$html .= $d->saveHTML();
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

		return NULL;
	}
}