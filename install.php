<?php 

function w2gm_install_maps() {
	global $wpdb;
	
	if (!get_option('w2gm_installed_maps_lite')) {
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2gm_content_fields_groups} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`on_tab` tinyint(1) NOT NULL DEFAULT '0',
					`hide_anonymous` tinyint(1) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields_groups} WHERE name = 'Contact Information'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields_groups} (`name`, `on_tab`, `hide_anonymous`) VALUES ('Contact Information', 0, 0)");
		
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2gm_content_fields} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`is_core_field` tinyint(1) NOT NULL DEFAULT '0',
					`order_num` tinyint(1) NOT NULL,
					`name` varchar(255) NOT NULL,
					`slug` varchar(255) NOT NULL,
					`description` text NOT NULL,
					`type` varchar(255) NOT NULL,
					`icon_image` varchar(255) NOT NULL,
					`is_required` tinyint(1) NOT NULL DEFAULT '0',
					`is_configuration_page` tinyint(1) NOT NULL DEFAULT '0',
					`is_search_configuration_page` tinyint(1) NOT NULL DEFAULT '0',
					`is_ordered` tinyint(1) NOT NULL DEFAULT '0',
					`is_hide_name` tinyint(1) NOT NULL DEFAULT '0',
					`on_listing_page` tinyint(1) NOT NULL DEFAULT '0',
					`on_listing_sidebar` tinyint(1) NOT NULL DEFAULT '0',
					`on_search_form` tinyint(1) NOT NULL DEFAULT '0',
					`on_map` tinyint(1) NOT NULL DEFAULT '0',
					`advanced_search_form` tinyint(1) NOT NULL,
					`categories` text NOT NULL,
					`options` text NOT NULL,
					`search_options` text NOT NULL,
					`group_id` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`),
					KEY `group_id` (`group_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'summary'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 1, 'Summary', 'summary', '', 'excerpt', '', 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, '', '', '', '0');");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'address'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 2, 'Address', 'address', '', 'address', 'w2gm-fa-map-marker', 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, '', '', '', '0');");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'content'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 3, 'Description', 'content', '', 'content', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, '', '', '', '0');");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'categories_list'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 4, 'Categories', 'categories_list', '', 'categories', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, '', '', '', '0');");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'listing_tags'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(1, 5, 'Listing Tags', 'listing_tags', '', 'tags', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, '', '', '', '0');");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'phone'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(0, 6, 'Phone', 'phone', '', 'string', 'w2gm-fa-phone', 0, 1, 0, 0, 0, 1, 1, 0, 1, 0, '', 'a:2:{s:10:\"max_length\";s:2:\"25\";s:5:\"regex\";s:0:\"\";}', '', '1');");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'website'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(0, 7, 'Website', 'website', '', 'website', 'w2gm-fa-globe', 0, 1, 0, 0, 0, 1, 1, 0, 1, 0, '', 'a:5:{s:8:\"is_blank\";i:1;s:11:\"is_nofollow\";i:1;s:13:\"use_link_text\";i:1;s:17:\"default_link_text\";s:13:\"view our site\";s:21:\"use_default_link_text\";i:0;}', '', '1');");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_content_fields} WHERE slug = 'email'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_content_fields} (`is_core_field`, `order_num`, `name`, `slug`, `description`, `type`, `icon_image`, `is_required`, `is_configuration_page`, `is_search_configuration_page`, `is_ordered`, `is_hide_name`, `on_listing_page`, `on_listing_sidebar`, `on_search_form`, `on_map`, `advanced_search_form`, `categories`, `options`, `search_options`, `group_id`) VALUES(0, 8, 'Email', 'email', '', 'email', 'w2gm-fa-envelope-o', 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, '', '', '', '1');");

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2gm_locations_levels} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`in_widget` tinyint(1) NOT NULL,
					`in_address_line` tinyint(1) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `in_select_widget` (`in_widget`,`in_address_line`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_locations_levels} WHERE name = 'Country'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_locations_levels} (`name`, `in_widget`, `in_address_line`) VALUES ('Country', 1, 1);");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_locations_levels} WHERE name = 'State'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_locations_levels} (`name`, `in_widget`, `in_address_line`) VALUES ('State', 1, 1);");
		if (!$wpdb->get_var("SELECT id FROM {$wpdb->w2gm_locations_levels} WHERE name = 'City'"))
			$wpdb->query("INSERT INTO {$wpdb->w2gm_locations_levels} (`name`, `in_widget`, `in_address_line`) VALUES ('City', 1, 1);");

		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->w2gm_locations_relationships} (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`post_id` int(11) NOT NULL,
					`location_id` int(11) NOT NULL,
					`address_line_1` varchar(255) NOT NULL,
					`address_line_2` varchar(255) NOT NULL,
					`zip_or_postal_index` varchar(25) NOT NULL,
					`additional_info` text NOT NULL,
					`manual_coords` tinyint(1) NOT NULL,
					`map_coords_1` float(10,6) NOT NULL,
					`map_coords_2` float(10,6) NOT NULL,
					`map_icon_file` varchar(255) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `location_id` (`location_id`),
					KEY `post_id` (`post_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	
		if (!is_array(get_terms(W2GM_LOCATIONS_TAX)) || !count(get_terms(W2GM_LOCATIONS_TAX))) {
			if (($parent_term = wp_insert_term('USA', W2GM_LOCATIONS_TAX)) && !is_a($parent_term, 'WP_Error')) {
				wp_insert_term('Alabama', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Alaska', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Arkansas', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Arizona', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('California', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Colorado', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Connecticut', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Delaware', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('District of Columbia', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Florida', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Georgia', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Hawaii', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Idaho', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Illinois', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Indiana', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Iowa', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Kansas', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Kentucky', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Louisiana', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Maine', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Maryland', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Massachusetts', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Michigan', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Minnesota', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Mississippi', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Missouri', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Montana', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Nebraska', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Nevada', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New Hampshire', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New Jersey', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New Mexico', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('New York', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('North Carolina', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('North Dakota', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Ohio', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Oklahoma', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Oregon', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Pennsylvania', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Rhode Island', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('South Carolina', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('South Dakota', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Tennessee', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Texas', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Utah', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Vermont', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Virginia', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Washington state', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('West Virginina', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Wisconsin', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
				wp_insert_term('Wyoming', W2GM_LOCATIONS_TAX, array('parent' => $parent_term['term_id']));
			}
		}

		add_option('w2gm_eternal_active_period', 1);
		add_option('w2gm_active_period_days', '');
		add_option('w2gm_active_period_months', '');
		add_option('w2gm_active_period_years', '');
		add_option('w2gm_change_expiration_date', 0);
		add_option('w2gm_enable_renew', 1);
		add_option('w2gm_unlimited_categories', 1);
		add_option('w2gm_categories_number', 0);
		add_option('w2gm_locations_number', 3);
		add_option('w2gm_show_directions', 1);
		add_option('w2gm_enable_map_listing', 1);
		add_option('w2gm_directions_functionality', 'builtin');
		add_option('w2gm_listings_tabs_order', array("addresses-tab"));
		add_option('w2gm_enable_stats', 1);
		add_option('w2gm_listing_logo_enabled', 1);
		add_option('w2gm_logo_enabled', 1);
		add_option('w2gm_enable_lighbox_gallery', 1);
		add_option('w2gm_auto_slides_gallery', 0);
		add_option('w2gm_auto_slides_gallery_delay', 3000);
		add_option('w2gm_enable_nologo', 1);
		add_option('w2gm_nologo_url', W2GM_URL . 'resources/images/nologo.png');
		add_option('w2gm_100_single_logo_width', 0);
		add_option('w2gm_single_logo_width', 400);
		add_option('w2gm_big_slide_bg_mode', 'cover');
		add_option('w2gm_videos_number', 3);
		add_option('w2gm_enable_description', 1);
		add_option('w2gm_enable_summary', 1);
		add_option('w2gm_excerpt_length', 25);
		add_option('w2gm_cropped_content_as_excerpt', 1);
		add_option('w2gm_strip_excerpt', 1);
		add_option('w2gm_ajax_loading', 0);
		add_option('w2gm_ajax_markers_loading', 0);
		add_option('w2gm_max_num', -1);
		add_option('w2gm_show_readmore_button', 1);
		add_option('w2gm_show_directions_button', 1);
		add_option('w2gm_start_address', '');
		add_option('w2gm_start_latitude', '');
		add_option('w2gm_start_longitude', '');
		add_option('w2gm_start_zoom', 2);
		add_option('w2gm_map_style', 'default');
		add_option('w2gm_default_map_width', '');
		add_option('w2gm_default_map_height', 550);
		add_option('w2gm_enable_clusters', 0);
		add_option('w2gm_enable_geolocation', 0);
		add_option('w2gm_default_geocoding_location', '');
		add_option('w2gm_addresses_order', array("line_1", "comma1", "line_2", "comma2", "location", "space1", "zip"));
		add_option('w2gm_enable_address_line_1', 1);
		add_option('w2gm_enable_address_line_2', 1);
		add_option('w2gm_enable_postal_index', 1);
		add_option('w2gm_enable_additional_info', 1);
		add_option('w2gm_enable_manual_coords', 1);
		add_option('w2gm_enable_users_markers', 1);
		add_option('w2gm_map_markers_type', 'icons');
		add_option('w2gm_default_marker_color', '#2393ba');
		add_option('w2gm_default_marker_icon', '');
		add_option('w2gm_map_marker_width', 48);
		add_option('w2gm_map_marker_height', 48);
		add_option('w2gm_map_marker_anchor_x', 24);
		add_option('w2gm_map_marker_anchor_y', 48);
		add_option('w2gm_map_infowindow_width', 410);
		add_option('w2gm_map_infowindow_offset', 50);
		add_option('w2gm_map_infowindow_logo_width', 150);
		add_option('w2gm_send_expiration_notification_days', 1);
		add_option('w2gm_preexpiration_notification', 'Your listing "[listing]" will expiry in [days] days.');
		add_option('w2gm_expiration_notification', 'Your listing "[listing]" had expired. You can renew it here [link]');
		add_option('w2gm_google_api_key', '');
		add_option('w2gm_notinclude_jqueryui_css', 0);
		add_option('w2gm_address_autocomplete', 1);
		add_option('w2gm_address_geocode', 0);
		add_option('w2gm_compare_palettes', 0);
		add_option('w2gm_color_scheme', 'default');
		add_option('w2gm_links_color', '#2393ba');
		add_option('w2gm_links_hover_color', '#2a6496');
		add_option('w2gm_button_1_color', '#2393ba');
		add_option('w2gm_button_2_color', '#1f82a5');
		add_option('w2gm_button_text_color', '#FFFFFF');
		add_option('w2gm_button_gradient', 0);
		add_option('w2gm_search_1_color', '#bafefe');
		add_option('w2gm_search_2_color', '#47c6c6');
		add_option('w2gm_search_text_color', '#FFFFFF');
		add_option('w2gm_primary_color', '#2393ba');
		add_option('w2gm_listing_title_font', 25);
		add_option('w2gm_jquery_ui_schemas', 'redmond');
		add_option('w2gm_enable_full_screen', 1);
		add_option('w2gm_enable_wheel_zoom', 1);
		add_option('w2gm_enable_dragging_touchscreens', 1);
		add_option('w2gm_center_map_onclick', 0);
		add_option('w2gm_hide_search_on_map_mobile', 0);
		add_option('w2gm_hide_listings_creation_date', 1);
		add_option('w2gm_hide_author_link', 1);
		add_option('w2gm_map_language_from_wpml', 0);
		add_option('w2gm_enable_listings_sidebar', 0);
		add_option('w2gm_listings_sidebar_position', 'right');
		add_option('w2gm_listings_sidebar_width', 350);
		add_option('w2gm_address_autocomplete_code', "0");
		add_option('w2gm_admin_notifications_email', get_option('admin_email'));

		add_option('w2gm_installed_maps_lite', true);
		add_option('w2gm_installed_maps_lite_version', W2GM_LITE_VERSION);
	} elseif (get_option('w2gm_installed_maps_lite_version') != W2GM_LITE_VERSION) {
		$upgrades_list = array(
				'1.0.1',
		);

		$old_version = get_option('w2gm_installed_maps_lite_version');
		foreach ($upgrades_list AS $upgrade_version) {
			if (!$old_version || version_compare($old_version, $upgrade_version, '<')) {
				$upgrade_function_name = 'w2gm_upgrade_to_' . str_replace('.', '_', $upgrade_version);
				if (function_exists($upgrade_function_name))
					$upgrade_function_name();
				do_action('w2gm_lite_version_upgrade', $upgrade_version);
			}
		}

		w2gm_save_dynamic_css();

		update_option('w2gm_installed_maps_lite_version', W2GM_LITE_VERSION);
		
		echo '<script>location.reload();</script>';
	}
	
	global $w2gm_instance;
	$w2gm_instance->loadClasses();
}

function w2gm_upgrade_to_1_0_1() {
	add_option('w2gm_admin_notifications_email', get_option('admin_email'));
}

?>