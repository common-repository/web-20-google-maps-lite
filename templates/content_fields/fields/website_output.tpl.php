<?php if ($content_field->value['url']): ?>
<div class="w2gm-field w2gm-field-output-block w2gm-field-output-block-<?php echo $content_field->type; ?> w2gm-field-output-block-<?php echo $content_field->id; ?>">
	<?php if ($content_field->icon_image || !$content_field->is_hide_name): ?>
	<span class="w2gm-field-caption">
		<?php if ($content_field->icon_image): ?>
		<span class="w2gm-field-icon w2gm-fa w2gm-fa-lg <?php echo $content_field->icon_image; ?>"></span>
		<?php endif; ?>
		<?php if (!$content_field->is_hide_name): ?>
		<span class="w2gm-field-name"><?php echo $content_field->name?>:</span>
		<?php endif; ?>
	</span>
	<?php endif; ?>
	<span class="w2gm-field-content">
		<a
			href="<?php echo esc_url($content_field->value['url']); ?>"
			<?php if ($content_field->is_blank) echo 'target="_blank"'; ?>
			<?php if ($content_field->is_nofollow) echo 'rel="nofollow"'; ?>
		><?php if ($content_field->value['text'] && $content_field->use_link_text) echo $content_field->value['text']; else echo $content_field->value['url']; ?></a>
	</span>
</div>
<?php endif; ?>