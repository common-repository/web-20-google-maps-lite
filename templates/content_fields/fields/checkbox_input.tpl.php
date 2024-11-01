<?php if (count($content_field->selection_items)): ?>
<div class="w2gm-form-group w2gm-field w2gm-field-input-block w2gm-field-input-block-<?php echo $content_field->id; ?>">
	<label class="w2gm-col-md-2 w2gm-control-label"><?php echo $content_field->name; ?><?php if ($content_field->canBeRequired() && $content_field->is_required): ?><span class="w2gm-red-asterisk">*</span><?php endif; ?></label>
	<div class="w2gm-col-md-10">
		<?php foreach ($content_field->selection_items AS $key=>$item): ?>
		<div class="w2gm-checkbox">
			<label>
				<input type="checkbox" name="w2gm-field-input-<?php echo $content_field->id; ?>[]" class="w2gm-field-input-checkbox" value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $content_field->value)) echo 'checked'; ?> />
				<?php echo $item; ?>
			</label>
		</div>
		<?php endforeach; ?>
		<?php if ($content_field->description): ?><p class="description"><?php echo $content_field->description; ?></p><?php endif; ?>
	</div>
</div>
<?php endif; ?>