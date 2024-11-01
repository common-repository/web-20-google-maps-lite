<?php

class w2gm_google_maps {
	public $args;
	public $unique_map_id;
	
	public $map_zoom;
	public $locations_array = array();
	public $locations_option_array = array();

	public static $map_content_fields;

	public function __construct($args = array()) {
		$this->args = $args;
	}
	
	public function setUniqueId($unique_id) {
		$this->unique_map_id = $unique_id;
	}

	public function collectLocations($listing) {
		global $w2gm_instance, $w2gm_address_locations, $w2gm_tax_terms_locations;

		if (count($listing->locations) == 1)
			$this->map_zoom = $listing->map_zoom;

		foreach ($listing->locations AS $location) {
			if ((!$w2gm_address_locations || in_array($location->id, $w2gm_address_locations)) && (!$w2gm_tax_terms_locations || in_array($location->selected_location, $w2gm_tax_terms_locations))) {
				if ($location->map_coords_1 != '0.000000' || $location->map_coords_2 != '0.000000') {
					$logo_image = '';
					if ($listing->logo_image) {
						$src = wp_get_attachment_image_src($listing->logo_image, array(get_option('w2gm_map_infowindow_logo_width'), get_option('w2gm_map_infowindow_logo_width')));
						$logo_image = $src[0];
					} elseif (get_option('w2gm_enable_nologo') && get_option('w2gm_nologo_url')) {
						$logo_image = get_option('w2gm_nologo_url');
					}

					if ($w2gm_instance->content_fields->getMapContentFields())
						$content_fields_output = $listing->setMapContentFields($w2gm_instance->content_fields->getMapContentFields(), $location);
					else 
						$content_fields_output = '';
	
					$this->listings_array[] = $listing;
					$this->locations_array[] = $location;
					$this->locations_option_array[] = array(
							$location->id,
							$location->map_coords_1,
							$location->map_coords_2,
							$location->map_icon_file,
							$location->map_icon_color,
							$listing->map_zoom,
							esc_js($listing->title()),
							$logo_image,
							$content_fields_output
					);
				}
			}
		}

		if ($this->locations_option_array)
			return true;
		else
			return false;
	}
	
	public function collectLocationsForAjax($listing) {	
		global $w2gm_address_locations, $w2gm_tax_terms_locations;

		foreach ($listing->locations AS $location) {
			if ((!$w2gm_address_locations || in_array($location->id, $w2gm_address_locations))  && (!$w2gm_tax_terms_locations || in_array($location->selected_location, $w2gm_tax_terms_locations))) {
				if ($location->map_coords_1 != '0.000000' || $location->map_coords_2 != '0.000000') {
					$this->listings_array[] = $listing;
					$this->locations_array[] = $location;
					$this->locations_option_array[] = array(
							$location->id,
							$location->map_coords_1,
							$location->map_coords_2,
							$location->map_icon_file,
							$location->map_icon_color,
							null,
							null,
							null,
							null
					);
				}
			}
		}
		if ($this->locations_option_array)
			return true;
		else
			return false;
	}
	
	public function buildListingsContent($show_directions_button, $show_readmore_button) {
		$out = '';
		foreach ($this->locations_array AS $key=>$location) {
			$listing = $this->listings_array[$key];
			$listing->setContentFields();

			$out .= w2gm_renderTemplate('frontend/listing_location.tpl.php', array('listing' => $listing, 'location' => $location, 'show_directions_button' => $show_directions_button, 'show_readmore_button' => $show_readmore_button), true);
		}
		return $out;
	}

	public function display($show_directions = true, $static_image = false, $enable_radius_circle = true, $enable_clusters = true, $show_directions_button = true, $show_readmore_button = true, $width = false, $height = false, $sticky_scroll = false, $sticky_scroll_toppadding = 10, $map_style_name = 'default', $enable_full_screen = true, $enable_wheel_zoom = true, $enable_dragging_touchscreens = true, $center_map_onclick = false, $enable_listings_sidebar = false, $listings_sidebar_position = 'left', $listings_sidebar_width = 350) {
		//if ($this->locations_option_array || $this->is_ajax_markers_management()) {
			$locations_options = json_encode($this->locations_option_array);
			$map_args = json_encode($this->args);
			$attributes = array(
						'locations_options' => $locations_options,
						'locations_array' => $this->locations_array,
						'show_directions' => $show_directions,
						'static_image' => $static_image,
						'enable_radius_circle' => $enable_radius_circle,
						'enable_clusters' => $enable_clusters,
						'map_zoom' => $this->map_zoom,
						'show_directions_button' => $show_directions_button,
						'show_readmore_button' => $show_readmore_button,
						'map_style_name' => $map_style_name,
						'width' => $width,
						'height' => $height,
						'sticky_scroll' => $sticky_scroll,
						'sticky_scroll_toppadding' => $sticky_scroll_toppadding,
						'enable_full_screen' => $enable_full_screen,
						'enable_wheel_zoom' => $enable_wheel_zoom,
						'enable_dragging_touchscreens' => $enable_dragging_touchscreens,
						'center_map_onclick' => $center_map_onclick,
						'enable_listings_sidebar' => $enable_listings_sidebar,
						'unique_map_id' => $this->unique_map_id,
						'map_args' => $map_args
			);
			if ($enable_listings_sidebar) {
				$attributes['listings_position'] = $listings_sidebar_position;
				$attributes['listings_width'] = $listings_sidebar_width;
				$attributes['listings_content'] = $this->buildListingsContent($show_directions_button, $show_readmore_button);
			}

			w2gm_renderTemplate('google_map.tpl.php', $attributes);
			wp_enqueue_script('google_maps_infobox');
		//}
	}
	
	public function is_ajax_markers_management() {
		if (isset($this->args['ajax_loading']) && $this->args['ajax_loading'] && ((isset($this->args['start_address']) && $this->args['start_address']) || ((isset($this->args['start_latitude']) && $this->args['start_latitude']) && (isset($this->args['start_longitude']) && $this->args['start_longitude']))))
			return true;
		else
			return false;
	}
}

?>