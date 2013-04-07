<?php
class Book_Model_Category extends Core_Model_Item_Abstract {
	protected $_arrSubCats = null;

	public function addSubCategory($cat) {
		if ($this->_arrSubCats == null) {
			$this->_arrSubCats = array();
		}
		array_push($this->_arrSubCats, $cat);
	}

	public function getSubCategories() {
		if ($this->_arrSubCats == null) {
			$this->_arrSubCats = array();
		}
		return $this->_arrSubCats;
	}

	public function getUsedCount() {
		$table = new Book_Model_DbTable_Books();
		$tblName = $table->info('name');
		$select = $table->select()->from($tblName, "COUNT(*) as count")
			->where("$tblName.category_id = ?", $this->getIdentity());
		$total = $table->fetchRow($select);
		return $total['count'];
	}

	public function getHref($params = array()) {
		$params = array_merge(array(
				'route' => 'book_list',
				'reset' => true,
				'category_id' => $this->getIdentity(),
				'slug' => $this->getSlug()
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}

	public function getTitle()
	{
		if(isset($this->category_name))
		{
			return $this->category_name;
		}
		return null;
	}

	public function getParentNode() {
		if (!$this -> parent_id) {
			return null;
		}
		return $this -> _table -> find($this -> parent_id) -> current();
	}

	public function getBreadCrumNode() {
		$result = array($this);
		$parent = $this -> getParentNode();
		while ($parent) {
			$result[] = $parent;
			$parent = $parent -> getParentNode();
		}
		return array_reverse($result);
	}

	public function getLevel() {
		return count($this -> getBreadCrumNode());
	}
	
	public function getNewestBooks($limit, $params = NULL) {
		$table = new Book_Model_DbTable_Books;
		$tableName = $table->info(Zend_Db_Table_Abstract::NAME);
		$select = $table->getSelect();
		$select->where("$tableName.category_id = ?", $this->getIdentity());
		if ($params && isset($params['text']) && !empty($params['text'])) {
			$select->where("$tableName.book_name LIKE ?", "%{$params['text']}%");
		}
		$select->limit($limit);
		
		return $table->fetchAll($select);
	}
}