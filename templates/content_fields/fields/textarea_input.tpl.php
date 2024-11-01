<div class="w2gm-form-group w2gm-field w2gm-field-input-block w2gm-field-input-block-<?php echo $content_field->id; ?>">
	<label class="w2gm-col-md-2 w2gm-control-label"><?php echo $content_field->name; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2gm-red-asterisk">*</span><?php endif; ?></label>
	<div class="w2gm-col-md-10">
		<?php if ($content_field->html_editor): ?>
		<?php wp_editor($content_field->value, 'w2gm-field-input-'.$content_field->id, array('media_buttons' => false, 'editor_class' => 'w2gm-editor-class')); ?>
		<?php else: ?>
		<textarea name="w2gm-field-input-<?php echo $content_field->id; ?>" class="w2gm-field-input-textarea w2gm-form-control" rows="5"><?php echo esc_textarea($content_field->value); ?></textarea>
		<?php endif; ?>
		<?php if ($content_field->description): ?><p class="description"><?php echo $content_field->description; ?></p><?php endif; ?>
	</div>
</div>