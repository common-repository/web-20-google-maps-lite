<div class="w2gm-form-group w2gm-field w2gm-field-input-block w2gm-field-input-block-<?php echo $content_field->id; ?>">
	<label class="w2gm-col-md-2 w2gm-control-label"><?php echo $content_field->name; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2gm-red-asterisk">*</span><?php endif; ?></label>
	<div class="w2gm-col-md-10">
		<div class="w2gm-row">
			<div class="w2gm-col-md-12">
				<label><?php _e('URL:', 'W2GM'); ?></label>
				<input type="text" name="w2gm-field-input-url_<?php echo $content_field->id; ?>" class="w2gm-field-input-url w2gm-form-control regular-text" value="<?php echo esc_url($content_field->value['url']); ?>" />
			</div>
			<?php if ($content_field->use_link_text): ?>
			<div class="w2gm-col-md-12">
				<label><?php _e('Link text:', 'W2GM'); ?></label>
				<input type="text" name="w2gm-field-input-text_<?php echo $content_field->id; ?>" class="w2gm-field-input-text w2gm-form-control regular-text" value="<?php echo esc_attr($content_field->value['text']); ?>" />
			</div>
			<?php endif; ?>
		</div>
		<?php if ($content_field->description): ?><p class="description"><?php echo $content_field->description; ?></p><?php endif; ?>
	</div>
</div>