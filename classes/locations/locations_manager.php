<?php 

class w2gm_locations_manager {
	
	public function __construct() {
		global $pagenow;

		if ($pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'admin-ajax.php') {
			add_action('add_meta_boxes', array($this, 'removeLocationsMetabox'));
			add_action('add_meta_boxes', array($this, 'addLocationsMetabox'), 300);
			
			add_action('wp_ajax_w2gm_tax_dropdowns_hook', 'w2gm_tax_dropdowns_updateterms');
			add_action('wp_ajax_nopriv_w2gm_tax_dropdowns_hook', 'w2gm_tax_dropdowns_updateterms');
		
			add_action('wp_ajax_w2gm_add_location_in_metabox', array($this, 'add_location_in_metabox'));
			add_action('wp_ajax_nopriv_w2gm_add_location_in_metabox', array($this, 'add_location_in_metabox'));

			// Will be deprecated soon
			add_action('wp_ajax_w2gm_select_map_icon', array($this, 'select_map_icon'));
			add_action('wp_ajax_nopriv_w2gm_select_map_icon', array($this, 'select_map_icon'));
			
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'));
		}

		add_filter('manage_' . W2GM_LOCATIONS_TAX . '_custom_column', array($this, 'taxonomy_rows'), 15, 3);
		add_filter('manage_edit-' . W2GM_LOCATIONS_TAX . '_columns',  array($this, 'taxonomy_columns'));
	}
	
	// remove native locations taxonomy metabox from sidebar
	public function removeLocationsMetabox() {
		remove_meta_box(W2GM_LOCATIONS_TAX . 'div', W2GM_POST_TYPE, 'side');
	}
	
	public function addLocationsMetabox($post_type) {
		if ($post_type == W2GM_POST_TYPE && get_option('w2gm_locations_number') > 0) {
			add_meta_box('w2gm_locations',
					__('Listing locations', 'W2GM'),
					array($this, 'listingLocationsMetabox'),
					W2GM_POST_TYPE,
					'normal',
					'high');
		}
	}

	public function listingLocationsMetabox($post) {
		global $w2gm_instance;
			
		$listing = w2gm_getCurrentListingInAdmin();
		$locations_levels = $w2gm_instance->locations_levels;
		w2gm_renderTemplate('locations/locations_metabox.tpl.php', array('listing' => $listing, 'locations_levels' => $locations_levels));
	}
	
	public function add_location_in_metabox() {
		global $w2gm_instance;
			
		if (isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
			$listing = new w2gm_listing();
			$listing->loadListingFromPost($_POST['post_id']);
	
			$locations_levels = $w2gm_instance->locations_levels;
			w2gm_renderTemplate('locations/locations_in_metabox.tpl.php', array('listing' => $listing, 'location' => new w2gm_location, 'locations_levels' => $locations_levels, 'delete_location_link' => true));
		}
		die();
	}
	
	public function select_map_icon() {
		$custom_map_icons = array();
	
		$custom_map_icons_themes = scandir(W2GM_MAP_ICONS_PATH . 'icons/');
		foreach ($custom_map_icons_themes AS $dir) {
			if (is_dir(W2GM_MAP_ICONS_PATH . 'icons/' . $dir) && $dir != '.' && $dir != '..') {
				$custom_map_icons_theme_files = scandir(W2GM_MAP_ICONS_PATH . 'icons/' . $dir);
				foreach ($custom_map_icons_theme_files AS $file)
				if (is_file(W2GM_MAP_ICONS_PATH . 'icons/' . $dir . '/' . $file) && $file != '.' && $file != '..')
					$custom_map_icons[$dir][] = $file;
			}
		}

		w2gm_renderTemplate('locations/select_icons.tpl.php', array('custom_map_icons' => $custom_map_icons));
		die();
	}

	public function validateLocations(&$errors) {
		global $w2gm_instance;

		$validation = new w2gm_form_validation();
		$validation->set_rules('w2gm_location[]', __('Listing location', 'W2GM'), 'is_natural');
		$validation->set_rules('selected_tax[]', __('Selected location', 'W2GM'), 'is_natural');
		$validation->set_rules('address_line_1[]', __('Address line 1', 'W2GM'));
		$validation->set_rules('address_line_2[]', __('Address line 2', 'W2GM'));
		$validation->set_rules('zip_or_postal_index[]', __('Zip code', 'W2GM'));
		$validation->set_rules('additional_info[]', __('Additional info', 'W2GM'));
		$validation->set_rules('manual_coords[]', __('Use manual coordinates', 'W2GM'), 'is_checked');
		$validation->set_rules('map_coords_1[]', __('Latitude', 'W2GM'), 'numeric');
		$validation->set_rules('map_coords_2[]', __('Longitude', 'W2GM'), 'numeric');
		$validation->set_rules('map_icon_file[]', __('Map icon file', 'W2GM'));
		$validation->set_rules('map_zoom', __('Map zoom', 'W2GM'), 'is_natural');
	
		if (!$validation->run()) {
			$errors[] = $validation->error_string();
			//return false;
		} else {
			$passed = true;
			if ($w2gm_instance->content_fields->getContentFieldBySlug('address')->is_required) {
				$address_passed = true;
				if ($validation_results = $validation->result_array()) {
					foreach ($validation_results['w2gm_location[]'] AS $key=>$value) {
						if (!$validation_results['selected_tax[]'][$key] && !$validation_results['address_line_1[]'][$key] && !$validation_results['zip_or_postal_index[]'][$key])
							$address_passed = false;
					}
				}
				if (!$address_passed) {
					$errors[] = __('Location, address or zip is required!', 'W2GM');
					$passed = false;
				}
			}

			$map_passed = false;
			if ($validation_results = $validation->result_array()) {
				foreach ($validation_results['w2gm_location[]'] AS $key=>$value) {
					if ($validation_results['map_coords_1[]'][$key] || $validation_results['map_coords_2[]'][$key])
						$map_passed = true;
				}
			}
			if (!$map_passed) {
				$errors[] = __('Listing must contain at least one map marker!', 'W2GM');
				$passed = false;
			}

			//if ($passed)
				return $validation->result_array();
			//else 
				//return false;
		}
	}
	
	public function saveLocations($post_id, $validation_results) {
		global $wpdb;
	
		$this->deleteLocations($post_id);
	
		if (isset($validation_results['w2gm_location[]'])) {
			// remove unauthorized locations
			$validation_results['w2gm_location[]'] = array_slice($validation_results['w2gm_location[]'], 0, get_option('w2gm_locations_number'), true);
	
			foreach ($validation_results['w2gm_location[]'] AS $key=>$value) {
				if (
					$validation_results['selected_tax[]'][$key] ||
					$validation_results['address_line_1[]'][$key] ||
					$validation_results['address_line_2[]'][$key] ||
					$validation_results['zip_or_postal_index[]'][$key] ||
					($validation_results['map_coords_1[]'][$key] || $validation_results['map_coords_2[]'][$key])
				) {
					$insert_values = array(
							'post_id' => $post_id,
							'location_id' => esc_sql($validation_results['selected_tax[]'][$key]),
							'address_line_1' => esc_sql($validation_results['address_line_1[]'][$key]),
							'address_line_2' => esc_sql($validation_results['address_line_2[]'][$key]),
							'zip_or_postal_index' => esc_sql($validation_results['zip_or_postal_index[]'][$key]),
							'additional_info' => (isset($validation_results['additional_info[]'][$key]) ? esc_sql($validation_results['additional_info[]'][$key]) : ''),
					);
					if (is_array($validation_results['manual_coords[]'])) {
						if (in_array($key, array_keys($validation_results['manual_coords[]'])) && $validation_results['manual_coords[]'][$key])
							$insert_values['manual_coords'] = 1;
						else
							$insert_values['manual_coords'] = 0;
					} else
						$insert_values['manual_coords'] = 0;
					$insert_values['map_coords_1'] = $validation_results['map_coords_1[]'][$key];
					$insert_values['map_coords_2'] = $validation_results['map_coords_2[]'][$key];
					if (get_option('w2gm_enable_users_markers'))
						$insert_values['map_icon_file'] = esc_sql($validation_results['map_icon_file[]'][$key]);
					$keys = array_keys($insert_values);
					
					// duplicate locations data in postmeta in order to export it as ordinary wordpress fields
					foreach ($keys AS $key)
						add_post_meta($post_id, '_'.$key, $insert_values[$key]);
					
					array_walk($keys, create_function('&$val', '$val = "`".$val."`";'));
					array_walk($insert_values, create_function('&$val', '$val = "\'".$val."\'";'));
					$wpdb->query("INSERT INTO {$wpdb->w2gm_locations_relationships} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $insert_values) . ")");
				}
			}

			if ($validation_results['selected_tax[]']) {
				array_walk($validation_results['selected_tax[]'], create_function('&$val', '$val = intval($val);'));
				wp_set_object_terms($post_id, $validation_results['selected_tax[]'], W2GM_LOCATIONS_TAX);
			}
	
			add_post_meta($post_id, '_map_zoom', $validation_results['map_zoom']);
		}
	}

	public function deleteLocations($post_id) {
		global $wpdb;

		$wpdb->delete($wpdb->w2gm_locations_relationships, array('post_id' => $post_id));
		wp_delete_object_term_relationships($post_id, W2GM_LOCATIONS_TAX);
		delete_post_meta($post_id, '_location_id');
		delete_post_meta($post_id, '_address_line_1');
		delete_post_meta($post_id, '_address_line_2');
		delete_post_meta($post_id, '_zip_or_postal_index');
		delete_post_meta($post_id, '_additional_info');
		delete_post_meta($post_id, '_manual_coords');
		delete_post_meta($post_id, '_map_coords_1');
		delete_post_meta($post_id, '_map_coords_2');
		delete_post_meta($post_id, '_map_icon_file');
		delete_post_meta($post_id, '_map_zoom');
	}
	
	public function taxonomy_columns($original_columns) {
		$new_columns = $original_columns;
		array_splice($new_columns, 1);
		$new_columns['w2gm_location_id'] = __('Location ID', 'W2GM');
		if (isset($original_columns['description']))
			unset($original_columns['description']);
		return array_merge($new_columns, $original_columns);
	}
	
	public function taxonomy_rows($row, $column_name, $term_id) {
		if ($column_name == 'w2gm_location_id') {
			return $row . $term_id;
		}
		return $row;
	}

	public function admin_enqueue_scripts_styles() {
		wp_localize_script(
				'w2gm_js_functions',
				'w2gm_google_maps_callback',
				array(
						'callback' => 'w2gm_load_maps_api_backend'
				)
		);
		wp_enqueue_script('w2gm_google_maps_edit');
	}
}

?>