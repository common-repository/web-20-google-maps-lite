<form method="POST" action="">
	<input type="hidden" name="action" value="import_settings">
	<input type="hidden" name="csv_file_name" value="<?php echo esc_attr($csv_file_name); ?>">
	<input type="hidden" name="images_dir" value="<?php echo esc_attr($images_dir); ?>">
	<input type="hidden" name="columns_separator" value="<?php echo esc_attr($columns_separator); ?>">
	<input type="hidden" name="values_separator" value="<?php echo esc_attr($values_separator); ?>">
	<input type="hidden" name="if_term_not_found" value="<?php echo esc_attr($if_term_not_found); ?>">
	<input type="hidden" name="listings_author" value="<?php echo esc_attr($listings_author); ?>">
	<input type="hidden" name="do_geocode" value="<?php echo esc_attr($do_geocode); ?>">
	<?php foreach ($fields AS $field): ?>
	<input type="hidden" name="fields[]" value="<?php echo esc_attr($field); ?>">
	<?php endforeach; ?>
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_csv_import_nonce');?>

	<?php if ($log['errors'] || $test_mode): ?>
	<?php submit_button(__('Go back', 'W2GM'), 'primary', 'goback', false); ?>
	&nbsp;&nbsp;&nbsp;
	<?php endif; ?>

	<a href="<?php echo admin_url('admin.php?page=w2gm_csv_import'); ?>" class="button button-primary"><?php _e('Import new file', 'W2GM'); ?></a>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>