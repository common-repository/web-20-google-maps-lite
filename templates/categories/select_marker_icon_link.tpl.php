<div>
	<span class="w2gm-marker-icon-tag <?php if ($icon_name): ?>w2gm-fa <?php echo esc_attr($icon_name); ?><?php endif; ?>"></span>
	<input type="hidden" name="marker_icon_image" class="marker_icon_image" value="<?php echo esc_attr($icon_name); ?>" />
	<input type="hidden" name="category_id" class="category_id" value="<?php echo esc_attr($term_id); ?>" />
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_marker_icon_image_nonce');?>
	<a class="select_marker_icon_image" href="javascript: void(0);"><?php _e('Select marker', 'W2GM'); ?></a>
</div>