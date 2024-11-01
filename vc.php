<?php

add_action('vc_before_init', 'w2gm_vc_init');

function w2gm_vc_init() {
	global $w2gm_instance, $w2gm_maps_styles;
	
	if (!isset($w2gm_instance->content_fields)) // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		return ;
	
	$map_styles = array('default' => '');
	foreach ($w2gm_maps_styles AS $name=>$style)
		$map_styles[$name] = $name;

	if (!function_exists('w2gm_categories_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		add_shortcode_param('categoriesfield', 'w2gm_categories_param');
		function w2gm_categories_param($settings, $value) {
			$out = "<script>
				function updateTagChecked() { jQuery('#" . $settings['param_name'] . "').val(jQuery('#" . $settings['param_name'] . "_select').val()); }
		
				jQuery(function() {
					jQuery('#" . $settings['param_name'] . "_select option').click(updateTagChecked);
					updateTagChecked();
				});
			</script>";
		
			$out .= '<select multiple="multiple" id="' . $settings['param_name'] . '_select" name="' . $settings['param_name'] . '_select" style="height: 300px">';
			$out .= '<option value="" ' . ((!$value) ? 'selected' : '') . '>' . __('- Select All -', 'W2GM') . '</option>';
			ob_start();
			w2gm_renderOptionsTerms(W2GM_CATEGORIES_TAX, 0, explode(',', $value));
			$out .= ob_get_clean();
			$out .= '</select>';
			$out .= '<input type="hidden" id="' . $settings['param_name'] . '" name="' . $settings['param_name'] . '" class="wpb_vc_param_value" value="' . $value . '" />';
		
			return $out;
		}
	}

	if (!function_exists('w2gm_locations_param')) { // some "unique" themes/plugins call vc_before_init more than ones - this is such protection
		add_shortcode_param('locationsfield', 'w2gm_locations_param');
		function w2gm_locations_param($settings, $value) {
			$out = "<script>
				function updateTagChecked() { jQuery('#" . $settings['param_name'] . "').val(jQuery('#" . $settings['param_name'] . "_select').val()); }
		
				jQuery(function() {
					jQuery('#" . $settings['param_name'] . "_select option').click(updateTagChecked);
					updateTagChecked();
				});
			</script>";
		
			$out .= '<select multiple="multiple" id="' . $settings['param_name'] . '_select" name="' . $settings['param_name'] . '_select" style="height: 300px">';
			$out .= '<option value="" ' . ((!$value) ? 'selected' : '') . '>' . __('- Select All -', 'W2GM') . '</option>';
			ob_start();
			w2gm_renderOptionsTerms(W2GM_LOCATIONS_TAX, 0, explode(',', $value));
			$out .= ob_get_clean();
			$out .= '</select>';
			$out .= '<input type="hidden" id="' . $settings['param_name'] . '" name="' . $settings['param_name'] . '" class="wpb_vc_param_value" value="' . $value . '" />';
		
			return $out;
		}
	}

	$vc_maps_args = array(
			'name'                    => __('Web 2.0 Google Map', 'W2GM'),
			'description'             => __('Google map and markers', 'W2GM'),
			'base'                    => 'webmap',
			'icon'                    => W2GM_RESOURCES_URL . 'images/webmaps.png',
			'show_settings_on_create' => true,
			'category'                => __('Google Maps Content', 'W2GM'),
			'params'                  => array(
					array(
							'type' => 'textfield',
							'param_name' => 'uid',
							'value' => '',
							'heading' => __('Enter unique string to connect this shortcode with specific search block.', 'W2GM'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'ajax_loading',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('Use AJAX loading', 'W2GM'),
							'description' => __('Load map markers using AJAX. The map loads only needed map markers, those visible in the viewport of the map.', 'W2GM'),
							//'std' => (int)get_option('w2gm_ajax_loading'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'ajax_markers_loading',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('InfoWindow AJAX loading', 'W2GM'),
							'description' => __('Load infowindow using AJAX.', 'W2GM'),
							//'std' => (int)get_option('w2gm_ajax_markers_loading'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'num',
							//'value' => -1,
							'heading' => __('Maximum number of listings on map', 'W2GM'),
							'description' => __('Place 0 if you need unlimited.', 'W2GM'),
							//'std' => (int)get_option('w2gm_max_num'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'show_readmore_button',
							'value' => array(__('Yes', 'W2GM') => '1', __('No', 'W2GM') => '0'),
							'heading' => __('Show read more button in InfoWindow?', 'W2GM'),
							//'std' => (int)get_option('w2gm_show_readmore_button'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'show_directions_button',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('Show directions button in InfoWindow?', 'W2GM'),
							//'std' => (int)get_option('w2gm_show_directions_button'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'start_address',
							'heading' => __('Starting Address', 'W2GM'),
							'description' => __('When map markers load by AJAX - it should have starting point and starting zoom. Enter start address or select latitude and longitude. Example: 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA.', 'W2GM'),
							//'std' => (string)get_option('w2gm_start_address'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'start_latitude',
							'heading' => __('Starting Point Latitude', 'W2GM'),
							//'std' => (string)get_option('w2gm_start_latitude'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'start_longitude',
							'heading' => __('Starting Point Longitude', 'W2GM'),
							'std' => (string)get_option('w2gm_start_longitude'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'start_zoom',
							'heading' => __('Google Maps zoom level', 'W2GM'),
							'value' => array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19'),
							//'std' => (string)get_option('w2gm_start_zoom'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'map_style',
							'value' => $map_styles,
							'heading' => __('Google Maps style', 'W2GM'),
							//'std' => (string)get_option('w2gm_map_style'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'width',
							'heading' => __('Map width (in pixels)', 'W2GM'),
							'description' => __('When empty - map uses whole possible width', 'W2GM'),
							//'std' => (int)get_option('w2gm_default_map_width'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'height',
							'heading' => __('Map height (in pixels)', 'W2GM'),
							//'std' => (int)get_option('w2gm_default_map_height'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'sticky_scroll',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('Make map to be sticky on scroll', 'W2GM'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'sticky_scroll_toppadding',
							'value' => 10,
							'heading' => __('Sticky scroll top padding', 'W2GM'),
							'description' => __('Top padding in pixels.', 'W2GM'),
							'dependency' => array('element' => 'sticky_scroll', 'value' => '1'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'radius_circle',
							'value' => array(__('Yes', 'W2GM') => '1', __('No', 'W2GM') => '0'),
							'heading' => __('Show radius circle?', 'W2GM'),
							'description' => __('Show circle during radius search?', 'W2GM'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'clusters',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('Enable clusters of map markers?', 'W2GM'),
							//'std' => (int)get_option('w2gm_enable_clusters'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'geolocation',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('GeoLocation', 'W2GM'),
							'description' => __('Enable automatic geolocation.', 'W2GM'),
							//'std' => (int)get_option('w2gm_enable_geolocation'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'address',
							'heading' => __('Address', 'W2GM'),
							'description' => __('Display markers near this address, recommended to set "radius" attribute.', 'W2GM'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'radius',
							'heading' => __('Radius', 'W2GM'),
							'description' => __('display listings near provided address within this radius in miles or kilometers.', 'W2GM'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'enable_full_screen',
							'value' => array(__('Yes', 'W2GM') => '1', __('No', 'W2GM') => '0'),
							'heading' => __('Enable full screen button', 'W2GM'),
							//'std' => (int)get_option('w2gm_enable_full_screen'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'enable_wheel_zoom',
							'value' => array(__('Yes', 'W2GM') => '1', __('No', 'W2GM') => '0'),
							'heading' => __('Enable zoom by mouse wheel', 'W2GM'),
							//'std' => (int)get_option('w2gm_enable_wheel_zoom'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'enable_dragging_touchscreens',
							'value' => array(__('Yes', 'W2GM') => '1', __('No', 'W2GM') => '0'),
							'heading' => __('Enable map dragging on touch screen devices', 'W2GM'),
							//'std' => (int)get_option('w2gm_enable_dragging_touchscreens'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'center_map_onclick',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('Center map on marker click', 'W2GM'),
							//'std' => (int)get_option('w2gm_center_map_onclick'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'enable_listings_sidebar',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('Enable listings sidebar', 'W2GM'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'listings_sidebar_position',
							'value' => array(__('Left', 'W2GM') => 'left', __('Right', 'W2GM') => 'right'),
							'heading' => __('Sidebar position', 'W2GM'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'listings_sidebar_width',
							'heading' => __('Listings sidebar width (in pixels)', 'W2GM'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'author',
							'heading' => __('Author', 'W2GM'),
							'description' => __('Enter exact ID of author.', 'W2GM'),
					),
					array(
							'type' => 'categoriesfield',
							'param_name' => 'categories',
							//'value' => 0,
							'heading' => __('Categories', 'W2GM'),
					),
					array(
							'type' => 'locationsfield',
							'param_name' => 'locations',
							//'value' => 0,
							'heading' => __('Locations', 'W2GM'),
					),
					array(
							'type' => 'dropdown',
							'param_name' => 'include_categories_children',
							'value' => array(__('No', 'W2GM') => '0', __('Yes', 'W2GM') => '1'),
							'heading' => __('Include children of selected categories and locations', 'W2GM'),
							'description' => __('When enabled - any subcategories or sublocations will be included as well.', 'W2GM'),
					),
					array(
							'type' => 'textfield',
							'param_name' => 'post__in',
							'heading' => __('Exact listings', 'W2GM'),
							'description' => __('Comma separated string of listings IDs. Possible to display map markers of exact listings.', 'W2GM'),
					),
			),
	);
	foreach ($w2gm_instance->search_fields->filter_fields_array AS $filter_field) {
		if (method_exists($filter_field, 'getVCParams') && ($field_params = $filter_field->getVCParams()))
			$vc_maps_args['params'] = array_merge($vc_maps_args['params'], $field_params);
	}
	vc_map($vc_maps_args);
}

?>