<?php 

class w2gm_ajax_controller {
	public $listing_id;

	public function __construct() {
		add_action('wp_ajax_w2gm_get_map_markers', array($this, 'get_map_markers'));
		add_action('wp_ajax_nopriv_w2gm_get_map_markers', array($this, 'get_map_markers'));

		add_action('wp_ajax_w2gm_get_map_marker_info', array($this, 'get_map_marker_info'));
		add_action('wp_ajax_nopriv_w2gm_get_map_marker_info', array($this, 'get_map_marker_info'));

		add_action('wp_ajax_w2gm_controller_request', array($this, 'controller_request'));
		add_action('wp_ajax_nopriv_w2gm_controller_request', array($this, 'controller_request'));

		add_action('wp_ajax_w2gm_select_field_icon', array($this, 'select_field_icon'));
		add_action('wp_ajax_nopriv_w2gm_select_field_icon', array($this, 'select_field_icon'));

		add_action('wp_ajax_w2gm_listing_dialog', array($this, 'listing_dialog'));
		add_action('wp_ajax_nopriv_w2gm_listing_dialog', array($this, 'listing_dialog'));
	}

	public function controller_request() {
		global $w2gm_instance;

		$post_args = $_POST;

		$map_controller = new w2gm_map_controller();
		$map_controller->hash = sanitize_text_field($post_args['hash']);
		$map_controller->init($post_args);
		wp_reset_postdata();
			
		$map_markers = $map_controller->google_map->locations_option_array;

		$out = array(
				'hash' => $map_controller->hash,
				'map_markers' => $map_markers
		);
		if (isset($post_args['with_listings']) || $post_args['with_listings']) {
			$out['listings'] = $map_controller->google_map->buildListingsContent($map_controller->args['show_directions_button'], $map_controller->args['show_readmore_button']);
		}
		if (isset($w2gm_instance->radius_values_array[$map_controller->hash]) && isset($w2gm_instance->radius_values_array[$map_controller->hash]['x_coord']) && isset($w2gm_instance->radius_values_array[$map_controller->hash]['y_coord'])) {
			$out['radius_params'] = array(
					'radius_value' => $w2gm_instance->radius_values_array[$map_controller->hash]['radius'],
					'map_coords_1' => $w2gm_instance->radius_values_array[$map_controller->hash]['x_coord'],
					'map_coords_2' => $w2gm_instance->radius_values_array[$map_controller->hash]['y_coord'],
					'dimension' => get_option('w2gm_miles_kilometers_in_search')
			);
		}
		echo json_encode($out);
		
		die();
	}

	public function get_map_markers() {
		global $w2gm_instance;

		$post_args = $_POST;
		$hash = sanitize_text_field($post_args['hash']);

		$map_markers = array();
		if (isset($post_args['neLat']) && isset($post_args['neLng']) && isset($post_args['swLat']) && isset($post_args['swLng'])) {
			// needed to unset 'ajax_loading' parameter when it is calling by AJAX, then $args will be passed to map controller
			$post_args['ajax_loading'] = 0;

			$map_controller = new w2gm_map_controller();
			$map_controller->hash = $hash;
			$map_controller->init($post_args);
			wp_reset_postdata();
			
			$map_markers = $map_controller->google_map->locations_option_array;
		}

		$out = array(
				'hash' => $hash,
				'map_markers' => $map_markers,
		);
		if (isset($post_args['with_listings']) || $post_args['with_listings']) {
			$out['listings'] = $map_controller->google_map->buildListingsContent($map_controller->args['show_directions_button'], $map_controller->args['show_readmore_button']);
		}
		if (isset($w2gm_instance->radius_values_array[$hash]) && isset($w2gm_instance->radius_values_array[$hash]['x_coord']) && isset($w2gm_instance->radius_values_array[$hash]['y_coord'])) {
			$out['radius_params'] = array(
					'radius_value' => $w2gm_instance->radius_values_array[$hash]['radius'],
					'map_coords_1' => $w2gm_instance->radius_values_array[$hash]['x_coord'],
					'map_coords_2' => $w2gm_instance->radius_values_array[$hash]['y_coord'],
					'dimension' => get_option('w2gm_miles_kilometers_in_search')
			);
		}
			
		echo json_encode($out);

		die();
	}
	
	public function get_map_marker_info() {
		global $w2gm_instance, $wpdb;

		if (isset($_POST['location_id']) && is_numeric($_POST['location_id'])) {
			$location_id = $_POST['location_id'];

			$row = $wpdb->get_row("SELECT * FROM {$wpdb->w2gm_locations_relationships} WHERE id=".$location_id, ARRAY_A);

			if ($row && $row['location_id'] || $row['map_coords_1'] != '0.000000' || $row['map_coords_2'] != '0.000000' || $row['address_line_1'] || $row['zip_or_postal_index']) {
				$listing = new w2gm_listing;
				if ($listing->loadListingFromPost($row['post_id'])) {
					$location = new w2gm_location($row['post_id']);
					
					// location_id into selected_location
					$row['selected_location'] = w2gm_getValue($row, 'location_id');

					$location->createLocationFromArray($row);
						
					$logo_image = '';
					if ($listing->logo_image) {
						$src = wp_get_attachment_image_src($listing->logo_image, array(80, 80));
						$logo_image = $src[0];
					}

					$content_fields_output = $listing->setMapContentFields($w2gm_instance->content_fields->getMapContentFields(), $location);

					$locations_option_array = array(
							$location->id,
							$location->map_coords_1,
							$location->map_coords_2,
							$location->map_icon_file,
							$location->map_icon_color,
							$listing->map_zoom,
							esc_js($listing->title()),
							$logo_image,
							$content_fields_output,
					);
						
					echo json_encode($locations_option_array);
				}
			}
		}
		die();
	}
	
	public function select_field_icon() {
		w2gm_renderTemplate('select_fa_icons.tpl.php', array('icons' => w2gm_get_fa_icons_names()));
		die();
	}
	
	public function listing_dialog() {
		global $w2gm_instance, $wpdb;

		if (isset($_REQUEST['location_id']) && is_numeric($_REQUEST['location_id'])) {
			$location_id = $_REQUEST['location_id'];
		
			if ($row = $wpdb->get_row("SELECT * FROM {$wpdb->w2gm_locations_relationships} WHERE id=".$location_id, ARRAY_A)) {
				$controller = new w2gm_frontend_controller;
				$controller->init(array('uid' => time()));
				$args = array(
						'post_type' => W2GM_POST_TYPE,
						'post_status' => 'publish',
						'p' => $row['post_id'],
						'posts_per_page' => 1,
				);
				$controller->query = new WP_Query($args);
				$controller->processQuery(true);

				if (count($controller->listings) == 1) {
					$listings_array = $controller->listings;
					$listing = array_shift($listings_array);
					$controller->listing = $listing;
					
					$this->listing_id = $listing->post->ID;
				
					$controller->listing->increaseClicksStats();
					
					$out = array(
							'listing_html' => w2gm_renderTemplate('frontend/listing_single.tpl.php', array('frontend_controller' => $controller), true),
							'hash' => $controller->hash
					);
					
					echo json_encode($out);
				}
			}
		}
		
		die();
	}
}
?>