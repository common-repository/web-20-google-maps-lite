<?php if ($listing->locations): ?>
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
	<span class="w2gm-field-content w2gm-field-addresses">
	<?php foreach ($listing->locations AS $location): ?>
		<address class="w2gm-location">
			<?php if ($location->map_coords_1 && $location->map_coords_2): ?><span class="w2gm-show-on-map" data-location-id="<?php echo $location->id; ?>"><?php endif; ?>
			<?php echo $location->getWholeAddress(); ?>
			<?php if ($location->map_coords_1 && $location->map_coords_2): ?></span><?php endif; ?>
		</address>
	<?php endforeach; ?>
	</span>
</div>
<?php endif; ?>