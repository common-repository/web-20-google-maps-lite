<?php if ($sticky_scroll || $height == '100%'): ?>
<script>
	(function($) {
		"use strict";
	
		$(function() {
			<?php if ($sticky_scroll): ?>
			$("#w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>").width($("#w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>").parent().width()).css({ 'z-index': 100 });
			
			$("#w2gm-maps-canvas-background-<?php echo $unique_map_id; ?>").position().left = $("#w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>").position().left;
			$("#w2gm-maps-canvas-background-<?php echo $unique_map_id; ?>").position().top = $("#w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>").position().top;
			$("#w2gm-maps-canvas-background-<?php echo $unique_map_id; ?>").width($("#w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>").width());
			$("#w2gm-maps-canvas-background-<?php echo $unique_map_id; ?>").height($("#w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>").height());
	
			window.a = function() {
				var b = $(document).scrollTop();
				var d = $("#scroller_anchor_<?php echo $unique_map_id; ?>").offset().top-<?php echo $sticky_scroll_toppadding; ?>;
				var c = $("#w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>");
				var e = $("#w2gm-maps-canvas-background-<?php echo $unique_map_id; ?>");
	
				// .scroller_bottom - this is special class used to restrict the area of scroll of map canvas
				if ($(".scroller_bottom").length)
					var f = $(".scroller_bottom").offset().top-($("#w2gm-maps-canvas-<?php echo $unique_map_id; ?>").height()+<?php echo $sticky_scroll_toppadding; ?>);
				else
					var f = $(document).height();
	
				if (b>d && b<f) {
					c.css({ position: "fixed", top: "<?php echo $sticky_scroll_toppadding; ?>px" });
					e.css({ position: "relative" });
				} else {
					if (b<=d) {
						c.css({ position: "relative", top: "" });
						e.css({ position: "absolute" });
					}
					if (b>=f) {
						c.css({ position: "absolute" });
						c.offset({ top: f });
						e.css({ position: "absolute" });
					}
				}
			};
			$(window).scroll(a);
			a();
			$("#w2gm-maps-canvas-background-<?php echo $unique_map_id; ?>").css({ position: "absolute" });
			<?php endif; ?>
	
			<?php if ($height == '100%'): ?>
			$('#w2gm-maps-canvas-<?php echo $unique_map_id; ?>').height(function(index, height) {
				return window.innerHeight - $('#scroller_anchor_<?php echo $unique_map_id; ?>').outerHeight(true) - <?php echo $sticky_scroll_toppadding; ?>;
			});
			$(window).resize(function(){
				$('#w2gm-maps-canvas-<?php echo $unique_map_id; ?>').height(function(index, height) {
					return window.innerHeight - $('#scroller_anchor_<?php echo $unique_map_id; ?>').outerHeight(true) - <?php echo $sticky_scroll_toppadding; ?>;
				});
			});
			<?php endif; ?>
		});
	})(jQuery);
</script>
<?php endif; ?>

<div class="w2gm-content">
<?php if (!$show_directions): ?>
<?php w2gm_renderTemplate('frontend/frontpanel_buttons.tpl.php'); ?>
<?php endif; ?>

<?php if (!$static_image): ?>
	<script>
		w2gm_map_markers_attrs_array.push(new w2gm_map_markers_attrs('<?php echo $unique_map_id; ?>', eval(<?php echo $locations_options; ?>), <?php echo ($enable_radius_circle) ? 1 : 0; ?>, <?php echo ($enable_clusters) ? 1 : 0; ?>, <?php echo ($show_directions_button) ? 1 : 0; ?>, <?php echo ($show_readmore_button) ? 1 : 0; ?>, '<?php echo esc_js($map_style_name); ?>', <?php echo ($enable_full_screen) ? 1 : 0; ?>, <?php echo ($enable_wheel_zoom) ? 1 : 0; ?>, <?php echo ($enable_dragging_touchscreens) ? 1 : 0; ?>, <?php echo ($center_map_onclick) ? 1 : 0; ?>, <?php echo $map_args; ?>));
	</script>

	<?php if ($sticky_scroll || $height == '100%'): ?>
	<div id="scroller_anchor_<?php echo $unique_map_id; ?>"></div> 
	<?php endif; ?>

	<div id="w2gm-maps-canvas-wrapper-<?php echo $unique_map_id; ?>" class="w2gm-maps-canvas-wrapper">
		<div id="w2gm-maps-canvas-<?php echo $unique_map_id; ?>" class="w2gm-maps-canvas" data-shortcode-hash="<?php echo $unique_map_id; ?>" style="<?php if ($enable_listings_sidebar): if ($listings_position == 'left'): ?>margin-left: <?php echo $listings_width; ?>px;<?php elseif ($listings_position == 'right'): ?>margin-right: <?php echo $listings_width; ?>px;<?php endif; endif; ?> <?php if ($width) echo 'max-width:' . $width . 'px'; else echo 'width: auto'; ?>; height: <?php if ($height) echo $height; else echo '300'; ?>px"></div>
		<?php if ($enable_listings_sidebar): ?>
		<div id="w2gm-maps-listings-wrapper-<?php echo $unique_map_id; ?>" class="w2gm-maps-listings-wrapper<?php if ($listings_position == 'right'): ?>-right<?php endif; ?>" data-shortcode-hash="<?php echo $unique_map_id; ?>" style="width: <?php echo $listings_width; ?>px; height: <?php if ($height) echo $height; else echo '300'; ?>px;">
			<?php echo $listings_content; ?>
		</div>
		<?php endif; ?>
	</div>

	<?php if ($sticky_scroll): ?>
	<div id="w2gm-maps-canvas-background-<?php echo $unique_map_id; ?>" style="position: relative"></div>
	<?php endif; ?>
	
	<?php if ($show_directions): ?>
	<div class="w2gm-row w2gm-form-group">
		<?php if (get_option('w2gm_directions_functionality') == 'builtin'): ?>
		<label class="w2gm-col-md-12 w2gm-control-label"><?php _e('Get directions from:', 'W2GM'); ?></label>
		<script>
			jQuery(document).ready(function($) {
				<?php if (get_option('w2gm_address_geocode')): ?>
				$(".w2gm-get-location-<?php echo $unique_map_id; ?>").click(function() { w2gm_geocodeField($("#from_direction_<?php echo $unique_map_id; ?>"), "<?php echo esc_js(__('GeoLocation service does not work on your device!', 'W2GM')); ?>"); });
				<?php endif; ?>
			});
		</script>
		<?php if (get_option('w2gm_address_geocode')): ?>
		<div class="w2gm-col-md-12 w2gm-has-feedback">
			<input type="text" id="from_direction_<?php echo $unique_map_id; ?>" class="w2gm-form-control <?php if (get_option('w2gm_address_autocomplete')): ?>w2gm-field-autocomplete<?php endif; ?>" placeholder="<?php esc_attr_e('Enter address or zip code', 'W2GM'); ?>" />
			<span class="w2gm-get-location w2gm-get-location-<?php echo $unique_map_id; ?> w2gm-glyphicon w2gm-glyphicon-screenshot w2gm-form-control-feedback" title="<?php esc_attr_e('Get my location', 'W2GM'); ?>"></span>
		</div>
		<?php else: ?>
		<div class="w2gm-col-md-12">
			<input type="text" id="from_direction_<?php echo $unique_map_id; ?>" placeholder="<?php esc_attr_e('Enter address or zip code', 'W2GM'); ?>" class="w2gm-form-control" />
		</div>
		<?php endif; ?>
		<div class="w2gm-col-md-12">
			<?php $i = 1; ?>
			<?php foreach ($locations_array AS $location): ?>
			<div class="w2gm-radio">
				<label>
					<input type="radio" name="select_direction" class="select_direction_<?php echo $unique_map_id; ?>" <?php checked($i, 1); ?> value="<?php echo $location->map_coords_1.' '.$location->map_coords_2; ?>" />
					<?php 
					if ($address = $location->getWholeAddress(false))
						echo $address;
					else 
						echo $location->map_coords_1.' '.$location->map_coords_2;
					?>
				</label>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="w2gm-col-md-12">
			<input type="button" class="direction_button front-btn w2gm-btn w2gm-btn-primary" id="get_direction_button_<?php echo $unique_map_id; ?>" value="<?php esc_attr_e('Get directions', 'W2GM'); ?>">
		</div>
		<div class="w2gm-col-md-12">
			<div id="route_<?php echo $unique_map_id; ?>" class="w2gm-maps-direction-route"></div>
		</div>
		<?php elseif (get_option('w2gm_directions_functionality') == 'google'): ?>
		<label class="w2gm-col-md-12 w2gm-control-label"><?php _e('directions to:', 'W2GM'); ?></label>
		<form action="//maps.google.com" target="_blank">
			<input type="hidden" name="saddr" value="Current Location" />
			<div class="w2gm-col-md-12">
				<?php $i = 1; ?>
				<?php foreach ($locations_array AS $location): ?>
				<div class="w2gm-radio">
					<label>
						<input type="radio" name="daddr" class="select_direction_<?php echo $unique_map_id; ?>" <?php checked($i, 1); ?> value="<?php echo $location->map_coords_1.','.$location->map_coords_2; ?>" />
						<?php 
						if ($address = $location->getWholeAddress(false))
							echo $address;
						else 
							echo $location->map_coords_1.' '.$location->map_coords_2;
						?>
					</label>
				</div>
				<?php endforeach; ?>
			</div>
			<div class="w2gm-col-md-12">
				<input class="w2gm-btn w2gm-btn-primary" type="submit" value="<?php esc_attr_e('Get directions', 'W2GM'); ?>" />
			</div>
		</form>
		<?php endif; ?>
	</div>
	<?php endif; ?>
<?php else: ?>
	<img src="//maps.googleapis.com/maps/api/staticmap?size=795x350&<?php foreach ($locations_array  AS $location) { if ($location->map_coords_1 != 0 && $location->map_coords_2 != 0) { ?>markers=<?php if (W2GM_MAP_ICONS_URL && $location->map_icon_file) { ?>icon:<?php echo W2GM_MAP_ICONS_URL . 'icons/' . urlencode($location->map_icon_file) . '%7C'; }?><?php echo $location->map_coords_1 . ',' . $location->map_coords_2 . '&'; }} ?><?php if ($map_zoom) echo 'zoom=' . $map_zoom; ?><?php if (get_option('w2gm_google_api_key')) echo '&key='.get_option('w2gm_google_api_key'); ?>" />
<?php endif; ?>
</div>