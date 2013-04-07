<?php
class Book_Model_Chapter extends Book_Model_Base
{
	protected $_parent_type = 'book_work';
	
	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'chapter',
			'reset' => true,
			'id' => $this->getIdentity(),
			'slug' => $this->getSlug()
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
	}
	
	/**
	 * Gets the description of the item. This might be about me for users (todo
	 *
	 * @return string The description
	 */
	public function getDescription()
	{
		if (isset($this->content))
		{
			return $this->content;
		}
		return '';
	}
	
	public function getTitle() {
		$title = parent::getTitle();
		if (empty($title)) {
			return sprintf(Zend_Registry::get('Zend_Translate')->translate('Chapter %d'), $this->getOrder());
		} 
		return $title;
	}
	
	public function getOrder() {
		$chapterTbl = new Book_Model_DbTable_Chapters();
		$chapterTblName = $chapterTbl->info('name'); 
		$select = $chapterTbl->select()->setIntegrityCheck(false)
			->from($chapterTblName, new Zend_Db_Expr('COUNT(chapter_id) AS ordering'))
			->where("$chapterTblName.work_id = ?", $this->work_id)
			->where("$chapterTblName.chapter_id < ?", $this->getIdentity());
		
		$data = $chapterTbl->fetchRow($select);
    	return (int) $data->ordering + 1;	
	}
	
	public function getOwner($recurseType = null)
  	{
    	$work = Engine_Api::_()->getItem('book_work', $this->work_id);
		return $work->getOwner($recurseType);
  	}
	
	public function getWork() {
		if (!empty($this->work_id)) {
			return Engine_Api::_()->getItem('book_work', $this->work_id);
		}
	}
	
	public function isPublished() {
		if ($this->published) {
			$work = $this->getWork();
			if (!empty($work)) {
				return $work->published;
			}
		}
		return false;
	}
}