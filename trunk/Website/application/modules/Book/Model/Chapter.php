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
        $id = $this->getIdentity();
        $workId = $this->work_id;
        
        if (!empty($id) && !empty($workId)) {
            $chapterTbl = new Book_Model_DbTable_Chapters();
            $chapterTblName = $chapterTbl->info('name');
            $select = $chapterTbl->select()->setIntegrityCheck(false)
                ->from($chapterTblName, new Zend_Db_Expr('COUNT(chapter_id) AS ordering'))
                ->where("$chapterTblName.work_id = ?", $this->work_id)
                ->where("$chapterTblName.chapter_id < ?", $this->getIdentity());
            $data = $chapterTbl->fetchRow($select);
            if ($data) {
                return (int) $data->ordering + 1;
            }
        }
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

    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File)
        {
            $file = $photo->getFileName();
        }
        elseif (is_array($photo) && !empty($photo['tmp_name']))
        {
            $file = $photo['tmp_name'];
        }
        elseif (is_string($photo) && file_exists($photo))
        {
            $file = $photo;
        }
        else
        {
            throw new Exception('invalid argument passed to setPhoto');
        }

        if ($this->photo_id)
        {
            $this->removeOldPhoto();
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
                'parent_id' => $this->getIdentity(),
                'parent_type' => $this->getType()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)->resize(800, 600)->write($path . '/m_' . $name)->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)->resize(400, 400)->write($path . '/p_' . $name)->destroy();

        // Resize image (featured)
        $imageFeatured = Engine_Image::factory();
        $imageFeatured->open($file);

        $size = min($imageFeatured->height, $imageFeatured->width);
        $x = ($imageFeatured->width - $size) / 2;
        $y = ($imageFeatured->height - $size) / 2;

        $imageFeatured->resample($x, $y, $size, $size, 200, 150)->write($path . '/f_' . $name)->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)->write($path . '/is_' . $name)->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/is_' . $name, $params);
        $iFeatured = $storage->create($path . '/f_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.icon');
        $iMain->bridge($iFeatured, 'thumb.featured');

        // Remove temp files
        @unlink($path . '/m_' . $name);
        @unlink($path . '/p_' . $name);
        @unlink($path . '/is_' . $name);
        @unlink($path . '/f_' . $name);

        // Update row
        $this->photo_id = $iMain->getIdentity();
        $this->save();

        return $this;
    }
}