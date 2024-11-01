<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Locations levels', 'W2GM'); ?>
	<?php echo sprintf('<a class="add-new-h2" href="?page=%s&action=%s">' . __('Create new locations level', 'W2GM') . '</a>', $_GET['page'], 'add'); ?>
</h2>

<?php $locations_levels_table->display(); ?>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>