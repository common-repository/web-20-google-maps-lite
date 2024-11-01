<?php 

class w2gm_map_controller extends w2gm_frontend_controller {
	public function init($args = array()) {
		global $w2gm_instance;
		
		/* if (!isset($args['uid']) || $args['uid'] == '')
			$args['uid'] = 'w2gm_unique_id'; */ // this parameter may be easily overwritten (need for default compatibility with search block)
		
		parent::init($args);

		$shortcode_atts = array_merge(array(
				'num' => (get_option('w2gm_max_num') ? get_option('w2gm_max_num') : -1),
				'width' => get_option('w2gm_default_map_width'),
				'height' => get_option('w2gm_default_map_height'),
				'radius_circle' => 0,
				'clusters' => get_option('w2gm_enable_clusters'),
				'sticky_scroll' => 0,
				'sticky_scroll_toppadding' => 10,
				'show_directions_button' => get_option('w2gm_show_directions_button'),
				'show_readmore_button' => get_option('w2gm_show_readmore_button'),
				'ajax_loading' => get_option('w2gm_ajax_loading'),
				'ajax_markers_loading' => get_option('w2gm_ajax_markers_loading'),
				'geolocation' => get_option('w2gm_enable_geolocation'),
				'address' => '',
				'radius' => 0,
				'start_address' => get_option('w2gm_start_address'),
				'start_latitude' => get_option('w2gm_start_latitude'),
				'start_longitude' => get_option('w2gm_start_longitude'),
				'start_zoom' => get_option('w2gm_start_zoom'),
				'map_style' => get_option('w2gm_map_style'),
				'include_categories_children' => 0,
				'include_get_params' => 1,
				'author' => 0,
				'enable_full_screen' => get_option('w2gm_enable_full_screen'),
				'enable_wheel_zoom' => get_option('w2gm_enable_wheel_zoom'),
				'enable_dragging_touchscreens' => get_option('w2gm_enable_dragging_touchscreens'),
				'center_map_onclick' => get_option('w2gm_center_map_onclick'),
				'enable_listings_sidebar' => get_option('w2gm_enable_listings_sidebar'),
				'listings_sidebar_position' => get_option('w2gm_listings_sidebar_position'),
				'listings_sidebar_width' => get_option('w2gm_listings_sidebar_width'),
				'uid' => null,
		), $args);
		$shortcode_atts = apply_filters('w2gm_related_shortcode_args', $shortcode_atts, $args);
		$this->args = $shortcode_atts;

		$args = array(
				'post_type' => W2GM_POST_TYPE,
				'post_status' => 'publish',
				'meta_query' => array(array('key' => '_listing_status', 'value' => 'active')),
				'posts_per_page' => ($shortcode_atts['num'] ? $shortcode_atts['num'] : -1),
		);
		$args = apply_filters('w2gm_search_args', $args, $this->args, $this->args['include_get_params'], $this->hash);

		if (isset($this->args['post__in'])) {
			$args = array_merge($args, array('post__in' => explode(',', $this->args['post__in'])));
		}

		if (isset($this->args['neLat']) && isset($this->args['neLng']) && isset($this->args['swLat']) && isset($this->args['swLng'])) {
			$y1 = $this->args['neLat'];
			$y2 = $this->args['swLat'];
			// when zoom level 2 - there may be problems with neLng and swLng of bounds
			if ($this->args['neLng'] > $this->args['swLng']) {
				$x1 = $this->args['neLng'];
				$x2 = $this->args['swLng'];
			} else {
				$x1 = 180;
				$x2 = -180;
			}
			
			global $wpdb;
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT DISTINCT
					post_id FROM {$wpdb->w2gm_locations_relationships} AS w2gm_lr
				WHERE MBRContains(
				GeomFromText('Polygon((%f %f,%f %f,%f %f,%f %f,%f %f))'),
				GeomFromText(CONCAT('POINT(',w2gm_lr.map_coords_1,' ',w2gm_lr.map_coords_2,')')))", $y2, $x2, $y2, $x1, $y1, $x1, $y1, $x2, $y2, $x2), ARRAY_A);

			$post_ids = array();
			foreach ($results AS $row)
				$post_ids[] = $row['post_id'];
			$post_ids = array_unique($post_ids);

			if ($post_ids) {
				if (isset($args['post__in'])) {
					$args['post__in'] = array_intersect($args['post__in'], $post_ids);
					if (empty($args['post__in']))
						// Do not show any listings
						$args['post__in'] = array(0);
				} else
					$args['post__in'] = $post_ids;
			} else
				// Do not show any listings
				$args['post__in'] = array(0);
		}

		$this->google_map = new w2gm_google_maps($this->args);
		$this->google_map->setUniqueId($this->hash);

		if (!$this->google_map->is_ajax_markers_management()) {
			$this->query = new WP_Query($args);

			//var_dump($this->query->request);
			$this->processQuery($this->args['ajax_markers_loading']);
		}

		apply_filters('w2gm_frontend_controller_construct', $this);
	}
	
	public function processQuery($is_ajax_map = false, $map_args = array()) {
		while ($this->query->have_posts()) {
			$this->query->the_post();

			$listing = new w2gm_listing;
			if (!$is_ajax_map) {
				$listing->loadListingForMap(get_post());
				$this->google_map->collectLocations($listing);
			} else {
				$listing->loadListingForAjaxMap(get_post());
				$this->google_map->collectLocationsForAjax($listing);
			}

			$this->listings[get_the_ID()] = $listing;
		}

		global $w2gm_address_locations, $w2gm_tax_terms_locations;
		// empty this global arrays - there may be some google maps on one page with different arguments
		$w2gm_address_locations = array();
		$w2gm_tax_terms_locations = array();

		// this is reset is really required after the loop ends
		wp_reset_postdata();
	}

	public function display() {
		ob_start();
		$this->google_map->display(
				false, // hide directions
				false, // static image
				$this->args['radius_circle'],
				$this->args['clusters'],
				$this->args['show_directions_button'],
				$this->args['show_readmore_button'],
				$this->args['width'],
				$this->args['height'],
				$this->args['sticky_scroll'],
				$this->args['sticky_scroll_toppadding'],
				$this->args['map_style'],
				$this->args['enable_full_screen'],
				$this->args['enable_wheel_zoom'],
				$this->args['enable_dragging_touchscreens'],
				$this->args['center_map_onclick'],
				$this->args['enable_listings_sidebar'],
				$this->args['listings_sidebar_position'],
				$this->args['listings_sidebar_width']
		);
		$output = ob_get_clean();
		
		wp_reset_postdata();

		return $output;
	}
}

?>