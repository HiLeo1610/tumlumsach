<?php
class Link extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'engine4_book_links';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('href', 'unique'),			
		);
	}
	
	public function getHTMLContent($refresh = true) 
	{
		$dataPath = Yii::app()->params['data_path'];
		
		$file = $dataPath . $this->link_id . '.html';
		if (!file_exists($file)) {
		    $content = file_get_contents($this->href);
		    
		    if (!empty($content)) {
		        $handle = fopen($file, 'w');
		        fwrite($handle, $content);
		        fclose($handle);
		    
		        return $content;
		    }
		}
		$handle = fopen($file, 'r');
		$filesize = filesize($file);
		if ($refresh && $filesize == 0) {
			fclose($handle);
			$content = file_get_contents($this->href);
			if (!empty($content)) {
				$handle = fopen($file, 'w');
				fwrite($handle, $content);
				fclose($handle);
				
				return $content;
			}
		} else {
			$content = fread($handle,filesize($file));
			fclose($handle);
			
			return $content;
		}
	}
	
	public function saveHTMLContent($content) 
	{
		$dataPath = Yii::app()->params['data_path'];
		$contentFile = $dataPath . $this->link_id . '.html';
		$handle = @fopen($contentFile, 'w');
		@fwrite($handle, $content);
		fclose($handle);
	}
}