<article class="w2gm-listing-location" id="post-<?php echo $location->id; ?>" data-location-id="<?php echo $location->id; ?>" style="height: auto;">
	<?php
	$logo_image = false;
	if ($listing->logo_image) {
		$src = wp_get_attachment_image_src($listing->logo_image, get_option('w2gm_listings_sidebar_width'), get_option('w2gm_listings_sidebar_width'));
		$logo_image = $src[0];
	} elseif (get_option('w2gm_enable_nologo') && get_option('w2gm_nologo_url')) {
		$logo_image = get_option('w2gm_nologo_url');
	}
	
	if ($logo_image):
	?>
	<div class="w2gm-pull-left w2gm-listing-logo-wrap">
		<figure class="w2gm-listing-logo">
			<div class="w2gm-listing-logo-img-wrap">
				<div style="background-image: url('<?php echo $logo_image; ?>');" class="w2gm-listing-logo-img"></div>
			</div>
		</figure>
	</div>
	<?php endif; ?>
	<div class="w2gm-clearfix w2gm-listing-text-content-wrap">
		<header class="w2gm-listing-header">
			<h2><?php echo $listing->title(); ?></h2>
		</header>
		<?php echo apply_filters('w2gm_map_info_window_fields_values', null, 'rating', $listing); ?>
		<?php
		if ($location->renderInfoFieldForMap())
			echo '<div class="w2gm-field w2gm-field-output-block"><span class="w2gm-field-icon w2gm-fa w2gm-fa-lg w2gm-fa-info-circle"></span> ' . $location->renderInfoFieldForMap() . '</div>';
		?>
		<?php $listing->renderAddressContentField($location); ?>
		<?php $listing->renderSidebarContentFields(); ?>
		
		<?php 
		if ($show_directions_button || $show_readmore_button):
			if (!$show_directions_button || !$show_readmore_button)
				$button_class = 'w2gm-map-info-window-buttons-single';
			else
				$button_class = 'w2gm-map-info-window-buttons';
		?>
		<div class="<?php echo $button_class; ?> w2gm-clearfix">
			<?php if ($show_directions_button): ?>
			<a href="https://www.google.com/maps/dir/Current+Location/<?php echo $location->map_coords_1; ?>,<?php echo $location->map_coords_2; ?>" target="_blank" class="w2gm-btn w2gm-btn-primary"><?php _e('« Directions', 'W2GM'); ?></a>
			<?php endif; ?>
			<?php if ($show_readmore_button): ?>
			<a href="javascript:void(0);" onClick="w2gm_show_listing(<?php echo $location->id; ?>, '<?php echo esc_js($listing->title()); ?>')" class="w2gm-btn w2gm-btn-primary"><?php _e('Read more »', 'W2GM')?></a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</article>