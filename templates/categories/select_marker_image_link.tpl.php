<div>
	<img class="w2gm-marker-image-png-tag w2gm-field-icon" src="<?php if ($image_png_name) echo esc_url(W2GM_MAP_ICONS_URL . 'icons/' . $image_png_name); ?>" <?php if (!$image_png_name): ?>style="display: none;" <?php endif; ?> />
	<input type="hidden" name="marker_png_image" class="marker_png_image" value="<?php if ($image_png_name) echo esc_attr($image_png_name); ?>">
	<input type="hidden" name="category_id" class="category_id" value="<?php echo esc_attr($term_id); ?>">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_marker_png_image_nonce');?>
	<a class="select_marker_png_image" href="javascript: void(0);"><?php _e('Select image', 'W2GM'); ?></a>
</div>