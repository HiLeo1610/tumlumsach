<?php
class Book_Api_Core extends Core_Api_Abstract {
    const DEFAULT_LIMIT = 200;

    public function getBooksSelect($params = array(), $order_by = true) {
        $table = new Book_Model_DbTable_Books;
        $select = $table->getSelect();

        if (!empty($params['orderby']))
        {
            $select->order($params['orderby']);
        }
        if (!empty($params['text']))
        {
            $select->where("book_name LIKE ?", "%{$params['text']}%");
        }

        return $select;
    }

    public function getBooksPaginator($params = array(), $order_by = true) {
        $paginator = Zend_Paginator::factory($this->getBooksSelect($params, $order_by));
        if (!empty($params['page']))
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit']))
        {
            $paginator->setItemCountPerPage($params['limit']);
        }

        return $paginator;
    }

    public function getRatingCount($item) {
        $table = new Book_Model_DbTable_Ratings();
        $select = $table->select()->where('parent_object_id = ?', $item->getIdentity())->where('parent_object_type', $item->getType());
        $row = $table->fetchAll($select);
        $total = count($row);

        return $total;
    }

    public function checkRated($object_id, $object_type, $user_id) {
        if (!empty($user_id))
        {
            $ratingTable = new Book_Model_DbTable_Ratings();
            $select = $ratingTable->select()->where('parent_object_type = ?', $object_type)->where('parent_object_id = ?', $object_id)->where('user_id = ?', $user_id);
            return ($ratingTable->fetchRow($select) != NULL);
        }

        return NULL;
    }

    public function getObject($type, $id) {
        $object = Engine_Api::_()->getItem($type, $id);
        if ($object) {
            $signatureTbl = new Book_Model_DbTable_Signatures;
            $signatureSel = $signatureTbl->select();
            $signatureSel->where('parent_object_type = ?', $type)->where('parent_object_id = ?', $id);
            $signature = $signatureTbl->fetchRow($signatureSel);
            if ($signature == NULL) {
                $signature = $signatureTbl->createRow(array(
                        'parent_object_id' => $id,
                        'parent_object_type' => $type,
                        'favorite_count' => 0,
                        'view_count' => 0
                ));
                $signature->save();
            }

            $itemTbl = Engine_Api::_()->getItemTable($type);
            $itemTblName = $itemTbl->info(Zend_Db_Table_Abstract::NAME);
            $select = $itemTbl->getSelect('*');
            $tablePrimaryKey = current($itemTbl->info(Zend_Db_Table_Abstract::PRIMARY));
            $select->where("$itemTblName.$tablePrimaryKey = ?", $id);
            return $itemTbl->fetchRow($select);
        }
    }

    public function getTotalBookCount($params = NULL) {
        $bookTbl = new Book_Model_DbTable_Books;
        return $bookTbl->getTotalCount($params);
    }

    public function isFavorite($user, $object) {
        $favTbl = new Book_Model_DbTable_Favorites;
        $select = $favTbl->select()->where('user_id = ?', $user->getIdentity());
        $select->where('parent_object_id = ?', $object->getIdentity());
        $select->where('parent_object_type = ?', $object->getType());

        return $favTbl->fetchRow($select);
    }

    public function importRawPosts() {
        try {
            $postTbl = new Book_Model_DbTable_Posts();
            $select = $postTbl->select();
            $select->from($postTbl->info('name'), new Zend_Db_Expr('MAX(`rawpost_id`) as max_rawpost_id'));
            $data = $select->query()->fetch();
            $maxRawpostId = (int)$data['max_rawpost_id'];

            $bookTbl = new Book_Model_DbTable_Books();
            	
            $rawPostTbl = new Book_Model_DbTable_Rawposts();
            $rawPostSelect = $rawPostTbl->select();
            $rawPostSelect->where('rawpost_id > ?', $maxRawpostId);
            	
            $rawBookTbl = new Book_Model_DbTable_Rawbooks();
            	
            foreach ($rawPostTbl->fetchAll($rawPostSelect) as $rawPost) {
                $data = array(
                        'post_name' => $rawPost->name,
                        'content' => $rawPost->content,
                        'user_id' => 1, //superadmin
                        'rawpost_id' => $rawPost->rawpost_id
                );
                if (!empty($rawPost->book_link_id)) {
                    $book = $rawBookTbl->getBookFromBookLinkId($rawPost->book_link_id);
                    	
                    if (!empty($book)) {
                        $data['parent_type'] = $book->getType();
                        $data['parent_id'] = $book->getIdentity();
                    }
                }

                $post = $postTbl->createRow($data);
                $post->save();
            }
            	
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function importRawBooks() {
        try {
            $bookTbl = new Book_Model_DbTable_Books();
            $select = $bookTbl->select();
            $select->from($bookTbl->info('name'), new Zend_Db_Expr('MAX(`rawbook_id`) as max_rawbook_id'));
            $data = $select->query()->fetch();
            $maxRawbookId = (int)$data['max_rawbook_id'];

            $userTbl = new User_Model_DbTable_Users();

            $rawBookTbl = new Book_Model_DbTable_Rawbooks();
            $rawBookSelect = $rawBookTbl->select();
            $rawBookSelect->where('rawbook_id > ?', $maxRawbookId);
            $rawBookSelect->order('rawbook_id ASC');
            $rawBookSelect->limit(self::DEFAULT_LIMIT);
            	
            $rawBooks = $rawBookTbl->fetchAll($rawBookSelect);
            foreach ($rawBooks as $rawBook) {
                if (!empty($rawBook['publisher'])) {
                    $publisherSelect = $userTbl->select()->where('displayname LIKE ?', $rawBook['publisher']);
                    $publisher = $userTbl->fetchRow($publisherSelect);
                }
                	
                if (!empty($rawBook['book_company'])) {
                    $bookCompanySelect = $userTbl->select()->where('displayname LIKE ?', $rawBook['book_company']);
                    $bookCompany = $userTbl->fetchRow($bookCompanySelect);
                }

                $data = array(
                        'book_name' => $rawBook->book_name,
                        'published_date' => date('Y-m-d H:i:s', $rawBook->published_date),
                        'price' => $rawBook->price,
                        'num_page' => $rawBook->num_page,
                        'description' => $rawBook->description,
                        'rawbook_id' => $rawBook->getIdentity(),
                        'user_id' => 1 //superadmin
                );
                	
                if (isset($publisher) && !empty($publisher)) {
                    $data['publisher_id'] = $publisher->getIdentity();
                }
                	
                if (isset($bookCompany) && !empty($bookCompany)) {
                    $data['book_company_id'] = $bookCompany->getIdentity();
                }
                	
                $book = $bookTbl->createRow($data);
                $book->save();
                	
                if (!empty($rawBook['photo'])) {
                    $image = Engine_Image::factory();

                    $name = basename($rawBook['photo']);
                    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
                    $params = array(
                            'parent_id' => $book->getIdentity(),
                            'parent_type' => $book->getType()
                    );

                    // Save
                    $storage = Engine_Api::_()->storage();

                    $image->open($rawBook['photo'])
                    ->write($path . '/m_' . $name)
                    ->destroy();

                    // Store
                    $iMain = $storage->create($path . '/m_' . $name, $params);

                    // Remove temp files
                    @unlink($path . '/m_' . $name);

                    $book->photo_id = $iMain->getIdentity();
                    $book->save();

                    $photoTbl = new Book_Model_DbTable_Photos();
                    $photo = $photoTbl->createRow(array(
                            'parent_object_type' => $book->getType(),
                            'parent_object_id' => $book->getIdentity(),
                            'file_id' => $iMain->getIdentity(),
                            'user_id' => 1, // superadmin
                            'approved' => 1,
                            'default' => 1
                    ));
                    $photo->save();
                }
            }

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
