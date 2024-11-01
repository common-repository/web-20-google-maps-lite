<?php

class w2gm_admin {

	public function __construct() {
		global $w2gm_instance;

		add_action('admin_menu', array($this, 'menu'));

		$w2gm_instance->settings_manager = new w2gm_settings_manager;

		$w2gm_instance->listings_manager = new w2gm_listings_manager;

		$w2gm_instance->locations_manager = new w2gm_locations_manager;

		$w2gm_instance->locations_levels_manager = new w2gm_locations_levels_manager;

		$w2gm_instance->categories_manager = new w2gm_categories_manager;

		$w2gm_instance->content_fields_manager = new w2gm_content_fields_manager;

		$w2gm_instance->media_manager = new w2gm_media_manager;

		$w2gm_instance->csv_manager = new w2gm_csv_manager;

		// hide some meta-blocks when create/edit posts
		add_action('admin_init', array($this, 'hideMetaBlocks'));

		add_action('admin_head-post-new.php', array($this, 'hidePreviewButton'));
		add_action('admin_head-post.php', array($this, 'hidePreviewButton'));
		
		add_filter('post_row_actions', array($this, 'removeQuickEdit'), 10, 2);
		add_filter('quick_edit_show_taxonomy', array($this, 'removeQuickEditTax'), 10, 2);

		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'), 0);

		add_action('admin_notices', 'w2gm_renderMessages');

		add_action('wp_ajax_w2gm_generate_color_palette', array($this, 'generate_color_palette'));
		add_action('wp_ajax_nopriv_w2gm_generate_color_palette', array($this, 'generate_color_palette'));
		add_action('wp_ajax_w2gm_get_jqueryui_theme', array($this, 'get_jqueryui_theme'));
		add_action('wp_ajax_nopriv_w2gm_get_jqueryui_theme', array($this, 'get_jqueryui_theme'));
		add_action('vp_w2gm_option_before_ajax_save', array($this, 'remove_colorpicker_cookie'));
		add_action('wp_footer', array($this, 'render_colorpicker'));
		
		add_filter(W2GM_CATEGORIES_TAX . '_row_actions', array($this, 'removeQuickView'), 10, 2);
		add_filter(W2GM_LOCATIONS_TAX . '_row_actions', array($this, 'removeQuickView'), 10, 2);
		add_filter(W2GM_TAGS_TAX . '_row_actions', array($this, 'removeQuickView'), 10, 2);
	}

	public function menu() {
		add_menu_page(__("Maps Settings", "W2GM"),
			__('Maps Admin', 'W2GM'),
			'administrator',
			'w2gm_settings',
			null,
			W2GM_RESOURCES_URL . 'images/menuicon.png'
		);
		add_submenu_page(
			'w2gm_settings',
			__("Maps Settings", "w2gm"),
			__("Maps Settings", "w2gm"),
			'administrator',
			'w2gm_settings',
			null
		);
		
		add_submenu_page(
			null,
			__("Maps Debug", "W2GM"),
			__("Maps Debug", "W2GM"),
			'administrator',
			'w2gm_debug',
			array($this, 'debug')
		);
	}
	
	public function debug() {
		global $w2gm_instance, $wpdb;
		
		$w2gm_locationGeoname = new w2gm_locationGeoname();
		$geolocation_response = $w2gm_locationGeoname->geocodeRequest('1600 Amphitheatre Parkway Mountain View, CA 94043', 'test');

		$settings = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'w2gm_%'", ARRAY_A);

		w2gm_renderTemplate('debug.tpl.php', array(
			'rewrite_rules' => get_option('rewrite_rules'),
			'geolocation_response' => $geolocation_response,
			'settings' => $settings,
			'levels' => $w2gm_instance->levels,
			'content_fields' => $w2gm_instance->content_fields,
		));
	}
	
	public function hideMetaBlocks() {
		 global $post, $pagenow;

		if (($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == W2GM_POST_TYPE) || ($pagenow == 'post.php' && $post && $post->post_type == W2GM_POST_TYPE)) {
			$user_id = get_current_user_id();
			update_user_meta($user_id, 'metaboxhidden_' . W2GM_POST_TYPE, array('authordiv', 'trackbacksdiv', 'commentstatusdiv', 'postcustom'));
		}
	}
	
	public function hidePreviewButton() {
		global $post_type;
		if ($post_type == W2GM_POST_TYPE)
			echo '<style type="text/css">#preview-action {display: none;}</style>';
	}

	public function w2gm_index_page() {
		global $w2gm_instance;

		w2gm_renderTemplate('admin_index.tpl.php');
	}
	
	public function removeQuickEdit($actions, $post) {
		if ($post->post_type == W2GM_POST_TYPE) {
			unset($actions['inline hide-if-no-js']);
			unset($actions['view']);
		}
		return $actions;
	}
	
	public function removeQuickEditTax($show_in_quick_edit, $taxonomy_name) {
		if ($taxonomy_name == W2GM_CATEGORIES_TAX || $taxonomy_name == W2GM_LOCATIONS_TAX)
			$show_in_quick_edit = false;
	
		return $show_in_quick_edit;
	}
	
	public function admin_enqueue_scripts_styles() {
		add_action('admin_head', array($this, 'enqueue_global_vars'));

		wp_register_style('w2gm_bootstrap', W2GM_RESOURCES_URL . 'css/bootstrap.css');
		wp_register_style('w2gm_admin', W2GM_RESOURCES_URL . 'css/admin.css');
		wp_register_style('w2gm_font_awesome', W2GM_RESOURCES_URL . 'css/font-awesome.css');
		wp_register_script('w2gm_js_functions', W2GM_RESOURCES_URL . 'js/js_functions.js', array('jquery'), false, true);

		// this jQuery UI version 1.10.3 is for WP v3.7.1
		wp_register_style('w2gm-jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css');

		wp_register_script('w2gm_google_maps_edit', W2GM_RESOURCES_URL . 'js/google_maps_edit.js', array('jquery'));

		wp_register_script('w2gm_categories_edit_scripts', W2GM_RESOURCES_URL . 'js/categories_icons.js', array('jquery'));
		wp_register_script('w2gm_categories_scripts', W2GM_RESOURCES_URL . 'js/manage_categories.js', array('jquery'));

		wp_register_script('w2gm_media_scripts', W2GM_RESOURCES_URL . 'js/ajaxfileupload.js', array('jquery'));
		
		wp_enqueue_style('w2gm_bootstrap');
		wp_enqueue_style('w2gm_admin');
		wp_enqueue_style('w2gm_font_awesome');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style('w2gm-jquery-ui-style');
		wp_enqueue_script('w2gm_js_functions');

		wp_localize_script(
			'w2gm_js_functions',
			'w2gm_google_maps_callback',
			array(
					'callback' => 'w2gm_load_maps_api_backend'
			)
		);

		wp_enqueue_script('w2gm_google_maps_edit');
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
		echo 'var w2gm_js_objects = ' . json_encode(
				array(
						'ajaxurl' => $ajaxurl,
						'ajax_loader_url' => W2GM_RESOURCES_URL . 'images/ajax-loader.gif',
						'ajax_loader_text' => __('Loading...', 'W2GM'),
						'is_rtl' => is_rtl(),
				)
		) . ';
';

		global $w2gm_maps_styles;
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
						'default_geocoding_location' => get_option('w2gm_default_geocoding_location'),
						'locations_targeting_text' => __('Locations targeting...', 'W2GM'),
						'map_style_name' => get_option('w2gm_map_style'),
						'map_markers_array' => w2gm_get_fa_icons_names(),
						'map_styles' => $w2gm_maps_styles,
						'address_autocomplete_code' => get_option('w2gm_address_autocomplete_code'),
				)
		) . ';
';
		echo '</script>
';
	}

	public function generate_color_palette() {
		ob_start();
		include W2GM_PATH . '/classes/customization/dynamic_css.php';
		$dynamic_css = ob_get_contents();
		ob_get_clean();

		echo $dynamic_css;
		die();
	}

	public function get_jqueryui_theme() {
		global $w2gm_color_schemes;

		if (isset($_COOKIE['w2gm_compare_palettes']) && get_option('w2gm_compare_palettes')) {
			$scheme = $_COOKIE['w2gm_compare_palettes'];
			if ($scheme && isset($w2gm_color_schemes[$scheme]['w2gm_jquery_ui_schemas']))
				echo '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/' . $w2gm_color_schemes[$scheme]['w2gm_jquery_ui_schemas'] . '/jquery-ui.css';
		}
		die();
	}
	
	public function remove_colorpicker_cookie($opt) {
		if (isset($_COOKIE['w2gm_compare_palettes'])) {
			unset($_COOKIE['w2gm_compare_palettes']);
			setcookie('w2gm_compare_palettes', null, -1, '/');
		}
	}

	public function render_colorpicker() {
		global $w2gm_instance;

		if (!empty($w2gm_instance->frontend_controllers))
			if (get_option('w2gm_compare_palettes'))
				if (current_user_can('manage_options'))
					w2gm_renderTemplate('color_picker/color_picker_panel.tpl.php');
	}
	
	public function removeQuickView($actions, $tag) {
		unset($actions['view']);
		return $actions;
	}
}
?>