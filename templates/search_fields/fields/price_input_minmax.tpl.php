<?php if (count($search_field->min_max_options)): ?>
<?php if ($columns == 1) $col_md = 12; else $col_md = 6; ?>
<div class="w2gm-row w2gm-field-search-block-<?php echo $search_field->content_field->id; ?> w2gm-field-search-block-<?php echo $random_id; ?> w2gm-field-search-block-<?php echo $search_field->content_field->id; ?>_<?php echo $random_id; ?>">
	<div class="w2gm-col-md-12">
		<label><?php echo $search_field->content_field->name; ?> <?php echo $search_field->content_field->currency_symbol; ?></label>
	</div>

	<div class="w2gm-col-md-<?php echo $col_md; ?> w2gm-form-group">
		<select name="field_<?php echo $search_field->content_field->slug; ?>_min" class="w2gm-form-control">
		<option value=""><?php _e('- Select min -', 'W2GM'); ?></option>
		<?php foreach ($search_field->min_max_options AS $item): ?>
			<?php if (is_numeric($item)): ?>
			<option value="<?php echo $item; ?>" <?php selected($search_field->min_max_value['min'], $item); ?>><?php echo number_format($item, 2, $search_field->content_field->decimal_separator, $search_field->content_field->thousands_separator); ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>
	</div>

	<div class="w2gm-col-md-<?php echo $col_md; ?> w2gm-form-group">
		<select name="field_<?php echo $search_field->content_field->slug; ?>_max" class="w2gm-form-control">
		<option value=""><?php _e('- Select max -', 'W2GM'); ?></option>
		<?php foreach ($search_field->min_max_options AS $item): ?>
			<?php if (is_numeric($item)): ?>
			<option value="<?php echo $item; ?>" <?php selected($search_field->min_max_value['max'], $item); ?>><?php echo number_format($item, 2, $search_field->content_field->decimal_separator, $search_field->content_field->thousands_separator); ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>
	</div>
</div>
<?php endif; ?>