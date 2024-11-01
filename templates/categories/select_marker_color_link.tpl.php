<div class="w2gm-content">
	<input type="text" name="marker_color" class="marker_color" value="<?php echo esc_attr($color); ?>" />
	<input type="hidden" name="category_id" class="category_id" value="<?php echo esc_attr($term_id); ?>" />
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_marker_color_nonce');?>
	<input type="button" name="save_color" class="save_color button button-primary" value="<?php esc_attr_e('Save Color', 'W2GM'); ?>" />
</div>