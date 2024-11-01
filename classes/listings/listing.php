<?php 

class w2gm_listing {
	public $post;
	public $expiration_date;
	public $listing_created = false;
	public $status; // active, expired, unpaid, stopped
	public $categories = array();
	public $locations = array();
	public $content_fields = array();
	public $map_zoom;
	public $logo_image;
	public $map;

	// Load existed listing
	public function loadListingFromPost($post) {
		if (is_object($post))
			$this->post = $post;
		elseif (is_numeric($post))
			if (!($this->post = get_post($post)))
				return false;

		$this->setMetaInformation();
		$this->setLocations();
		$this->setContentFields();
		$this->setMapZoom();
		$this->setMedia();

		apply_filters('w2gm_listing_loading', $this);
		return true;
	}

	public function setMetaInformation() {
		if (!get_option('w2gm_eternal_active_period'))
			$this->expiration_date = get_post_meta($this->post->ID, '_expiration_date', true);

		$this->status = get_post_meta($this->post->ID, '_listing_status', true);

		$this->listing_created = get_post_meta($this->post->ID, '_listing_created', true);

		return $this->expiration_date;
	}

	public function setLocations() {
		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM {$wpdb->w2gm_locations_relationships} WHERE post_id=".$this->post->ID, ARRAY_A);
		
		foreach ($results AS $row) {
			if ($row['location_id'] || $row['map_coords_1'] != '0.000000' || $row['map_coords_2'] != '0.000000' || $row['address_line_1'] || $row['zip_or_postal_index']) {
				$location = new w2gm_location($this->post->ID);
				$location_settings = array(
						'id' => $row['id'],
						'selected_location' => $row['location_id'],
						'address_line_1' => $row['address_line_1'],
						'address_line_2' => $row['address_line_2'],
						'zip_or_postal_index' => $row['zip_or_postal_index'],
						'additional_info' => $row['additional_info'],
				);
				$location_settings['manual_coords'] = w2gm_getValue($row, 'manual_coords');
				$location_settings['map_coords_1'] = w2gm_getValue($row, 'map_coords_1');
				$location_settings['map_coords_2'] = w2gm_getValue($row, 'map_coords_2');
				if (get_option('w2gm_map_markers_type') == 'images') {
					if (($marker = w2gm_getValue($row, 'map_icon_file')) && strpos($marker, 'w2gm-fa-') === false) {
						$location_settings['map_icon_file'] = $marker;
						$location_settings['map_icon_manually_selected'] = true;
					} else {
						$location_settings['map_icon_manually_selected'] = false;
						if ($categories = wp_get_object_terms($this->post->ID, W2GM_CATEGORIES_TAX, array('orderby' => 'name'))) {
							$images = get_option('w2gm_categories_marker_images');
							$image_found = false;
							foreach ($categories AS $category_obj) {
								if (!$image_found && isset($images[$category_obj->term_id])) {
									$location_settings['map_icon_file'] = $images[$category_obj->term_id];
									$image_found = true;
								}
								if ($image_found)
									break;
								if ($parent_categories = w2gm_get_term_parents_ids($category_obj->term_id, W2GM_CATEGORIES_TAX)) {
									foreach ($parent_categories AS $parent_category_id) {
										if (!$image_found && isset($images[$parent_category_id])) {
											$location_settings['map_icon_file'] = $images[$parent_category_id];
											$image_found = true;
										}
										if ($image_found) {
											break;
											break;
										}
									}
								}
							}
						}
					}
				} else {
					$marker = w2gm_getValue($row, 'map_icon_file');
					if ($marker && in_array($marker, w2gm_get_fa_icons_names())) {
						$location_settings['map_icon_file'] = $marker;
						$location_settings['map_icon_manually_selected'] = true;
						if ($categories = wp_get_object_terms($this->post->ID, W2GM_CATEGORIES_TAX, array('orderby' => 'name'))) {
							$colors = get_option('w2gm_categories_marker_colors');
							$color_found = false;
							foreach ($categories AS $category_obj) {
								if (!$color_found && isset($colors[$category_obj->term_id])) {
									$location_settings['map_icon_color'] = $colors[$category_obj->term_id];
									$color_found = true;
								}
								if ($color_found)
									break;
								if ($parent_categories = w2gm_get_term_parents_ids($category_obj->term_id, W2GM_CATEGORIES_TAX)) {
									foreach ($parent_categories AS $parent_category_id) {
										if (!$color_found && isset($colors[$parent_category_id])) {
											$location_settings['map_icon_color'] = $colors[$parent_category_id];
											$color_found = true;
										}
										if ($color_found) {
											break;
											break;
										}
									}
								}
							}
						}
					} else {
						$location_settings['map_icon_manually_selected'] = false;
						if ($categories = wp_get_object_terms($this->post->ID, W2GM_CATEGORIES_TAX, array('orderby' => 'name'))) {
							$icons = get_option('w2gm_categories_marker_icons');
							$colors = get_option('w2gm_categories_marker_colors');
							$icon_found = false;
							$color_found = false;
							foreach ($categories AS $category_obj) {
								if (!$icon_found && isset($icons[$category_obj->term_id])) {
									$location_settings['map_icon_file'] = $icons[$category_obj->term_id];
									$icon_found = true;
								}
								if (!$color_found && isset($colors[$category_obj->term_id])) {
									$location_settings['map_icon_color'] = $colors[$category_obj->term_id];
									$color_found = true;
								}
								if ($icon_found && $color_found)
									break;
								if ($parent_categories = w2gm_get_term_parents_ids($category_obj->term_id, W2GM_CATEGORIES_TAX)) {
									foreach ($parent_categories AS $parent_category_id) {
										if (!$icon_found && isset($icons[$parent_category_id])) {
											$location_settings['map_icon_file'] = $icons[$parent_category_id];
											$icon_found = true;
										}
										if (!$color_found && isset($colors[$parent_category_id])) {
											$location_settings['map_icon_color'] = $colors[$parent_category_id];
											$color_found = true;
										}
										if ($icon_found && $color_found) {
											break;
											break;
										}
									}
								}
								// icon from one category and color from another - this would be bad idea
								if ($icon_found || $color_found)
									break;
							}
						}
					}
				}
				$location->createLocationFromArray($location_settings);
				
				$this->locations[] = $location;
			}
		}
	}

	public function setMapZoom() {
		if (!$this->map_zoom = get_post_meta($this->post->ID, '_map_zoom', true))
			$this->map_zoom = get_option('w2gm_start_zoom');
	}

	public function setContentFields() {
		global $w2gm_instance;

		$post_categories_ids = wp_get_post_terms($this->post->ID, W2GM_CATEGORIES_TAX, array('fields' => 'ids'));
		$this->content_fields = $w2gm_instance->content_fields->loadValues($this->post->ID, $post_categories_ids);
	}
	
	public function setMedia() {
		if (get_option('w2gm_listing_logo_enabled')) {
			$this->logo_image = get_post_meta($this->post->ID, '_attached_image', true);
		}
	}

	public function getContentField($field_id) {
		if (isset($this->content_fields[$field_id]))
			return $this->content_fields[$field_id];
	}
	
	public function renderContentField($field_id) {
		if (isset($this->content_fields[$field_id]))
			$this->content_fields[$field_id]->renderOutput($this);
	}

	public function display($is_single = false, $return = false) {
		return w2gm_renderTemplate('frontend/listing.tpl.php', array('listing' => $this, 'is_single' => $is_single), $return);
	}
	
	public function renderContentFields($is_single = true) {
		global $w2gm_instance;

		$content_fields_on_single = array();
		foreach ($this->content_fields AS $content_field) {
			if (
				$content_field->isNotEmpty($this) &&
				(!$is_single || ($is_single && $content_field->on_listing_page))
			)
				if ($is_single)
					$content_fields_on_single[] = $content_field;
				else 
					$content_field->renderOutput($this);
		}

		if ($is_single && $content_fields_on_single) {
			$content_fields_by_groups = $w2gm_instance->content_fields->sortContentFieldsByGroups($content_fields_on_single);
			foreach ($content_fields_by_groups AS $item) {
				if (is_a($item, 'w2gm_content_field') || (is_a($item, 'w2gm_content_fields_group') && !$item->on_tab))
					$item->renderOutput($this, $is_single);
			}
		}
	}

	public function renderSidebarContentFields() {
		$content_fields = $this->content_fields;

		foreach ($content_fields AS $content_field) {
			if (
				$content_field->isNotEmpty($this) &&
				$content_field->on_listing_sidebar &&
				// address field always will be the first, so we remove it from this output and will output it directly in the template
				$content_field->type != 'address'
			)
				$content_field->renderOutput($this);
		}
	}
	
	public function renderAddressContentField($location) {
		foreach ($this->content_fields AS $content_field) {
			if ($content_field->type == 'address') {
				if ($content_field->on_listing_sidebar)
					$content_field->renderOutputForSidebar($location, $this);
				break;
			}
		}
	}
	
	public function getFieldsGroupsOnTabs() {
		global $w2gm_instance;

		$fields_groups = array();
		foreach ($this->content_fields AS $content_field)
			if (
				$content_field->group_id &&
				$content_field->isNotEmpty($this) &&
				($content_fields_group = $w2gm_instance->content_fields->getContentFieldsGroupById($content_field->group_id)) &&
				$content_fields_group->on_tab &&
				!in_array($content_field->group_id, array_keys($fields_groups))
			) {
				$content_fields_group->setContentFields($this->content_fields);
				if ($content_fields_group->content_fields_array)
					$fields_groups[$content_field->group_id] = $content_fields_group;
			}
		return $fields_groups;
	}

	public function isMap() {
		foreach ($this->locations AS $location)
			if ($location->map_coords_1 != '0.000000' || $location->map_coords_2 != '0.000000')
				return true;

		return false;
	}
	
	public function renderMap($unique_map_id = null, $show_directions = true, $static_image = false, $enable_radius_circle = false, $enable_clusters = false, $show_directions_button = false, $show_readmore_button = false) {
		$this->map = new w2gm_google_maps;
		$this->map->setUniqueId($unique_map_id);
		$this->map->collectLocations($this);
		$this->map->display($show_directions, $static_image, $enable_radius_circle, $enable_clusters, $show_directions_button, $show_readmore_button, false, 600, false, false, get_option('w2gm_map_style'), false, false, false, false, false, null, null);
	}
	
	public function title() {
		return get_the_title($this->post);
	}

	public function processActivate($is_renew = true) {
		$continue = true;
		if ($is_renew)
			$continue = apply_filters('w2gm_listing_renew', $continue, $this);
		
		if ($continue) {
			$listings = array();

			// adapted for WPML
			global $sitepress;
			if (function_exists('wpml_object_id_filter') && $sitepress) {
				$trid = $sitepress->get_element_trid($this->post->ID, 'post_' . W2GM_POST_TYPE);
				$translations = $sitepress->get_element_translations($trid, 'post_' . W2GM_POST_TYPE, false, true);
				foreach ($translations AS $lang=>$translation) {
					$listing = new w2gm_listing();
					$listing->loadListingFromPost($translation->element_id);
					$listings[] = $listing;
				}
			} else
				$listings[] = $this;

			foreach ($listings AS $listing) {
				if (!get_option('w2gm_eternal_active_period')) {
					$expiration_date = w2gm_sumDates(time(), get_option('w2gm_active_period_days'), get_option('w2gm_active_period_months'), get_option('w2gm_active_period_years'));
					update_post_meta($listing->post->ID, '_expiration_date', $expiration_date);
				}
				update_post_meta($listing->post->ID, '_order_date', time());
				update_post_meta($listing->post->ID, '_listing_status', 'active');
				
				delete_post_meta($listing->post->ID, '_expiration_notification_sent');
				delete_post_meta($listing->post->ID, '_preexpiration_notification_sent');
		
				wp_update_post(array('ID' => $listing->post->ID, 'post_status' => 'publish'));

				do_action('w2gm_listing_process_activate', $listing, $is_renew);
			}
			return true;
		}
	}
	
	public function saveExpirationDate($date_array) {
		$new_tmstmp = $date_array['expiration_date_tmstmp'] + $date_array['expiration_date_hour']*3600 + $date_array['expiration_date_minute']*60;
		
		$listings_ids = array();
		
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$trid = $sitepress->get_element_trid($this->post->ID, 'post_' . W2GM_POST_TYPE);
			$translations = $sitepress->get_element_translations($trid);
			foreach ($translations AS $lang=>$translation)
				$listings_ids[] = $translation->element_id;
		} else
			$listings_ids[] = $this->post->ID;

		$updated = false;
		foreach ($listings_ids AS $listing_id)
			if ($new_tmstmp != get_post_meta($listing_id, '_expiration_date', true)) {
				update_post_meta($listing_id, '_expiration_date', $new_tmstmp);
				$updated = true;
			}

		return $updated;
	}

	/**
	 * Load existed listing especially for map info window
	 * 
	 * @param $post is required and must be object
	 */
	public function loadListingForMap($post) {
		$this->post = $post;
	
		$this->setLocations();
		$this->setMapZoom();
		$this->setMedia();

		apply_filters('w2gm_listing_map_loading', $this);
		return true;
	}

	/**
	 * Load existed listing especially for AJAX map - set only locations
	 * 
	 * @param $post is required and must be object
	 */
	public function loadListingForAjaxMap($post) {
		$this->post = $post;

		$this->setLocations();
		$this->setMedia();

		apply_filters('w2gm_listing_map_loading', $this);
		return true;
	}

	public function setMapContentFields($map_content_fields, $location) {
		$post_categories_ids = wp_get_post_terms($this->post->ID, W2GM_CATEGORIES_TAX, array('fields' => 'ids'));
		$content_fields_output = array(
			$location->renderInfoFieldForMap()
		);
		
		foreach($map_content_fields AS $field_slug=>$content_field) {
			// is it native content field
			if (is_a($content_field, 'w2gm_content_field')) {
				if (!$content_field->isCategories() || $content_field->categories === array() || (is_array($content_field->categories) && !is_wp_error($post_categories_ids) && array_intersect($content_field->categories, $post_categories_ids))) {
					$content_field->loadValue($this->post->ID);
					$output = $content_field->renderOutputForMap($location, $this);
					$content_fields_output[] = apply_filters('w2gm_map_content_field_output', $output, $content_field, $location, $this);
				} else 
					$content_fields_output[] = null;
			} else
				$content_fields_output[] = apply_filters('w2gm_map_info_window_fields_values', $content_field, $field_slug, $this);
		}

		return $content_fields_output;
	}

	public function getExcerptFromContent($words_length = 35) {
		$the_excerpt = strip_tags(strip_shortcodes($this->post->post_content));
		$words = explode(' ', $the_excerpt, $words_length + 1);
		if (count($words) > $words_length) {
			array_pop($words);
			array_push($words, '…');
			$the_excerpt = implode(' ', $words);
		}
		return $the_excerpt;
	}
	
	public function increaseClicksStats() {
		$date = date('n-Y');
		$clicks_data = get_post_meta($this->post->ID, '_clicks_data', true);
		if (isset($clicks_data[$date]))
			$clicks_data[$date] = $clicks_data[$date]+1;
		else
			$clicks_data[$date] = 1;
		update_post_meta($this->post->ID, '_clicks_data', $clicks_data);
	
		$total_clicks = get_post_meta($this->post->ID, '_total_clicks', true);
		if ($total_clicks)
			$total_clicks++;
		else
			$total_clicks = 1;
		update_post_meta($this->post->ID, '_total_clicks', $total_clicks);
	}
}

?>