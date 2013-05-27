<?php

class Phunuonline extends CrawlPostProvider
{
    private $_links = NULL;
    private $_arrXPath = NULL;

    function __construct()
    {
        parent::__construct();

        $this->_urls = array(
                'http://phunuonline.com.vn/nguoi-yeu-sach.html',
        );
        $this->_providerName = 'phunuonline.com.vn';
        $this->_arrXPath = array(
                'name' => '//div[@id="detail"]/h2',
                'content' => '//div[@id="newscontents"]',
                'book_name' => array('//div[@id="newscontents"]')
        );
    }
    /*
     * (non-PHPdoc) @see CrawlProvider::getLinks()
     */
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
			if (!empty($href))
			{
			    //$pattern = '/^(http\:\/\/phunuonline\.com\.vn\/nguoi-yeu-sach\/)(([\da-z\.-]+)\/){2}.+(.html)/';
			    $pattern = '/^(http\:\/\/phunuonline\.com\.vn\/nguoi-yeu-sach\/)(tu-sach-gia-dinh|tac-gia-tac-pham|sach-moi|tac-pham-va-du-luan|thu-choi-sach).+(.html)/';
			    if (preg_match($pattern, $href))
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

    /*
     * (non-PHPdoc) @see CrawlProvider::parseContent()
     */
    public function parseContent($href, $isForceFix = false)
    {
		$model = Link::model()->find('href = :href AND type = :type', array('href' => $href, 'type' => $this->getType()));
		$content = $model->getHTMLContent();

		if ($model == NULL || empty($content)) {
			echo 'store href ' . $href . PHP_EOL;
			$model = $this->storeHref($href);
		}

		if (!empty($model) && !empty($content)) {
			$arrContent = $this->_parseHTMLToContent($content);

			if ($this->_isValidContent($arrContent)) {
				return $this->_normalizeContent($arrContent);
			} else {
				$content = file_get_contents($model->href);
				$model->saveHTMLContent($content);

				$arrContent = $this->_parseHTMLToContent($content);
				if ($this->_isValidContent($arrContent)) {
					$model->saveHTMLContent($content);
					return $this->_normalizeContent($arrContent);
				}
			}
		}

		return null;
	}

    private function _isValidContent($arrContent)
    {
        return (!empty($arrContent['name']) && !empty($arrContent['content']));
    }

    private function _normalizeContent($arrContent)
    {
        if (isset($arrContent['book_name'])) {
            $book = Book::model()->find(
                    'LCASE(book_name) LIKE LCASE(:name)',
                    array('name' => '%' . $arrContent['book_name'] . '%')
            );
            if ($book == null) {
                $book = Book::model()->find('LCASE(book_name) LIKE LCASE(:name)', array('name' => $arrContent['name']));
            }

            if (isset($book) && !empty($book)) {
                $arrContent['book_link_id'] = $book->link_id;
            }
        }

        if (isset($arrContent['book_name'])) {
            unset($arrContent['book_name']);
        }

        return $arrContent;
    }

    private function _parseHTMLToContent($content) {
        $arrContent = array();
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($content);
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
                if ($key != 'content') {
                    $arrContent[$key] = $content->item(0)->nodeValue;
                } else {
                    $html = '';
                    $node = $content->item(0);
                    libxml_use_internal_errors(true);
                    $d = new DOMDocument("1.0", "UTF-8");
                    foreach ($node->childNodes as $child) {
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

        return $arrContent;
    }
}