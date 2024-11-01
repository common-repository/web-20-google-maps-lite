<div class="w2gm-row w2gm-field-search-block-<?php echo $search_field->content_field->id; ?> w2gm-field-search-block-<?php echo $random_id; ?> w2gm-field-search-block-<?php echo $search_field->content_field->id; ?>_<?php echo $random_id; ?>">
	<div class="w2gm-col-md-12">
		<label><?php echo $search_field->content_field->name; ?></label>
	</div>
	<div class="w2gm-col-md-12 w2gm-form-group">
		<input type="text" class="w2gm-form-control" name="field_<?php echo $search_field->content_field->slug; ?>" value="<?php echo esc_attr($search_field->value); ?>" />
	</div>
</div>