<?php
class Core_Model_Rowset_Item extends Engine_Db_Table_Rowset {
    public function init()
    {
        $arrPhotoIds = array();
        foreach ($this->_data as $data) {
            if (!empty($data['photo_id'])) {
                $arrPhotoIds[$data['user_id']] = $data['photo_id'];
            }
        }
    
        $fileTbl = Engine_Api::_()->getItemTable('storage_file');
        $files = $fileTbl->getMultiFiles($arrPhotoIds);
        
        if (!empty($files)) {
            $urls = array();
            foreach ($files as $file) {
                if (!array_key_exists($file->getIdentity(), $urls)) {
                    $url = array();
                } else {
                    $url = $urls[$file->parent_file_id];
                }
                $url = $file->map();
                if (empty($urls[$file->parent_file_id])) {
                    $urls[$file->parent_file_id] = array();
                }
                $urls[$file->parent_file_id][$file->type] = $url;
            }
            foreach ($this->_data as $index => $data) {
                if (!empty($data['photo_id']) && isset($urls[$data['photo_id']])) {
                    $this->_data[$index]['photo_url'] = $urls[$data['photo_id']];
                }
            }
        }
    }
}