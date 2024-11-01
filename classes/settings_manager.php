<?php

global $w2gm_wpml_dependent_options;

class w2gm_settings_manager {
	public function __construct() {
		add_action('init', array($this, 'plugin_settings'));
		add_action('vp_w2gm_option_after_ajax_save', array($this, 'save_option'), 10, 3);
	}
	
	public function plugin_settings() {
		global $w2gm_instance, $w2gm_maps_styles, $sitepress;

		$listings_tabs = array(array('value' => 'addresses-tab', 'label' => __('Addresses tab', 'W2GM')));
		foreach ($w2gm_instance->content_fields->content_fields_groups_array AS $fields_group)
			if ($fields_group->on_tab)
				$listings_tabs[] = array('value' => 'field-group-tab-'.$fields_group->id, 'label' => $fields_group->name);
			
		$map_styles = array(array('value' => 'default', 'label' => 'Default style'));
		foreach ($w2gm_maps_styles AS $name=>$style)
			$map_styles[] = array('value' => $name, 'label' => $name);
		
		$country_codes = array(array('value' => 0, 'label' => 'Worldwide'));
		$w2gm_country_codes = w2gm_country_codes();
		foreach ($w2gm_country_codes AS $country=>$code)
			$country_codes[] = array('value' => $code, 'label' => $country);
		
		$theme_options = array(
				'option_key' => 'vpt_option',
				'page_slug' => 'w2gm_settings',
				'template' => array(
					'title' => __('Web 2.0 Google Maps Settings', 'W2GM'),
					'logo' => W2GM_RESOURCES_URL . 'images/settings.png',
					'menus' => array(
						'listings' => array(
							'name' => 'listings',
							'title' => __('Listings', 'W2GM'),
							'icon' => 'font-awesome:w2gm-fa-list-alt',
							'controls' => array(
								'listings' => array(
									'type' => 'section',
									'title' => __('Listings settings', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_eternal_active_period',
											'label' => __('Listings will never expire', 'W2GM'),
											'default' => get_option('w2gm_eternal_active_period'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_active_period_days',
											'label' => __('Active period of listings (in days)', 'W2GM'),
											'description' => __('Works when listings may expire.', 'W2GM'),
											'default' => get_option('w2gm_active_period_days'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_active_period_months',
											'label' => __('Active period of listings (in months)', 'W2GM'),
											'description' => __('Works when listings may expire.', 'W2GM'),
											'default' => get_option('w2gm_active_period_months'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_active_period_years',
											'label' => __('Active period of listings (in years)', 'W2GM'),
											'description' => __('Works when listings may expire.', 'W2GM'),
											'default' => get_option('w2gm_active_period_years'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_change_expiration_date',
											'label' => __('Allow regular users to change listings expiration dates', 'W2GM'),
											'default' => get_option('w2gm_change_expiration_date'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_hide_listings_creation_date',
											'label' => __('Hide listings creation date', 'W2GM'),
											'default' => get_option('w2gm_hide_listings_creation_date'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_hide_author_link',
											'label' => __('Hide author information', 'W2GM'),
											'description' => __('Author name and possible link to author website will be hidden on single listing pages.', 'W2GM'),
											'default' => get_option('w2gm_hide_author_link'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_renew',
											'label' => __('Allow listings to renew', 'W2GM'),
											'default' => get_option('w2gm_enable_renew'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_unlimited_categories',
											'label' => __('Allow unlimited categories', 'W2GM'),
											'default' => get_option('w2gm_unlimited_categories'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_categories_number',
											'label' => __('Number of categories allowed for each listing', 'W2GM'),
											'default' => get_option('w2gm_categories_number'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_locations_number',
											'label' => __('Number of locations allowed for each listing', 'W2GM'),
											'default' => get_option('w2gm_locations_number'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_map_listing',
											'label' => __('Enable map in listing window', 'W2GM'),
											'default' => get_option('w2gm_enable_map_listing'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_show_directions',
											'label' => __('Show directions panel in listing window?', 'W2GM'),
											'default' => get_option('w2gm_show_directions'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2gm_directions_functionality',
											'label' => __('Directions functionality in listing window', 'W2GM'),
											'items' => array(
												array(
													'value' => 'builtin',
													'label' =>__('Built-in routing', 'W2GM'),
												),
												array(
													'value' => 'google',
													'label' =>__('Link to Google Maps', 'W2GM'),
												),
											),
											'default' => array(
													get_option('w2gm_directions_functionality')
											),
										),
										array(
											'type' => 'sorter',
											'name' => 'w2gm_listings_tabs_order',
											'label' => __('Priority of opening of listing tabs', 'W2GM'),
									 		'items' => $listings_tabs,
											'description' => __('Set up priority of tabs those are opened by default. If any listing does not have any tab - next tab in the order will be opened by default.'),
											'default' => get_option('w2gm_listings_tabs_order'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_stats',
											'label' => __('Enable statistics functionality', 'W2GM'),
											'default' => get_option('w2gm_enable_stats'),
										),
									),
								),
								'logos' => array(
									'type' => 'section',
									'title' => __('Listings logos & images', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_listing_logo_enabled',
											'label' => __('Allow logo image for each listing', 'W2GM'),
											'default' => get_option('w2gm_listing_logo_enabled'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_logo_enabled',
											'label' => __('Enable logo in infowindow', 'W2GM'),
											'default' => get_option('w2gm_logo_enabled'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_nologo',
											'label' => __('Enable default logo image', 'W2GM'),
											'default' => get_option('w2gm_enable_nologo'),
										),
										array(
											'type' => 'upload',
											'name' => 'w2gm_nologo_url',
											'label' => __('Default logo image', 'W2GM'),
									 		'description' => __('This image will appear when listing owner did not upload own logo.', 'W2GM'),
											'default' => get_option('w2gm_nologo_url'),
										)
									),
								),
								'excerpts' => array(
									'type' => 'section',
									'title' => __('Description & Excerpt settings', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_description',
											'label' => __('Enable description field', 'W2GM'),
											'default' => get_option('w2gm_enable_description'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_summary',
											'label' => __('Enable summary field', 'W2GM'),
											'default' => get_option('w2gm_enable_summary'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_excerpt_length',
											'label' => __('Excerpt max length', 'W2GM'),
											'description' => __('Insert the number of words you want to show in the listings excerpts', 'W2GM'),
											'default' => get_option('w2gm_excerpt_length'),
											'validation' => 'required|numeric',
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_cropped_content_as_excerpt',
											'label' => __('Use cropped content as excerpt', 'W2GM'),
											'description' => __('When excerpt field is empty - use cropped main content', 'W2GM'),
											'default' => get_option('w2gm_cropped_content_as_excerpt'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_strip_excerpt',
											'label' => __('Strip HTML from excerpt', 'W2GM'),
											'description' => __('Check the box if you want to strip HTML from the excerpt content only', 'W2GM'),
											'default' => get_option('w2gm_strip_excerpt'),
										),
									),
								),
							),
						),
						'maps' => array(
							'name' => 'maps',
							'title' => __('Default Maps', 'W2GM'),
							'icon' => 'font-awesome:w2gm-fa-dashboard',
							'controls' => array(
								'maps' => array(
									'type' => 'section',
									'title' => __('Default maps settings', 'W2GM'),
									'description' => __('These are default settings for all [webmap] shortcodes. You may configure individual look and behaviour of each map using shortcode parameters.', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_ajax_loading',
											'label' => __('Use AJAX loading', 'W2GM'),
											'description' => __('Load map markers using AJAX. The map loads only needed map markers, those visible in the viewport of the map', 'W2GM'),
											'default' => get_option('w2gm_ajax_loading'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_ajax_markers_loading',
											'label' => __('InfoWindow AJAX loading', 'W2GM'),
											'description' => __('Load infowindow using AJAX.', 'W2GM'),
											'default' => get_option('w2gm_ajax_markers_loading'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_max_num',
											'label' => __('Maximum number of listings on map', 'W2GM'),
											'description' => __('Place -1 if you need unlimited.', 'W2GM'),
											'default' => get_option('w2gm_max_num'),
											'validation' => 'numeric',
										),
									 	array(
											'type' => 'toggle',
											'name' => 'w2gm_show_readmore_button',
											'label' => __('Show read more button in InfoWindow?', 'W2GM'),
											'default' => get_option('w2gm_show_readmore_button'),
										),
									 	array(
											'type' => 'toggle',
											'name' => 'w2gm_show_directions_button',
											'label' => __('Show directions button in InfoWindow?', 'W2GM'),
											'default' => get_option('w2gm_show_directions_button'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_start_address',
											'label' => __('Start address', 'W2GM'),
											'description' => __('When map markers load by AJAX - it should have starting point and starting zoom. Enter start address or select latitude and longitude. Example: 1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA.', 'W2GM'),
											'default' => get_option('w2gm_start_address'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_start_latitude',
											'label' => __('Starting point latitude', 'W2GM'),
											'default' => get_option('w2gm_start_latitude'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_start_longitude',
											'label' => __('Starting point longitude', 'W2GM'),
											'default' => get_option('w2gm_start_longitude'),
										),
										array(
											'type' => 'slider',
											'name' => 'w2gm_start_zoom',
											'label' => __('Starting zoom level', 'W2GM'),
									 		'min' => 1,
									 		'max' => 19,
											'default' => get_option('w2gm_start_zoom'),
										),
									 	array(
											'type' => 'select',
											'name' => 'w2gm_map_style',
											'label' => __('Google Maps style', 'W2GM'),
									 		'items' => $map_styles,
											'default' => array(get_option('w2gm_map_style')),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_default_map_width',
											'label' => __('Default map width (in pixels)', 'W2GM'),
											'default' => get_option('w2gm_default_map_width'),
											'description' => __('When empty - map uses whole possible width', 'W2GM'),
											'validation' => 'numeric',
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_default_map_height',
											'label' => __('Default map height (in pixels)', 'W2GM'),
											'default' => get_option('w2gm_default_map_height'),
											'validation' => 'required|numeric',
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_clusters',
											'label' => __('Enable clusters of map markers?', 'W2GM'),
											'default' => get_option('w2gm_enable_clusters'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_geolocation',
											'label' => __('Enable Geolocation', 'W2GM'),
											'default' => get_option('w2gm_enable_geolocation'),
										),
									),
								),
								'listings_bar' => array(
									'type' => 'section',
									'title' => __('Listings sidebar settings', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_listings_sidebar',
											'label' => __('Enable listings sidebar', 'W2GM'),
											'default' => get_option('w2gm_enable_listings_sidebar'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2gm_listings_sidebar_position',
											'label' => __('Sidebar position', 'W2GM'),
											'items' => array(
												array(
													'value' => 'left',
													'label' =>__('Left', 'W2GM'),
												),
												array(
													'value' => 'right',
													'label' =>__('Right', 'W2GM'),
												),
											),
											'default' => array(
													get_option('w2gm_listings_sidebar_position')
											),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_listings_sidebar_width',
											'label' => __('Listings sidebar width (in pixels)', 'W2GM'),
											'default' => get_option('w2gm_listings_sidebar_width'),
											'validation' => 'numeric',
										),
									),
								),
								'maps_controls' => array(
									'type' => 'section',
									'title' => __('Maps controls settings', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_full_screen',
											'label' => __('Enable full screen button', 'W2GM'),
											'default' => get_option('w2gm_enable_full_screen'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_wheel_zoom',
											'label' => __('Enable zoom by mouse wheel', 'W2GM'),
											'description' => __('For desktops', 'W2GM'),
											'default' => get_option('w2gm_enable_wheel_zoom'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_dragging_touchscreens',
											'label' => __('Enable map dragging on touch screen devices', 'W2GM'),
											'default' => get_option('w2gm_enable_dragging_touchscreens'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_center_map_onclick',
											'label' => __('Center map on marker click', 'W2GM'),
											'default' => get_option('w2gm_center_map_onclick'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_hide_search_on_map_mobile',
											'label' => __('Hide compact search form on the map for mobile devices', 'W2GM'),
											'description' => __('This setting for all maps', 'W2GM'),
											'default' => get_option('w2gm_hide_search_on_map_mobile'),
										),
									),
								),
							),
						),
						'addresses' => array(
							'name' => 'addresses',
							'title' => __('Markers & Addresses', 'W2GM'),
							'icon' => 'font-awesome:w2gm-fa-map-marker',
							'controls' => array(
								'addresses' => array(
									'type' => 'section',
									'title' => __('Addresses settings', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'textbox',
											'name' => 'w2gm_default_geocoding_location',
											'label' => __('Default country/state for correct geocoding', 'W2GM'),
											'description' => __('This value needed when you build local diirectory, all your listings place in one local area - country or state. You do not want to set countries or states in the search, so this hidden string will be automatically added to the address for correct geocoding when you create/edit listings.', 'W2GM'),
											'default' => get_option('w2gm_default_geocoding_location'),
										),
										array(
											'type' => 'sorter',
											'name' => 'w2gm_addresses_order',
											'label' => __('Order of address lines', 'W2GM'),
									 		'items' => array(
									 			array('value' => 'location', 'label' => __('Selected location', 'W2GM')),
									 			array('value' => 'line_1', 'label' => __('Address Line 1', 'W2GM')),
									 			array('value' => 'line_2', 'label' => __('Address Line 2', 'W2GM')),
									 			array('value' => 'zip', 'label' => __('Zip code or postal index', 'W2GM')),
									 			array('value' => 'space1', 'label' => __('-- Space ( ) --', 'W2GM')),
									 			array('value' => 'space2', 'label' => __('-- Space ( ) --', 'W2GM')),
									 			array('value' => 'space3', 'label' => __('-- Space ( ) --', 'W2GM')),
									 			array('value' => 'comma1', 'label' => __('-- Comma (,) --', 'W2GM')),
									 			array('value' => 'comma2', 'label' => __('-- Comma (,) --', 'W2GM')),
									 			array('value' => 'comma3', 'label' => __('-- Comma (,) --', 'W2GM')),
									 			array('value' => 'break1', 'label' => __('-- Line Break --', 'W2GM')),
									 			array('value' => 'break2', 'label' => __('-- Line Break --', 'W2GM')),
									 		),
											'description' => __('Order address elements as you wish, commas and spaces help to build address line.'),
											'default' => get_option('w2gm_addresses_order'),
										),
										array(
											'type' => 'select',
											'name' => 'w2gm_address_autocomplete_code',
											'label' => __('Restrict autocomplete addess fields to the default country', 'W2GM'),
									 		'items' => $country_codes,
											'default' => get_option('w2gm_address_autocomplete_code'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_address_line_1',
											'label' => __('Enable address line 1 field', 'W2GM'),
											'default' => get_option('w2gm_enable_address_line_1'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_address_line_2',
											'label' => __('Enable address line 2 field', 'W2GM'),
											'default' => get_option('w2gm_enable_address_line_2'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_postal_index',
											'label' => __('Enable zip code', 'W2GM'),
											'default' => get_option('w2gm_enable_postal_index'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_additional_info',
											'label' => __('Enable additional info field', 'W2GM'),
											'default' => get_option('w2gm_enable_additional_info'),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_manual_coords',
											'label' => __('Enable manual coordinates fields', 'W2GM'),
											'default' => get_option('w2gm_enable_manual_coords'),
										),
									),
								),
								'markers' => array(
									'type' => 'section',
									'title' => __('Map markers & InfoWindow settings', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_enable_users_markers',
											'label' => __('Allow users to select markers', 'W2GM'),
											'default' => get_option('w2gm_enable_users_markers'),
										),
										array(
											'type' => 'radiobutton',
											'name' => 'w2gm_map_markers_type',
											'label' => __('Type of Map Markers', 'W2GM'),
											'items' => array(
												array(
													'value' => 'icons',
													'label' =>__('Font Awesome icons (recommended)', 'W2GM'),
												),
												array(
													'value' => 'images',
													'label' =>__('PNG images', 'W2GM'),
												),
											),
											'default' => array(
													get_option('w2gm_map_markers_type')
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2gm_default_marker_color',
											'label' => __('Default Map Marker color', 'W2GM'),
											'default' => get_option('w2gm_default_marker_color'),
											'description' => __('For Font Awesome icons.', 'W2GM'),
										),
										array(
											'type' => 'fontawesome',
											'name' => 'w2gm_default_marker_icon',
											'label' => __('Default Map Marker icon'),
											'description' => __('For Font Awesome icons.', 'W2GM'),
											'default' => array(
												get_option('w2gm_default_marker_icon')
											),
										),
										array(
											'type' => 'slider',
											'name' => 'w2gm_map_marker_width',
											'label' => __('Map marker width (in pixels)', 'W2GM'),
											'description' => __('For PNG images.', 'W2GM'),
											'default' => get_option('w2gm_map_marker_width'),
									 		'min' => 10,
									 		'max' => 64,
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2gm_map_marker_height',
											'label' => __('Map marker height (in pixels)', 'W2GM'),
									 		'description' => __('For PNG images.', 'W2GM'),
											'default' => get_option('w2gm_map_marker_height'),
									 		'min' => 10,
									 		'max' => 64,
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2gm_map_marker_anchor_x',
											'label' => __('Map marker anchor horizontal position (in pixels)', 'W2GM'),
									 		'description' => __('For PNG images.', 'W2GM'),
											'default' => get_option('w2gm_map_marker_anchor_x'),
									 		'min' => 0,
									 		'max' => 64,
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2gm_map_marker_anchor_y',
											'label' => __('Map marker anchor vertical position (in pixels)', 'W2GM'),
									 		'description' => __('For PNG images.', 'W2GM'),
											'default' => get_option('w2gm_map_marker_anchor_y'),
									 		'min' => 0,
									 		'max' => 64,
										),
									 	array(
											'type' => 'slider',
											'name' => 'w2gm_map_infowindow_width',
											'label' => __('Map InfoWindow width (in pixels)', 'W2GM'),
											'default' => get_option('w2gm_map_infowindow_width'),
									 		'min' => 100,
									 		'max' => 600,
									 		'step' => 10,
										),
										array(
											'type' => 'slider',
											'name' => 'w2gm_map_infowindow_offset',
											'label' => __('Map InfoWindow vertical position above marker (in pixels)', 'W2GM'),
											'default' => get_option('w2gm_map_infowindow_offset'),
									 		'min' => 30,
									 		'max' => 120,
										),
										array(
											'type' => 'slider',
											'name' => 'w2gm_map_infowindow_logo_width',
											'label' => __('Map InfoWindow logo width (in pixels)', 'W2GM'),
											'default' => get_option('w2gm_map_infowindow_logo_width'),
									 		'min' => 40,
									 		'max' => 300,
											'step' => 10,
										),
									),
								),
							),
						),
						'notifications' => array(
							'name' => 'notifications',
							'title' => __('Email notifications', 'W2GM'),
							'icon' => 'font-awesome:w2gm-fa-envelope',
							'controls' => array(
								'notifications' => array(
									'type' => 'section',
									'title' => __('Email notifications', 'W2GM'),
									'fields' => array(
									 	array(
											'type' => 'textbox',
											'name' => 'w2gm_send_expiration_notification_days',
											'label' => __('Days before pre-expiration notification will be sent', 'W2GM'),
											'default' => get_option('w2gm_send_expiration_notification_days'),
										),
										array(
											'type' => 'textbox',
											'name' => 'w2gm_admin_notifications_email',
											'label' => __('This email will be used for notifications to admin and in "From" field. Required to send emails.', 'W2GM'),
											'default' => get_option('w2gm_admin_notifications_email'),
										),
									 	array(
											'type' => 'textarea',
											'name' => 'w2gm_preexpiration_notification',
											'label' => __('Pre-expiration notification', 'W2GM'),
											'default' => get_option('w2gm_preexpiration_notification'),
										),
									 	array(
											'type' => 'textarea',
											'name' => 'w2gm_expiration_notification',
											'label' => __('Expiration notification', 'W2GM'),
											'default' => get_option('w2gm_expiration_notification'),
										),
									),
								),
							),
						),
						'advanced' => array(
							'name' => 'advanced',
							'title' => __('Advanced settings', 'W2GM'),
							'icon' => 'font-awesome:w2gm-fa-gear',
							'controls' => array(
								'google_api' => array(
									'type' => 'section',
									'title' => __('Google API', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'textbox',
											'name' => 'w2gm_google_api_key',
											'label' => __('Google API key', 'W2GM'),
											'description' => sprintf(__('get your Google API key <a href="%s" target="_blank">here</a>, following APIs must be enabled in the console: Google Maps Directions API, Google Maps Geocoding API, Google Maps JavaScript API, Google Places API Web Service and Google Static Maps API.', 'W2GM'), 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,static_maps_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true') . ' ' . sprintf(__('Then check geolocation <a href="%s">response</a>.', 'W2GM'), admin_url('admin.php?page=w2gm_debug')),
											'default' => get_option('w2gm_google_api_key'),
										),
									),
								),
								'js_css' => array(
									'type' => 'section',
									'title' => __('JavaScript & CSS', 'W2GM'),
									'description' => __('Do not touch these settings if you do not know what they mean. It may cause lots of problems.', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_notinclude_jqueryui_css',
											'label' => __('Do not include jQuery UI CSS', 'W2GM'),
									 		'description' =>  __('Some themes and 3rd party plugins include own jQuery UI CSS - this may cause conflicts in styles.', 'W2GM'),
											'default' => get_option('w2gm_notinclude_jqueryui_css'),
										),
									),
								),
								'miscellaneous' => array(
									'type' => 'section',
									'title' => __('Miscellaneous', 'W2GM'),
									'fields' => array(
									 	array(
											'type' => 'toggle',
											'name' => 'w2gm_address_autocomplete',
											'label' => __('Enable autocomplete on addresses fields', 'W2GM'),
											'default' => get_option('w2gm_address_autocomplete'),
										),
									 	array(
											'type' => 'toggle',
											'name' => 'w2gm_address_geocode',
											'label' => __('Enable "Get my location" button on addresses fields', 'W2GM'),
											'default' => get_option('w2gm_address_geocode'),
										),
									),
								),
							),
						),
						'customization' => array(
							'name' => 'customization',
							'title' => __('Customization', 'W2GM'),
							'icon' => 'font-awesome:w2gm-fa-check',
							'controls' => array(
								'color_schemas' => array(
									'type' => 'section',
									'title' => __('Color palettes', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'toggle',
											'name' => 'w2gm_compare_palettes',
											'label' => __('Compare palettes at the frontend', 'W2GM'),
									 		'description' =>  __('Do not forget to switch off this setting when comparison will be completed.', 'W2GM'),
											'default' => get_option('w2gm_compare_palettes'),
										),
										array(
											'type' => 'select',
											'name' => 'w2gm_color_scheme',
											'label' => __('Color palette', 'W2GM'),
											'items' => array(
												array('value' => 'default', 'label' => __('Default', 'W2GM')),
												array('value' => 'orange', 'label' => __('Orange', 'W2GM')),
												array('value' => 'red', 'label' => __('Red', 'W2GM')),
												array('value' => 'yellow', 'label' => __('Yellow', 'W2GM')),
												array('value' => 'green', 'label' => __('Green', 'W2GM')),
												array('value' => 'gray', 'label' => __('Gray', 'W2GM')),
												array('value' => 'blue', 'label' => __('Blue', 'W2GM')),
											),
											'default' => array(get_option('w2gm_color_scheme')),
										),
										array(
											'type' => 'notebox',
											'description' => esc_attr__("Don't forget to clear cache of your browser and on server (when used) after customization changes were made.", 'W2GM'),
											'status' => 'warning',
										),
									),
								),
								'links_colors' => array(
									'type' => 'section',
									'title' => __('Links & buttons', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'color',
											'name' => 'w2gm_links_color',
											'label' => __('Links color', 'W2GM'),
											'default' => get_option('w2gm_links_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_links_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2gm_links_hover_color',
											'label' => __('Links hover color', 'W2GM'),
											'default' => get_option('w2gm_links_hover_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_links_hover_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2gm_button_1_color',
											'label' => __('Button primary color', 'W2GM'),
											'default' => get_option('w2gm_button_1_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_button_1_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2gm_button_2_color',
											'label' => __('Button secondary color', 'W2GM'),
											'default' => get_option('w2gm_button_2_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_button_2_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2gm_button_text_color',
											'label' => __('Button text color', 'W2GM'),
											'default' => get_option('w2gm_button_text_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_button_text_color'
											),
										),
										array(
											'type' => 'toggle',
											'name' => 'w2gm_button_gradient',
											'label' => __('Use gradient on buttons', 'W2GM'),
											'description' => __('This will remove all icons from buttons', 'W2GM'),
											'default' => get_option('w2gm_button_gradient'),
										),
									),
								),
								'search_colors' => array(
									'type' => 'section',
									'title' => __('Search block', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'color',
											'name' => 'w2gm_search_1_color',
											'label' => __('Primary gradient color', 'W2GM'),
											'default' => get_option('w2gm_search_1_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_search_1_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2gm_search_2_color',
											'label' => __('Secondary gradient color', 'W2GM'),
											'default' => get_option('w2gm_search_2_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_search_2_color'
											),
										),
										array(
											'type' => 'color',
											'name' => 'w2gm_search_text_color',
											'label' => __('Search block text color', 'W2GM'),
											'default' => get_option('w2gm_search_text_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_search_text_color'
											),
										),
									),
								),
								'misc_colors' => array(
									'type' => 'section',
									'title' => __('Misc settings', 'W2GM'),
									'fields' => array(
										array(
											'type' => 'color',
											'name' => 'w2gm_primary_color',
											'label' => __('Primary color', 'W2GM'),
											'description' =>  __('The color of labels, panels, map info window caption', 'W2GM'),
											'default' => get_option('w2gm_primary_color'),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_primary_color'
											),
										),
										array(
											'type' => 'slider',
											'name' => 'w2gm_listing_title_font',
											'label' => __('Listing title font size (in pixels)', 'W2GM'),
											'min' => '7',
											'max' => '40',
											'default' => '20',
										),
										array(
											'type' => 'radioimage',
											'name' => 'w2gm_jquery_ui_schemas',
											'label' => __('jQuery UI Style', 'W2GM'),
									 		'description' =>  __('Controls the color of calendar, dialogs and slider UI widgets', 'W2GM'),
									 		'items' => array(
									 			array(
									 				'value' => 'blitzer',
									 				'label' => 'Blitzer',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_blitzer.png'
									 			),
									 			array(
									 				'value' => 'smoothness',
									 				'label' => 'Smoothness',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_smoothness.png'
									 			),
									 			array(
									 				'value' => 'redmond',
									 				'label' => 'Redmond',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_windoze.png'
									 			),
									 			array(
									 				'value' => 'ui-darkness',
									 				'label' => 'UI Darkness',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_ui_dark.png'
									 			),
									 			array(
									 				'value' => 'ui-lightness',
									 				'label' => 'UI Lightness',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_ui_light.png'
									 			),
									 			array(
									 				'value' => 'trontastic',
									 				'label' => 'Trontastic',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_trontastic.png'
									 			),
									 			array(
									 				'value' => 'start',
									 				'label' => 'Start',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_start_menu.png'
									 			),
									 			array(
									 				'value' => 'sunny',
									 				'label' => 'Sunny',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_sunny.png'
									 			),
									 			array(
									 				'value' => 'overcast',
									 				'label' => 'Overcast',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_overcast.png'
									 			),
									 			array(
									 				'value' => 'le-frog',
									 				'label' => 'Le Frog',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_le_frog.png'
									 			),
									 			array(
									 				'value' => 'hot-sneaks',
									 				'label' => 'Hot Sneaks',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_hot_sneaks.png'
									 			),
									 			array(
									 				'value' => 'excite-bike',
									 				'label' => 'Excite Bike',
									 				'img' => (is_ssl() ? 'https' : 'http').'://jqueryui.com/resources/images/themeGallery/theme_90_excite_bike.png'
									 			),
									 		),
											'default' => array(get_option('w2gm_jquery_ui_schemas')),
											'binding' => array(
												'field' => 'w2gm_color_scheme',
												'function' => 'w2gm_affect_setting_w2gm_jquery_ui_schemas'
											),
										),
									),
								),
							),
						),
					)
				),
				'use_auto_group_naming' => true,
				'use_util_menu' => false,
				'minimum_role' => 'edit_theme_options',
				'layout' => 'fixed',
				'page_title' => __('Maps settings', 'W2GM'),
				'menu_label' => __('Maps settings', 'W2GM'),
		);
		
		// adapted for WPML /////////////////////////////////////////////////////////////////////////
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$theme_options['template']['menus']['advanced']['controls']['wpml'] = array(
				'type' => 'section',
				'title' => __('WPML Settings', 'W2GM'),
				'fields' => array(
					array(
						'type' => 'toggle',
						'name' => 'w2gm_map_language_from_wpml',
						'label' => __('Force WPML language on maps', 'W2GM'),
						'description' => __("Ignore the browser's language setting and force it to display information in a particular WPML language", 'W2GM'),
						'default' => get_option('w2gm_map_language_from_wpml'),
					),
				),
			);
		}
		
		$theme_options = apply_filters('w2gm_build_settings', $theme_options);

		$VP_W2GM_Option = new VP_W2GM_Option($theme_options);
	}

	public function save_option($opts, $old_opts, $status) {
		global $w2gm_wpml_dependent_options, $sitepress;

		if ($status) {
			foreach ($opts AS $option=>$value) {
				// adapted for WPML
				if (in_array($option, $w2gm_wpml_dependent_options)) {
					if (function_exists('wpml_object_id_filter') && $sitepress) {
						if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE) {
							update_option($option.'_'.ICL_LANGUAGE_CODE, $value);
							continue;
						}
					}
				}
				
				if ($option == 'w2gm_google_api_key') {
					$value = trim($value);
				}
				update_option($option, $value);
			}
			
			w2gm_save_dynamic_css();
		}
	}
}

function w2gm_save_dynamic_css() {
	$upload_dir = wp_upload_dir();
	$filename = trailingslashit($upload_dir['basedir']) . 'w2gm-plugin.css';
		
	ob_start();
	include W2GM_PATH . '/classes/customization/dynamic_css.php';
	$dynamic_css = ob_get_contents();
	ob_get_clean();
		
	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once(ABSPATH .'/wp-admin/includes/file.php');
		WP_Filesystem();
	}
		
	if ($wp_filesystem) {
		$wp_filesystem->put_contents(
				$filename,
				$dynamic_css,
				FS_CHMOD_FILE // predefined mode settings for WP files
		);
	}
}

// adapted for WPML
function w2gm_get_wpml_dependent_option_name($option) {
	global $w2gm_wpml_dependent_options, $sitepress;

	if (in_array($option, $w2gm_wpml_dependent_options))
		if (function_exists('wpml_object_id_filter') && $sitepress)
			if ($sitepress->get_default_language() != ICL_LANGUAGE_CODE)
				if (get_option($option.'_'.ICL_LANGUAGE_CODE) !== false)
					return $option.'_'.ICL_LANGUAGE_CODE;

	return $option;
}
function w2gm_get_wpml_dependent_option($option) {
	return get_option(w2gm_get_wpml_dependent_option_name($option));
}
function w2gm_get_wpml_dependent_option_description() {
	global $sitepress;
	return ((function_exists('wpml_object_id_filter') && $sitepress) ? sprintf(__('%s This is multilingual option, each language may have own value.', 'W2GM'), '<br /><img src="'.W2GM_RESOURCES_URL . 'images/multilang.png" /><br />') : '');
}

?>