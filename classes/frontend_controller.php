<?php 

class w2gm_frontend_controller {
	public $args = array();
	public $query;
	public $page_title;
	public $template;
	public $listings = array();
	public $search_form;
	public $google_map;
	public $paginator;
	public $breadcrumbs = array();
	public $base_url;
	public $messages = array();
	public $hash = null;
	public $do_initial_load = true;
	public $request_by = 'frontend_controller';

	public function __construct($args = array()) {
		apply_filters('w2gm_frontend_controller_construct', $this);
	}
	
	public function init($attrs = array()) {
		$this->args['logo_animation_effect'] = get_option('w2gm_logo_animation_effect');

		if (!$this->hash)
			if (isset($attrs['uid']) && $attrs['uid'])
				$this->hash = md5($attrs['uid']);
			else
				$this->hash = md5(get_class($this).serialize($attrs));
	}

	public function processQuery($load_map = true, $map_args = array()) {
		if ($load_map) {
			$this->google_map = new w2gm_google_maps($map_args);
			$this->google_map->setUniqueId($this->hash);
		}

		while ($this->query->have_posts()) {
			$this->query->the_post();

			$listing = new w2gm_listing;
			$listing->loadListingFromPost(get_post());
			$listing->logo_animation_effect = (isset($this->args['logo_animation_effect'])) ? $this->args['logo_animation_effect'] : get_option('w2gm_logo_animation_effect');

			if ($load_map && !$this->google_map->is_ajax_markers_management())
				$this->google_map->collectLocations($listing);
			
			$this->listings[get_the_ID()] = $listing;
		}
		
		global $w2gm_address_locations, $w2gm_tax_terms_locations;
		// empty this global arrays - there may be some google maps on one page with different arguments
		$w2gm_address_locations = array();
		$w2gm_tax_terms_locations = array();

		// this is reset is really required after the loop ends 
		wp_reset_postdata();
	}
	
	public function getQuery() {
		return $this->query;
	}
	
	public function getPageTitle() {
		return $this->page_title;
	}

	public function display() {
		$output =  w2gm_renderTemplate($this->template, array('frontend_controller' => $this), true);
		wp_reset_postdata();
	
		return $output;
	}
}

function w2gm_what_search($args, $defaults = array(), $include_GET_params = true) {
	if ($include_GET_params)
		$args['s'] = w2gm_getValue($_REQUEST, 'what_search', w2gm_getValue($defaults, 'what_search'));
	else
		$args['s'] =  w2gm_getValue($defaults, 'what_search');
	
	// 's' parameter must be removed when it is empty, otherwise it may case WP_query->is_search = true
	if (empty($args['s']))
		unset($args['s']);

	return $args;
}
add_filter('w2gm_search_args', 'w2gm_what_search', 10, 3);

function w2gm_address($args, $defaults = array(), $include_GET_params = true) {
	global $wpdb, $w2gm_address_locations;

	if ($include_GET_params) {
		$address = w2gm_getValue($_REQUEST, 'address', w2gm_getValue($defaults, 'address'));
		$search_location = w2gm_getValue($_REQUEST, 'location_id', w2gm_getValue($defaults, 'location_id'));
	} else {
		$search_location = w2gm_getValue($defaults, 'location_id');
		$address = w2gm_getValue($defaults, 'address');
	}
	
	$where_sql_array = array();
	if ($search_location && is_numeric($search_location)) {
		$term_ids = get_terms(W2GM_LOCATIONS_TAX, array('child_of' => $search_location, 'fields' => 'ids', 'hide_empty' => false));
		$term_ids[] = $search_location;
		$where_sql_array[] = "(location_id IN (" . implode(', ', $term_ids) . "))";
	}
	
	if ($address)
		$where_sql_array[] = $wpdb->prepare("(address_line_1 LIKE '%%%s%%' OR address_line_2 LIKE '%%%s%%' OR zip_or_postal_index LIKE '%%%s%%')", $address, $address, $address);

	if ($where_sql_array) {
		$results = $wpdb->get_results("SELECT id, post_id FROM {$wpdb->w2gm_locations_relationships} WHERE " . implode(' AND ', $where_sql_array), ARRAY_A);
		$post_ids = array();
		foreach ($results AS $row) {
			$post_ids[] = $row['post_id'];
			$w2gm_address_locations[] = $row['id'];
		}
		if ($post_ids)
			$args['post__in'] = $post_ids;
		else
			// Do not show any listings
			$args['post__in'] = array(0);	
	}
	return $args;
}
add_filter('w2gm_search_args', 'w2gm_address', 10, 3);

function w2gm_related_shortcode_args($shortcode_atts) {
	if (isset($shortcode_atts['author']) && $shortcode_atts['author'] === 'related') {
		if ($user_id = get_the_author_meta('ID')) {
			$shortcode_atts['author'] = $user_id;
		}
	}

	return $shortcode_atts;
}
add_filter('w2gm_related_shortcode_args', 'w2gm_related_shortcode_args');

?>