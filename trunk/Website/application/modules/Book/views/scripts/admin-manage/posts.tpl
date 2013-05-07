<script type="text/javascript">
</script>

<h2>
    <?php echo $this->translate("Books Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<?php if (count($this->paginator)): ?>
	<div class="book_item_count">
		<?php
			$totalItemCount = $this->paginator->getTotalItemCount();
			echo $this->translate(array('%s post', '%s posts', $totalItemCount), $this->locale()->toNumber($totalItemCount));
		?>
	</div>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <table class='admin_table admin_book_table'>
            <thead>
                <tr>
                    <th class='admin_table_short'>
                        <input onclick='selectAll();' type='checkbox' class='checkbox' />
                    </th>
                    <th class='admin_table_short'>
                        <a href="javascript:void(0);" onclick="">ID</a>
                    </th>
                    <th>
                        <a href="javascript:void(0);" onclick=""><?php echo $this->translate("Name") ?></a>
                    </th>
                    <th>
                        <a href="javascript:void(0);"><?php echo $this->translate("Parent") ?></a>
                    </th>
                    <th>
                        <a href="javascript:void(0);" onclick=""><?php echo $this->translate("User") ?></a>
                    </th>
                    <th class="center">
                        <a href="javascript:void(0);" onclick=""><?php echo $this->translate("Rating Count") ?></a>
                    </th>
                    <th class="center">
                        <a href="javascript:void(0);" onclick=""><?php echo $this->translate("Rating") ?></a>
                    </th>
                    <th class="center">
                        <a href="javascript:void(0);" onclick=""><?php echo $this->translate("View Count") ?></a>
                    </th>
                    <th class="center">
                        <a href="javascript:void(0);" onclick=""><?php echo $this->translate("Favorite Count") ?></a>
                    </th>
                    <th>
                        <a href="javascript:void(0);" onclick=""><?php echo $this->translate("Creation Date") ?></a>
                    </th>
                    <th><?php echo $this->translate("Options") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->paginator as $item): ?>
                    <tr>
                        <td>
                        	<input type='checkbox' class='checkbox' name='id[]' value='<?php echo $item->getIdentity() ?>' />
                        </td>
                        <td><?php echo $item->getIdentity() ?></td>
                        <td><?php echo $item?></td>
                        <td><?php echo $item->getParentObject();?></td>
                        <td><?php echo $item->getOwner();?></td>
                        <td class="center"><?php echo $item->rating_count?></td>
                        <td class="center"><?php echo $item->rating?></td>
                        <td class="center"><?php echo $item->view_count?></td>
                        <td class="center"><?php echo $item->favorite_count?></td>
                        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                        <td>
                        	<a href=""><?php echo $this->translate('delete')?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <br />
	    <div>
	        <?php
	            echo $this->paginationControl($this->paginator, null, null, array(
	                'pageAsQuery' => true,
	                'query' => $this->params,
	            ));
	        ?>
	    </div>

        <br />

        <div class='buttons'>
            <button type='submit' value='deletes' name="action">
                <?php echo $this->translate("Delete Selected") ?>
            </button>
            <button type="submit" value="import-posts" name="action">
            	<?php echo $this->translate("Import Data") ?>
            </button>            
        </div>
    </form>

    

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no books posted by your members yet.") ?>
        </span>
    </div>
<?php endif; ?>