<?php
/*
Plugin Name: Web 2.0 Google Maps Lite plugin
Plugin URI: http://www.salephpscripts.com/wordpress_maps/
Description: Build powerful, searchable and responsive Google Maps with markers and insert them on pages in some seconds.
Version: 1.0.6
Author: salephpscripts.com
Author URI: http://www.salephpscripts.com
License: GPLv2 or any later version
*/

define('W2GM_LITE_VERSION', '1.0.6');

if (defined('W2GM_VERSION')) {
	deactivate_plugins(basename(__FILE__)); // Deactivate ourself
	wp_die(sprintf("Sorry, but lite version of Web 2.0 Google Maps plugin isn't compatible with its full version. Only one of them can work on the site. Deactivate Web 2.0 Google Maps plugin first. <a href='%s'>Back to plugin's page</a>", admin_url('plugins.php')));
}

define('W2GM_PATH', plugin_dir_path(__FILE__));
define('W2GM_URL', plugins_url('/', __FILE__));

define('W2GM_TEMPLATES_PATH', W2GM_PATH . 'templates/');

define('W2GM_RESOURCES_PATH', W2GM_PATH . 'resources/');
define('W2GM_RESOURCES_URL', W2GM_URL . 'resources/');

define('W2GM_MAP_ICONS_PATH', W2GM_RESOURCES_PATH . 'images/map_icons/');
define('W2GM_MAP_ICONS_URL', W2GM_RESOURCES_URL . 'images/map_icons/');

define('W2GM_POST_TYPE', 'w2gm_listing');
define('W2GM_CATEGORIES_TAX', 'w2gm-category');
define('W2GM_LOCATIONS_TAX', 'w2gm-location');
define('W2GM_TAGS_TAX', 'w2gm-tag');

include_once W2GM_PATH . 'install.php';
include_once W2GM_PATH . 'classes/admin.php';
include_once W2GM_PATH . 'classes/form_validation.php';
include_once W2GM_PATH . 'classes/listings/listings_manager.php';
include_once W2GM_PATH . 'classes/listings/listing.php';
include_once W2GM_PATH . 'classes/categories_manager.php';
include_once W2GM_PATH . 'classes/media_manager.php';
include_once W2GM_PATH . 'classes/content_fields/content_fields_manager.php';
include_once W2GM_PATH . 'classes/content_fields/content_fields.php';
include_once W2GM_PATH . 'classes/locations/locations_manager.php';
include_once W2GM_PATH . 'classes/locations/locations_levels_manager.php';
include_once W2GM_PATH . 'classes/locations/locations_levels.php';
include_once W2GM_PATH . 'classes/locations/location.php';
include_once W2GM_PATH . 'classes/frontend_controller.php';
include_once W2GM_PATH . 'classes/shortcodes/map_controller.php';
include_once W2GM_PATH . 'classes/ajax_controller.php';
include_once W2GM_PATH . 'classes/settings_manager.php';
include_once W2GM_PATH . 'classes/google_maps.php';
include_once W2GM_PATH . 'classes/csv_manager.php';
include_once W2GM_PATH . 'classes/location_geoname.php';
include_once W2GM_PATH . 'classes/search_fields/search_fields.php';
include_once W2GM_PATH . 'functions.php';
include_once W2GM_PATH . 'functions_ui.php';
include_once W2GM_PATH . 'maps_styles.php';
include_once W2GM_PATH . 'vc.php';
include_once W2GM_PATH . 'vafpress-framework/bootstrap.php';
include_once W2GM_PATH . 'classes/customization/color_schemes.php';

global $w2gm_instance;
global $w2gm_messages;

global $w2gm_shortcodes, $w2gm_shortcodes_init;
$w2gm_shortcodes = array(
		'webmap' => 'w2gm_map_controller',
);
$w2gm_shortcodes_init = array(
);

class w2gm_plugin {
	public $admin;
	public $listings_manager;
	public $locations_manager;
	public $locations_levels_manager;
	public $categories_manager;
	public $content_fields_manager;
	public $media_manager;
	public $settings_manager;
	public $csv_manager;

	public $current_listing; // this is object of listing under edition right now
	public $locations_levels;
	public $content_fields;
	public $search_fields;
	public $ajax_controller;
	public $frontend_controllers = array();
	public $_frontend_controllers = array(); // this duplicate property needed because we unset each controller when we render shortcodes, but WP doesn't really know which shortcode already was processed
	public $action;
	
	public $radius_values_array = array();

	public function __construct() {
		register_activation_hook(__FILE__, array($this, 'activation'));
		register_deactivation_hook(__FILE__, array($this, 'deactivation'));
	}
	
	public function activation() {
		global $wp_version;

		if (version_compare($wp_version, '3.6', '<')) {
			deactivate_plugins(basename(__FILE__)); // Deactivate ourself
			wp_die("Sorry, but you can't run this plugin on current WordPress version, it requires WordPress v3.6 or higher.");
		}
		if (defined('W2DC_VERSION') && version_compare(W2DC_VERSION, '1.11.4', '<')) {
			deactivate_plugins(basename(__FILE__)); // Deactivate ourself
			wp_die("Sorry, but Web 2.0 Google Maps plugin isn't compatible with current version of Web 2.0 Directory plugin, it requires Web 2.0 Directory v1.11.4 or higher.");
		}
		if (defined('W2GM_VERSION')) {
			deactivate_plugins(basename(__FILE__)); // Deactivate ourself
			wp_die("Sorry, but lite version of Web 2.0 Google Maps plugin isn't compatible with its full version. Only one of them can work on the site.");
		}
		flush_rewrite_rules();
		
		wp_schedule_event(current_time('timestamp'), 'hourly', 'scheduled_events');
	}

	public function deactivation() {
		flush_rewrite_rules();

		wp_clear_scheduled_hook('scheduled_events');
	}
	
	public function init() {
		global $w2gm_shortcodes, $wpdb;

		$_GET = stripslashes_deep($_GET);
		if (isset($_REQUEST['w2gm_action']))
			$this->action = $_REQUEST['w2gm_action'];

		add_action('plugins_loaded', array($this, 'load_textdomains'));

		if (!isset($wpdb->w2gm_content_fields))
			$wpdb->w2gm_content_fields = $wpdb->prefix . 'w2gm_content_fields';
		if (!isset($wpdb->w2gm_content_fields_groups))
			$wpdb->w2gm_content_fields_groups = $wpdb->prefix . 'w2gm_content_fields_groups';
		if (!isset($wpdb->w2gm_locations_levels))
			$wpdb->w2gm_locations_levels = $wpdb->prefix . 'w2gm_locations_levels';
		if (!isset($wpdb->w2gm_locations_relationships))
			$wpdb->w2gm_locations_relationships = $wpdb->prefix . 'w2gm_locations_relationships';

		add_action('scheduled_events', array($this, 'suspend_expired_listings'));

		foreach ($w2gm_shortcodes AS $shortcode=>$function)
			add_shortcode($shortcode, array($this, 'renderShortcode'));
		
		add_action('init', array($this, 'checkSettings'), 0);

		add_action('init', array($this, 'register_post_type'), 0);

		add_action('wp', array($this, 'loadFrontendControllers'), 1);
		
		if (!get_option('w2gm_installed_maps_lite') || get_option('w2gm_installed_maps_lite_version') != W2GM_LITE_VERSION) {
			if (get_option('w2gm_installed_maps_lite'))
				$this->loadClasses();

			add_action('init', 'w2gm_install_maps', 0);
		} else {
			$this->loadClasses();
		}

		add_filter('no_texturize_shortcodes', array($this, 'w2gm_no_texturize'));

		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
		add_action('wp_head', array($this, 'enqueue_dynamic_css'), 9999);
	}

	public function load_textdomains() {
		load_plugin_textdomain('W2GM', '', dirname(plugin_basename( __FILE__ )) . '/languages');
	}
	
	public function loadClasses() {
		$this->locations_levels = new w2gm_locations_levels;
		$this->content_fields = new w2gm_content_fields;
		$this->search_fields = new w2gm_search_fields;
		$this->ajax_controller = new w2gm_ajax_controller;
		$this->admin = new w2gm_admin();
	}

	public function w2gm_no_texturize($shortcodes) {
		global $w2gm_shortcodes;
		
		foreach ($w2gm_shortcodes AS $shortcode=>$function)
			$shortcodes[] = $shortcode;
		
		return $shortcodes;
	}

	public function renderShortcode() {
		global $w2gm_shortcodes;

		// remove content filters in order not to break the layout of page
		remove_filter('the_content', 'wpautop');
		remove_filter('the_content', 'wptexturize');
		remove_filter('the_content', 'shortcode_unautop');
		remove_filter('the_content', 'convert_chars');
		remove_filter('the_content', 'prepend_attachment');
		remove_filter('the_content', 'convert_smilies');

		$attrs = func_get_args();
		$shortcode = $attrs[2];

		$filters_where_not_to_display = array(
				'wp_head',
				'init',
				'wp',
		);
		
		if (isset($this->_frontend_controllers[$shortcode]) && !in_array(current_filter(), $filters_where_not_to_display)) {
			$shortcode_controllers = $this->_frontend_controllers[$shortcode];
			foreach ($shortcode_controllers AS $key=>&$controller) {
				unset($this->_frontend_controllers[$shortcode][$key]); // there are possible more than 1 same shortcodes on a page, so we have to unset which already was displayed
				if (method_exists($controller, 'display'))
					return $controller->display();
			}
		}

		if (isset($w2gm_shortcodes[$shortcode])) {
			$shortcode_class = $w2gm_shortcodes[$shortcode];
			if ($attrs[0] === '')
				$attrs[0] = array();
			$shortcode_instance = new $shortcode_class();
			$this->frontend_controllers[$shortcode][] = $shortcode_instance;
			$shortcode_instance->init($attrs[0], $shortcode);

			if (method_exists($shortcode_instance, 'display'))
				return $shortcode_instance->display();
		}
	}

	public function loadFrontendControllers() {
		global $post, $wp_query;

		if ($wp_query->posts) {
			$pattern = get_shortcode_regex();
			foreach ($wp_query->posts AS $archive_post) {
				if (isset($archive_post->post_content))
					$this->loadNestedFrontendController($pattern, $archive_post->post_content);
			}
		} elseif ($post && isset($post->post_content)) {
			$pattern = get_shortcode_regex();
			$this->loadNestedFrontendController($pattern, $post->post_content);
		}
	}

	// this may be recursive function to catch nested shortcodes
	public function loadNestedFrontendController($pattern, $content) {
		global $w2gm_shortcodes_init, $w2gm_shortcodes;
		
		if (preg_match_all('/'.$pattern.'/s', $content, $matches) && array_key_exists(2, $matches)) {
			foreach ($matches[2] AS $key=>$shortcode) {
				if ($shortcode != 'shortcodes') {
					if (isset($w2gm_shortcodes_init[$shortcode]) && class_exists($w2gm_shortcodes_init[$shortcode])) {
						$shortcode_class = $w2gm_shortcodes_init[$shortcode];
						if (!($attrs = shortcode_parse_atts($matches[3][$key])))
							$attrs = array();
						$shortcode_instance = new $shortcode_class();
						$this->frontend_controllers[$shortcode][] = $shortcode_instance;
						$this->_frontend_controllers[$shortcode][] = $shortcode_instance;
						$shortcode_instance->init($attrs, $shortcode);
					} elseif (isset($w2gm_shortcodes[$shortcode]) && class_exists($w2gm_shortcodes[$shortcode])) {
						$shortcode_class = $w2gm_shortcodes[$shortcode];
						$this->frontend_controllers[$shortcode][] = $shortcode_class;
					}
					if ($shortcode_content = $matches[5][$key])
						$this->loadNestedFrontendController($pattern, $shortcode_content);
				}
			}
		}
	}
	
	public function checkSettings() {
		if (!get_option('w2gm_google_api_key') && is_admin())
			w2gm_addMessage(sprintf(__("<b>Web 2.0 Google Maps plugin</b>: since 22 June 2016 Google requires mandatory Maps API keys for maps created on NEW websites/domains. Please, <a href=\"http://www.salephpscripts.com/wordpress_maps/demo/documentation/#google_maps_keys\" target=\"_blank\">follow instructions</a> and enter API keys on <a href=\"%s\">maps settings page</a>. Otherwise it may cause problems with Google Maps, Geocoding, addition/edition listings locations, autocomplete on addresses fields.", 'W2GM'), admin_url('admin.php?page=w2gm_settings#_advanced')));
	}

	public function register_post_type() {
		$args = array(
			'labels' => array(
				'name' => __('Maps listings', 'W2GM'),
				'singular_name' => __('Maps listing', 'W2GM'),
				'add_new' => __('Create new listing', 'W2GM'),
				'add_new_item' => __('Create new listing', 'W2GM'),
				'edit_item' => __('Edit listing', 'W2GM'),
				'new_item' => __('New listing', 'W2GM'),
				'view_item' => __('View listing', 'W2GM'),
				'search_items' => __('Search listings', 'W2GM'),
				'not_found' =>  __('No listings found', 'W2GM'),
				'not_found_in_trash' => __('No listings found in trash', 'W2GM')
			),
			'description' => __('Maps listings', 'W2GM'),
			'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
			'publicly_queriable' => true,  // you should be able to query it
			'show_ui' => true,  // you should be able to edit it in wp-admin
			'exclude_from_search' => true,  // you should exclude it from search results
			'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
			'has_archive' => false,  // it shouldn't have archive page
			'rewrite' => false,  // it shouldn't have rewrite rules
			'supports' => array('title', 'author'),
			'menu_icon' => W2GM_RESOURCES_URL . 'images/menuicon.png',
		);
		if (get_option('w2gm_enable_description'))
			$args['supports'][] = 'editor';
		if (get_option('w2gm_enable_summary'))
			$args['supports'][] = 'excerpt';
		register_post_type(W2GM_POST_TYPE, $args);
		
		register_taxonomy(W2GM_CATEGORIES_TAX, W2GM_POST_TYPE, array(
				'hierarchical' => true,
				'show_in_nav_menus' => false,
				'show_tagcloud' => false,
				'labels' => array(
					'name' =>  __('Maps categories', 'W2GM'),
					'menu_name' =>  __('Maps categories', 'W2GM'),
					'singular_name' => __('Category', 'W2GM'),
					'add_new_item' => __('Create category', 'W2GM'),
					'new_item_name' => __('New category', 'W2GM'),
					'edit_item' => __('Edit category', 'W2GM'),
					'view_item' => __('View category', 'W2GM'),
					'update_item' => __('Update category', 'W2GM'),
					'search_items' => __('Search categories', 'W2GM'),
				),
			)
		);
		register_taxonomy(W2GM_LOCATIONS_TAX, W2GM_POST_TYPE, array(
				'hierarchical' => true,
				'show_in_nav_menus' => false,
				'show_tagcloud' => false,
				'labels' => array(
					'name' =>  __('Maps locations', 'W2GM'),
					'menu_name' =>  __('Maps locations', 'W2GM'),
					'singular_name' => __('Location', 'W2GM'),
					'add_new_item' => __('Create location', 'W2GM'),
					'new_item_name' => __('New location', 'W2GM'),
					'edit_item' => __('Edit location', 'W2GM'),
					'view_item' => __('View location', 'W2GM'),
					'update_item' => __('Update location', 'W2GM'),
					'search_items' => __('Search locations', 'W2GM'),
					
				),
			)
		);
		register_taxonomy(W2GM_TAGS_TAX, W2GM_POST_TYPE, array(
				'hierarchical' => false,
				'show_in_nav_menus' => false,
				'show_tagcloud' => false,
				'labels' => array(
					'name' =>  __('Maps tags', 'W2GM'),
					'menu_name' =>  __('Maps tags', 'W2GM'),
					'singular_name' => __('Tag', 'W2GM'),
					'add_new_item' => __('Create tag', 'W2GM'),
					'new_item_name' => __('New tag', 'W2GM'),
					'edit_item' => __('Edit tag', 'W2GM'),
					'view_item' => __('View tag', 'W2GM'),
					'update_item' => __('Update tag', 'W2GM'),
					'search_items' => __('Search tags', 'W2GM'),
				),
			)
		);
	}

	public function suspend_expired_listings() {
		global $wpdb;

		$posts_ids = $wpdb->get_col($wpdb->prepare("
				SELECT
					wp_pm1.post_id
				FROM
					{$wpdb->postmeta} AS wp_pm1
				LEFT JOIN
					{$wpdb->postmeta} AS wp_pm2 ON wp_pm1.post_id=wp_pm2.post_id
				WHERE
					wp_pm1.meta_key = '_expiration_date' AND
					wp_pm1.meta_value < %d AND
					wp_pm2.meta_key = '_listing_status' AND
					(wp_pm2.meta_value = 'active' OR wp_pm2.meta_value = 'stopped')
			", current_time('timestamp')));
		$listings_ids = array();
		foreach ($posts_ids AS $post_id) {
			add_post_meta($post_id, '_expiration_notification_sent', true);
			update_post_meta($post_id, '_listing_status', 'expired');
			wp_update_post(array('ID' => $post_id, 'post_status' => 'draft')); // This needed in order terms counts were always actual

			// adapted for WPML
			global $sitepress;
			if (function_exists('wpml_object_id_filter') && $sitepress) {
				$trid = $sitepress->get_element_trid($post_id, 'post_' . W2GM_POST_TYPE);
				$listings_ids[] = $trid;
			} else
				$listings_ids[] = $post_id;
		}
		
		$listings_ids = array_unique($listings_ids);
		foreach ($listings_ids AS $listing_id) {
			if ($post = get_post($listing_id)) {
				if (get_option('w2gm_expiration_notification')) {
					$listing_owner = get_userdata($post->post_author);
			
					$headers[] = "From: " . get_option('blogname') . " <" . w2gm_get_admin_notification_email() . ">";
					$headers[] = "Reply-To: " . w2gm_get_admin_notification_email();
					$headers[] = "Content-Type: text/html";
				
					$subject = "[" . get_option('blogname') . "] " . __('Expiration notification', 'W2GM');
				
					$body = str_replace('[listing]', $post->post_title,
							str_replace('[link]', admin_url('options.php?page=w2gm_renew&listing_id=' . $post->ID)),
					get_option('w2gm_expiration_notification'));
					wp_mail($listing_owner->user_email, $subject, $body, $headers);
				}
			}
		}

		$posts_ids = $wpdb->get_col($wpdb->prepare("
				SELECT
					wp_pm1.post_id
				FROM
					{$wpdb->postmeta} AS wp_pm1
				LEFT JOIN
					{$wpdb->postmeta} AS wp_pm2 ON wp_pm1.post_id=wp_pm2.post_id
				WHERE
					wp_pm1.meta_key = '_expiration_date' AND
					wp_pm1.meta_value < %d AND
					wp_pm2.meta_key = '_listing_status' AND
					(wp_pm2.meta_value = 'active' OR wp_pm2.meta_value = 'stopped')
			", current_time('timestamp')+(get_option('w2gm_send_expiration_notification_days')*86400)));

		$listings_ids = array();

		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			foreach ($posts_ids AS $post_id) {
				$trid = $sitepress->get_element_trid($post_id, 'post_' . W2GM_POST_TYPE);
				$listings_ids[] = $trid;
			}
		} else
			$listings_ids = $posts_ids;

		$listings_ids = array_unique($listings_ids);
		foreach ($listings_ids AS $listing_id) {
			if (!get_post_meta($listing_id, '_preexpiration_notification_sent', true) && ($post = get_post($listing_id))) {
				if (get_option('w2gm_preexpiration_notification')) {
					$listing_owner = get_userdata($post->post_author);
	
					$headers[] = "From: " . get_option('blogname') . " <" . w2gm_get_admin_notification_email() . ">";
					$headers[] = "Reply-To: " . w2gm_get_admin_notification_email();
					$headers[] = "Content-Type: text/html";
	
					$subject = "[" . get_option('blogname') . "] " . __('Expiration notification', 'W2GM');
					
					$body = str_replace('[listing]', $post->post_title,
							str_replace('[days]',
					get_option('w2gm_send_expiration_notification_days'), get_option('w2gm_preexpiration_notification')));
					wp_mail($listing_owner->user_email, $subject, $body, $headers);
				}

				if ($listing = $this->listings_manager->loadListing($listing_id))
					apply_filters('w2gm_listing_renew', false, $listing);
			}
			
			add_post_meta($listing_id, '_preexpiration_notification_sent', true);
		}
	}

	/**
	 * Get property by shortcode name
	 * 
	 * @param string $shortcode
	 * @param string $property if property missed - return controller object
	 * @return mixed
	 */
	public function getShortcodeProperty($shortcode, $property = false) {
		if (!isset($this->frontend_controllers[$shortcode]) || !isset($this->frontend_controllers[$shortcode][0]))
			return false;

		if ($property && !isset($this->frontend_controllers[$shortcode][0]->$property))
			return false;

		if ($property)
			return $this->frontend_controllers[$shortcode][0]->$property;
		else 
			return $this->frontend_controllers[$shortcode][0];
	}
	
	public function getShortcodeByHash($hash) {
		if (!isset($this->frontend_controllers) || !is_array($this->frontend_controllers) || empty($this->frontend_controllers))
			return false;

		foreach ($this->frontend_controllers AS $shortcodes)
			foreach ($shortcodes AS $controller)
				if (is_object($controller) && $controller->hash == $hash)
					return $controller;
	}
	
	public function getListingsShortcodeByuID($uid) {
		foreach ($this->frontend_controllers AS $shortcodes)
			foreach ($shortcodes AS $controller)
				if (is_object($controller) && get_class($controller) == 'w2gm_listings_controller' && $controller->args['uid'] == $uid)
					return $controller;
	}

	public function enqueue_scripts_styles() {
		if ($this->frontend_controllers) {
			add_action('wp_head', array($this, 'enqueue_global_vars'));
			add_action('wp_print_scripts', array($this, 'dequeue_maps_googleapis'), 1000);

			wp_register_style('w2gm_bootstrap', W2GM_RESOURCES_URL . 'css/bootstrap.css');
			if (!(function_exists('is_rtl') && is_rtl()))
				wp_register_style('w2gm_frontend', W2GM_RESOURCES_URL . 'css/frontend.css');
			else
				wp_register_style('w2gm_frontend', W2GM_RESOURCES_URL . 'css/frontend-rtl.css');
			wp_register_style('w2gm_font_awesome', W2GM_RESOURCES_URL . 'css/font-awesome.css');
	
			if (is_file(W2GM_RESOURCES_PATH . 'css/frontend-custom.css'))
				wp_register_style('w2gm_frontend-custom', W2GM_RESOURCES_URL . 'css/frontend-custom.css');
	
			wp_register_script('w2gm_js_functions', W2GM_RESOURCES_URL . 'js/js_functions.js', array('jquery'), false, true);

			wp_register_script('w2gm_categories_scripts', W2GM_RESOURCES_URL . 'js/manage_categories.js', array('jquery'), false, true);

			wp_register_script('w2gm_media_scripts', W2GM_RESOURCES_URL . 'js/ajaxfileupload.js', array('jquery'), false, true);

			// this jQuery UI version 1.10.4
			if (get_option('w2gm_jquery_ui_schemas')) $ui_theme = w2gm_get_dynamic_option('w2gm_jquery_ui_schemas'); else $ui_theme = 'smoothness';
			wp_register_style('w2gm-jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/' . $ui_theme . '/jquery-ui.css');

			wp_register_script('w2gm_google_maps_edit', W2GM_RESOURCES_URL . 'js/google_maps_edit.js', array('jquery'), false, true);

			wp_enqueue_style('w2gm_bootstrap');
			wp_enqueue_style('w2gm_frontend');
			wp_enqueue_style('w2gm_font_awesome');
	
			// Include dynamic-css file only when we are not in palettes comparison mode
			if (!isset($_COOKIE['w2gm_compare_palettes']) || !get_option('w2gm_compare_palettes')) {
				// Include dynamically generated css file if this file exists
				$upload_dir = wp_upload_dir();
				$filename = trailingslashit(set_url_scheme($upload_dir['baseurl'])) . 'w2gm-plugin.css';
				$filename_dir = trailingslashit($upload_dir['basedir']) . 'w2gm-plugin.css';
				global $wp_filesystem;
				if (empty($wp_filesystem)) {
					require_once(ABSPATH .'/wp-admin/includes/file.php');
					WP_Filesystem();
				}
				if ($wp_filesystem && trim($wp_filesystem->get_contents($filename_dir))) // if css file creation success
					wp_enqueue_style('w2gm-dynamic-css', $filename);
			}
	
			wp_enqueue_style('w2gm_frontend-custom');

			wp_enqueue_script('jquery-ui-dialog');
			wp_enqueue_script('jquery-ui-draggable');
			if (!get_option('w2gm_notinclude_jqueryui_css'))
				wp_enqueue_style('w2gm-jquery-ui-style');

			wp_enqueue_script('w2gm_js_functions');
			
			wp_register_style('w2gm_listings_slider', W2GM_RESOURCES_URL . 'css/bxslider/jquery.bxslider.css');
			wp_enqueue_style('w2gm_listings_slider');

			wp_localize_script(
				'w2gm_js_functions',
				'w2gm_google_maps_callback',
				array(
						'callback' => 'w2gm_load_maps_api'
				)
			);
		}
	}
	
	public function dequeue_maps_googleapis() {
		if ((get_option('w2gm_google_api_key') && !(defined('W2GM_NOTINCLUDE_MAPS_API') && W2GM_NOTINCLUDE_MAPS_API)) && !(defined('W2GM_NOT_DEQUEUE_MAPS_API') && W2GM_NOT_DEQUEUE_MAPS_API)) {
			global $wp_scripts;
			foreach ($wp_scripts->registered AS $key=>$script) {
				if (strpos($script->src, 'maps.googleapis.com') !== false || strpos($script->src, 'maps.google.com/maps/api') !== false)
					unset($wp_scripts->registered[$key]);
			}
		}
	}
	
	public function enqueue_global_vars() {
		// adapted for WPML
		global $sitepress;
		if (function_exists('wpml_object_id_filter') && $sitepress) {
			$ajaxurl = admin_url('admin-ajax.php?lang=' .  $sitepress->get_current_language());
		} else
			$ajaxurl = admin_url('admin-ajax.php');

		echo '
<script>
';
		echo 'var w2gm_controller_args_array = {};
';
		echo 'var w2gm_map_markers_attrs_array = [];
';
		echo 'var w2gm_map_markers_attrs = (function(map_id, markers_array, enable_radius_circle, enable_clusters, show_directions_button, show_readmore_button, map_style_name, enable_full_screen, enable_wheel_zoom, enable_dragging_touchscreens, center_map_onclick, map_attrs) {
		this.map_id = map_id;
		this.markers_array = markers_array;
		this.enable_radius_circle = enable_radius_circle;
		this.enable_clusters = enable_clusters;
		this.show_directions_button = show_directions_button;
		this.show_readmore_button = show_readmore_button;
		this.map_style_name = map_style_name;
		this.enable_full_screen = enable_full_screen;
		this.enable_wheel_zoom = enable_wheel_zoom;
		this.enable_dragging_touchscreens = enable_dragging_touchscreens;
		this.center_map_onclick = center_map_onclick;
		this.map_attrs = map_attrs;
		});
';
		global $w2gm_maps_styles;
		echo 'var w2gm_js_objects = ' . json_encode(
				array(
						'ajaxurl' => $ajaxurl,
						'ajax_loader_url' => W2GM_RESOURCES_URL . 'images/ajax-loader.gif',
						'ajax_iloader_url' => W2GM_RESOURCES_URL . 'images/ajax-indicator.gif',
						'ajax_loader_text' => __('Loading...', 'W2GM'),
						'ajax_map_loader_url' => W2GM_RESOURCES_URL . 'images/ajax-map-loader.gif',
						'ajax_load' => (int)get_option('w2gm_ajax_load'),
						'ajax_initial_load' => (int)get_option('w2gm_ajax_initial_load'),
						'is_rtl' => is_rtl(),
						'lang' => (($sitepress && get_option('w2gm_map_language_from_wpml')) ? ICL_LANGUAGE_CODE : ''),
				)
		) . ';
';
			
		$map_content_fields = $this->content_fields->getMapContentFields();
		$map_content_fields_icons = array('w2gm-fa-info-circle');
		foreach ($map_content_fields AS $content_field)
			if (is_a($content_field, 'w2gm_content_field') && $content_field->icon_image)
				$map_content_fields_icons[] = $content_field->icon_image;
			else
				$map_content_fields_icons[] = '';
		echo 'var w2gm_google_maps_objects = ' . json_encode(
				array(
						'notinclude_maps_api' => ((defined('W2GM_NOTINCLUDE_MAPS_API') && W2GM_NOTINCLUDE_MAPS_API) ? 1 : 0),
						'google_api_key' => get_option('w2gm_google_api_key'),
						'map_markers_type' => get_option('w2gm_map_markers_type'),
						'default_marker_color' => get_option('w2gm_default_marker_color'),
						'default_marker_icon' => get_option('w2gm_default_marker_icon'),
						'global_map_icons_path' => W2GM_MAP_ICONS_URL,
						'marker_image_width' => (int)get_option('w2gm_map_marker_width'),
						'marker_image_height' => (int)get_option('w2gm_map_marker_height'),
						'marker_image_anchor_x' => (int)get_option('w2gm_map_marker_anchor_x'),
						'marker_image_anchor_y' => (int)get_option('w2gm_map_marker_anchor_y'),
						'infowindow_width' => (int)get_option('w2gm_map_infowindow_width'),
						'infowindow_offset' => -(int)get_option('w2gm_map_infowindow_offset'),
						'infowindow_logo_width' => (int)get_option('w2gm_map_infowindow_logo_width'),
						'w2gm_map_info_window_button_readmore' => __('Read more »', 'W2GM'),
						'w2gm_map_info_window_button_directions' => __('« Directions', 'W2GM'),
						'map_style_name' => get_option('w2gm_map_style'),
						'locations_targeting_text' => __('Locations targeting...', 'W2GM'),
						'w2gm_map_content_fields_icons' => $map_content_fields_icons,
						'map_markers_array' => w2gm_get_fa_icons_names(),
						'map_styles' => $w2gm_maps_styles,
						'address_autocomplete_code' => get_option('w2gm_address_autocomplete_code'),
				)
		) . ';
';
		echo '</script>
';
	}

	// Include dynamically generated css code if css file does not exist.
	public function enqueue_dynamic_css($load_scripts_styles = false) {
		if ($this->frontend_controllers || $load_scripts_styles) {
			$upload_dir = wp_upload_dir();
			$filename = trailingslashit(set_url_scheme($upload_dir['baseurl'])) . 'w2gm-plugin.css';
			$filename_dir = trailingslashit($upload_dir['basedir']) . 'w2gm-plugin.css';
			global $wp_filesystem;
			if (empty($wp_filesystem)) {
				require_once(ABSPATH .'/wp-admin/includes/file.php');
				WP_Filesystem();
			}
			if ((!$wp_filesystem || !trim($wp_filesystem->get_contents($filename_dir))) ||
				// When we are in palettes comparison mode - this will build css according to $_COOKIE['w2gm_compare_palettes']
				(isset($_COOKIE['w2gm_compare_palettes']) && get_option('w2gm_compare_palettes')))
			{
				ob_start();
				include W2GM_PATH . '/classes/customization/dynamic_css.php';
				$dynamic_css = ob_get_contents();
				ob_get_clean();
				echo '<style type="text/css">
	';
				echo $dynamic_css;
				echo '</style>';
			}
		}
	}
}

$w2gm_instance = new w2gm_plugin();
$w2gm_instance->init();

?>
