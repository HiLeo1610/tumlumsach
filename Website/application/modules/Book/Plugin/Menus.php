<?php
class Book_Plugin_Menus {
	public function onMenuInitialize_EditQuickCreate($row) {
		$viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }
		
		if (Engine_Api::_()->core()->hasSubject()) {
			$subject = Engine_Api::_()->core()->getSubject();
			if ($subject->getType() == 'book_post') {
				if (!empty($subject->parent_type) && !empty($subject->parent_id)) {
					$subject = $subject->getParent();
				}
			}
			
			// Firstly, just post about book is allowed
			if ($subject->getType() == 'book') {
				$arr = array(
					'route' => 'post',
					'action' => 'create',
					'class' => 'icon_post_new buttonlink',
					'reset_params' => true,
					'icon' => 'application/modules/Book/externals/images/post_create.png',
					'params' => array(
						'parent_type' => $subject->getType(),
						'parent_id' => $subject->getIdentity(),
					)
				);
				return $arr; 
			}
		}
		return array(
			'route' => 'post_general',
			'action' => 'create',
			'class' => 'icon_post_new buttonlink',
			'reset_params' => true,
			'icon' => 'application/modules/Book/externals/images/post_create.png',
		);
	}
	
	public function onMenuInitialize_BookEdit($row) {
		$viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }
        $subject = Engine_Api::_()->core()->getSubject();
		if ($subject && $subject instanceof Book_Model_Book) {
			$owner = $subject->getOwner();
			if (!$owner->isSelf($viewer) 
				&& !$subject->isBookAuthor($viewer)
				&& !$subject->isPublisher($viewer)
				&& !$subject->isBookCompany($viewer)
				&& $viewer->isAdmin() == 0) {
				return false;
			}
				
			return array(
				'label' => 'Edit this book',
				'route' => 'book_specific',
				'action' => 'edit',
				'class' => 'buttonlink book_edit_icon',
				'params' => array(
					'id' => $subject->getIdentity()
				)				
			);
		}
	}
	
	public function onMenuInitialize_BookUploadPhoto($row) {
		$viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        $subject = Engine_Api::_()->core()->getSubject();
		if ($subject && $subject instanceof Book_Model_Book) {
			return array(
				'label' => 'Upload photo',
				'route' => 'book_specific',
				'action' => 'upload-photo',
				'class' => 'buttonlink smoothbox book_upload_icon',
				'params' => array(
					'id' => $subject->getIdentity()
				)				
			);
		}
	}
	
	public function onMenuInitialize_BookQuickCreate($row) {
		$viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }
		
		return array(
			'route' => 'book_general',
			'action' => 'create',
			'class' => 'icon_book_new buttonlink',
			'reset_params' => true,
			'icon' => 'application/modules/Book/externals/images/book_create.png',
			
		);
	}
	
	public function onMenuInitialize_BookDelete($row) {
		$viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        $subject = Engine_Api::_()->core()->getSubject();
		if ($subject && $subject instanceof Book_Model_Book) {
			if ($subject->isOwner($viewer)) {
				return array(
					'label' => 'Delete this book',
					'route' => 'book_specific',
					'action' => 'delete',
					'class' => 'buttonlink smoothbox book_delete_icon',
					'params' => array(
						'id' => $subject->getIdentity()
					)				
				);
			}
			return false;
		}
	}
	
	public function onMenuInitialize_BookFavorite($row) {
		$viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        $subject = Engine_Api::_()->core()->getSubject();
		if ($subject && $subject instanceof Book_Model_Book) {
			$favRow = Engine_Api::_()->book()->isFavorite($viewer, $subject);
			if ($favRow != NULL) {
				$arr = array(
					'label' => 'Remove favorite',
                    'route' => 'book_specific',
                	'action' => 'remove-favorite',
					'class' => 'smoothbox buttonlink remove_favorite',                	
                    'params' => array(
                    	'id' => $subject->getIdentity(),
					)                    
				);
			} else {
				$arr = array(
					'label' => 'Add to favorite',
                    'route' => 'book_specific',
                	'action' => 'favorite',
                	'class' => 'smoothbox buttonlink add_favorite',
                    'params' => array(
                    	'id' => $subject->getIdentity(),
					)
				);
			}
			return $arr;
		}
	}
}	