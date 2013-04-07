<h2><?php echo $this->translate("Books Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='book_clear'>
    <div class='settings'>
        <form class="global_form">
            <div>
                <h3><?php echo $this->translate("Book Categories") ?></h3>

                <?php if (count($this->categories) > 0): ?>

                    <table class='admin_table'>
                        <thead>

                            <tr>
                                <th><?php echo $this->translate("Category Name") ?></th>
                                <th><?php echo $this->translate("Number of Times Used") ?></th>
                                <th><?php echo $this->translate("Options") ?></th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php foreach ($this->categories as $category): ?>
                                <?php if ($category->parent_id == 0) : ?>
                                    <tr>
                                        <td>
                                            <?php if(count($category->getSubCategories()) > 0) : ?>
                                                <span class="book-category-collapse-control book-category-collapsed"></span>
                                            <?php else : ?>
                                                <span class="book-category-collapse-nocontrol"></span>
                                            <?php endif; ?>
                                            <?php echo $category->category_name ?>
                                        </td>
                                        <td><?php echo $category->getUsedCount() ?></td>
                                        <td>
                                            <?php
                                            echo $this->htmlLink(array('route' => 'default', 'module' => 'video', 'controller' => 'admin-settings', 'action' => 'edit-category', 'id' => $category->category_id), $this->translate('edit'), array(
                                                'class' => 'smoothbox',
                                            ))
                                            ?>
                                            |
                                            <?php
                                            echo $this->htmlLink(array('route' => 'default', 'module' => 'video', 'controller' => 'admin-settings', 'action' => 'delete-category', 'id' => $category->category_id), $this->translate('delete'), array(
                                                'class' => 'smoothbox',
                                            ))
                                            ?>

                                        </td>
                                    </tr>
                                    <?php foreach ($category->getSubCategories() as $subCat) : ?>
                                        <tr class="book-category-sub-category">
                                            <td class="category-name"><?php echo $subCat->category_name ?></td>
                                            <td><?php echo $subCat->getUsedCount() ?></td>
                                            <td>
                                                <?php
                                                echo $this->htmlLink(array('route' => 'default',
                                                        'module' => 'book',
                                                        'controller' => 'admin-settings',
                                                        'action' => 'edit-category',
                                                        'id' => $subCat->category_id),
                                                    $this->translate('edit'), array('class' => 'smoothbox'))
                                                ?>
                                                |
                                                <?php
                                                echo $this->htmlLink(array('route' => 'default',
                                                        'module' => 'book',
                                                        'controller' => 'admin-settings',
                                                        'action' => 'delete-category',
                                                        'id' => $subCat->category_id),
                                                    $this->translate('delete'), array('class' => 'smoothbox'))
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="tip">
                        <span><?php echo $this->translate("There are currently no categories.") ?></span>
                    </div>
                <?php endif; ?>
                <br/>
                <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'book', 'controller' => 'admin-settings', 'action' => 'add-category'),
                        $this->translate('Add New Category'),
                        array(
                            'class' => 'smoothbox buttonlink',
                            'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);'
                        ));
                ?>
            </div>
        </form>
    </div>
</div>