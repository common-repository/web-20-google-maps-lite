<?php if (has_term('', W2GM_CATEGORIES_TAX, $listing->post->ID)): ?>
<div class="w2gm-field-output-block w2gm-field-output-block-<?php echo $content_field->type; ?> w2gm-field-output-block-<?php echo $content_field->id; ?>">
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
	<?php //echo get_the_term_list($listing->post->ID, W2GM_CATEGORIES_TAX, '', ', ', ''); ?>
		<?php
		$terms = get_the_terms($listing->post->ID, W2GM_CATEGORIES_TAX);
		foreach ($terms as $term):?>
			<span class="w2gm-label w2gm-label-primary"><?php echo $term->name; ?>&nbsp;&nbsp;<span class="w2gm-glyphicon w2gm-glyphicon-tag"></span></span>
		<?php endforeach; ?>
	</span>
</div>
<?php endif; ?>