<?php 

class w2gm_categories_manager {
	
	public function __construct() {
		global $pagenow;

		if ($pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'admin-ajax.php') {
			add_action('add_meta_boxes', array($this, 'removeCategoriesMetabox'));
			add_action('add_meta_boxes', array($this, 'addCategoriesMetabox'));
		}
		
		add_filter('manage_' . W2GM_CATEGORIES_TAX . '_custom_column', array($this, 'taxonomy_rows'), 15, 3);
		add_filter('manage_edit-' . W2GM_CATEGORIES_TAX . '_columns',  array($this, 'taxonomy_columns'));
		add_action(W2GM_CATEGORIES_TAX . '_edit_form_fields', array($this, 'select_marker_image_form'));
		add_action(W2GM_CATEGORIES_TAX . '_edit_form_fields', array($this, 'select_marker_icon_form'));
		add_action(W2GM_CATEGORIES_TAX . '_edit_form_fields', array($this, 'select_marker_color_form'));
		if (($pagenow == 'edit-tags.php' || $pagenow == 'term.php') && isset($_GET['taxonomy']) && $_GET['taxonomy'] == W2GM_CATEGORIES_TAX)
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_category_edit_scripts'));
		add_action('wp_ajax_w2gm_select_category_marker_png_image_dialog', array($this, 'select_category_marker_png_image_dialog'));
		add_action('wp_ajax_w2gm_select_category_marker_png_image', array($this, 'select_category_marker_png_image'));
		add_action('wp_ajax_w2gm_select_category_marker_icon', array($this, 'select_category_marker_icon'));
		add_action('wp_ajax_w2gm_select_category_marker_color', array($this, 'select_category_marker_color'));
		
		add_filter('manage_' . W2GM_TAGS_TAX . '_custom_column', array($this, 'tags_taxonomy_rows'), 15, 3);
		add_filter('manage_edit-' . W2GM_TAGS_TAX . '_columns',  array($this, 'tags_taxonomy_columns'));

		// 'checked_ontop' for maps categories taxonomy must always be false
		add_filter('wp_terms_checklist_args', array($this, 'unset_checked_ontop'), 100);
	}
	
	// remove native locations taxonomy metabox from sidebar
	public function removeCategoriesMetabox() {
		remove_meta_box(W2GM_CATEGORIES_TAX . 'div', W2GM_POST_TYPE, 'side');
	}

	public function addCategoriesMetabox($post_type) {
		if ($post_type == W2GM_POST_TYPE && (get_option('w2gm_categories_number') > 0 || get_option('w2gm_unlimited_categories'))) {
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'));

			add_meta_box(W2GM_CATEGORIES_TAX,
					__('Listing categories', 'W2GM'),
					'post_categories_meta_box',
					W2GM_POST_TYPE,
					'normal',
					'high',
					array('taxonomy' => W2GM_CATEGORIES_TAX));
		}
	}
	
	public function unset_checked_ontop($args) {
		if (isset($args['taxonomy']) && $args['taxonomy'] == W2GM_CATEGORIES_TAX)
			$args['checked_ontop'] = false;

		return $args;
	}

	public function validateCategories(&$postarr, &$errors) {
		global $w2gm_instance;

		if (isset($postarr['tax_input'][W2GM_CATEGORIES_TAX][0]) && $postarr['tax_input'][W2GM_CATEGORIES_TAX][0] == 0)
			unset($postarr['tax_input'][W2GM_CATEGORIES_TAX][0]);

		if (
			$w2gm_instance->content_fields->getContentFieldBySlug('categories_list')->is_required &&
			(
			!isset($postarr['tax_input'][W2GM_CATEGORIES_TAX]) ||
			!is_array($postarr['tax_input'][W2GM_CATEGORIES_TAX]) ||
			!count($postarr['tax_input'][W2GM_CATEGORIES_TAX])
			)
		)
			$errors[] = __('Select at least one category!', 'W2GM');

		if (isset($postarr['tax_input'][W2GM_CATEGORIES_TAX]) && is_array($postarr['tax_input'][W2GM_CATEGORIES_TAX])) {
			if (!get_option('w2gm_unlimited_categories'))
				// remove unauthorized categories
				$postarr['tax_input'][W2GM_CATEGORIES_TAX] = array_slice($postarr['tax_input'][W2GM_CATEGORIES_TAX], 0, get_option('w2gm_categories_number'), true);

			$post_categories_ids = $postarr['tax_input'][W2GM_CATEGORIES_TAX];
		} else
			$post_categories_ids = array();

		return $post_categories_ids;
	}

	public function validateTags(&$postarr, &$errors) {
		if (isset($postarr[W2GM_TAGS_TAX]) && $postarr[W2GM_TAGS_TAX]) {
			$post_tags_ids = array();
			foreach ($postarr[W2GM_TAGS_TAX] AS $tag) {
				if ($term = term_exists($tag, W2GM_TAGS_TAX)) {
					$post_tags_ids[] = intval($term['term_id']);
				} else {
					if ($newterm = wp_insert_term($tag, W2GM_TAGS_TAX))
						if (!is_wp_error($newterm))
							$post_tags_ids[] = intval($newterm['term_id']);
				}
			}
		} else
			$post_tags_ids = array();

		return $post_tags_ids;
	}
	
	public function tags_taxonomy_columns($original_columns) {
		$new_columns = $original_columns;
		array_splice($new_columns, 1);
		$new_columns['w2gm_tags_id'] = __('Tag ID', 'W2GM');
		return array_merge($new_columns, $original_columns);
	}
	
	public function tags_taxonomy_rows($row, $column_name, $term_id) {
		if ($column_name == 'w2gm_tags_id') {
			return $row . $term_id;
		}
		return $row;
	}
	
	public function taxonomy_columns($original_columns) {
		$new_columns = $original_columns;
		array_splice($new_columns, 1);
		$new_columns['w2gm_category_id'] = __('Category ID', 'W2GM');
		if (get_option('w2gm_map_markers_type') == 'icons') {
			$new_columns['w2gm_marker_category_icon'] = __('Marker Icon', 'W2GM');
			$new_columns['w2gm_marker_category_color'] = __('Marker Color', 'W2GM');
		} elseif (get_option('w2gm_map_markers_type') == 'images') {
			$new_columns['w2gm_marker_category_image'] = __('Marker Image', 'W2GM');
		}
		if (isset($original_columns['description']))
			unset($original_columns['description']);
		return array_merge($new_columns, $original_columns);
	}
	
	public function taxonomy_rows($row, $column_name, $term_id) {
		if ($column_name == 'w2gm_category_id') {
			return $row . $term_id;
		}
		if (get_option('w2gm_map_markers_type') == 'icons') {
			if ($column_name == 'w2gm_marker_category_icon') {
				return $row . $this->choose_marker_icon_link($term_id);
			}
			if ($column_name == 'w2gm_marker_category_color') {
				return $row . $this->choose_marker_icon_color($term_id);
			}
		} elseif (get_option('w2gm_map_markers_type') == 'images') {
			return $row . $this->choose_marker_image($term_id);
		}
		return $row;
	}
	
	// Category Map Marker Image
	public function select_marker_image_form($term) {
		if (get_option('w2gm_map_markers_type') == 'images') {
			w2gm_renderTemplate('categories/select_marker_image_form.tpl.php', array('term' => $term));
		}
	}
	public function choose_marker_image($term_id) {
		if (get_option('w2gm_map_markers_type') == 'images') {
			$image_png_name = $this->getCategoryMarkerImage($term_id);
			w2gm_renderTemplate('categories/select_marker_image_link.tpl.php', array('term_id' => $term_id, 'image_png_name' => $image_png_name));
		}
	}
	public function getCategoryMarkerImage($term_id) {
		if (($images = get_option('w2gm_categories_marker_images')) && is_array($images) && isset($images[$term_id]))
			return $images[$term_id];
	}
	public function select_category_marker_png_image() {
		if (isset($_POST['category_id']) && is_numeric($_POST['category_id']) && check_ajax_referer(W2GM_PATH, 'w2gm_marker_png_image_nonce')) {
			$category_id = $_POST['category_id'];
			$markers_images = get_option('w2gm_categories_marker_images');
			if (isset($_POST['image_name']) && $_POST['image_name']) {
				$image_name = sanitize_text_field($_POST['image_name']);
				$markers_images[$category_id] = $image_name;
				update_option('w2gm_categories_marker_images', $markers_images);
				echo $category_id;
			} else {
				if (isset($markers_images[$category_id]))
					unset($markers_images[$category_id]);
				update_option('w2gm_categories_marker_images', $markers_images);
			}
		}
		die();
	}
	public function select_category_marker_png_image_dialog() {
		$custom_map_images = array();
	
		$custom_map_images_themes = scandir(W2GM_MAP_ICONS_PATH . 'icons/');
		foreach ($custom_map_images_themes AS $dir) {
			if (is_dir(W2GM_MAP_ICONS_PATH . 'icons/' . $dir) && $dir != '.' && $dir != '..') {
				$custom_map_images_theme_files = scandir(W2GM_MAP_ICONS_PATH . 'icons/' . $dir);
				foreach ($custom_map_images_theme_files AS $file)
				if (is_file(W2GM_MAP_ICONS_PATH . 'icons/' . $dir . '/' . $file) && $file != '.' && $file != '..')
					$custom_map_images[$dir][] = $file;
			}
		}
	
		w2gm_renderTemplate('categories/select_marker_image_dialog.tpl.php', array('custom_map_images' => $custom_map_images));
		die();
	}

	// Category Map Marker Icon
	public function select_marker_icon_form($term) {
		if (get_option('w2gm_map_markers_type') == 'icons') {
			w2gm_renderTemplate('categories/select_marker_icon_form.tpl.php', array('term' => $term));
		}
	}
	public function choose_marker_icon_link($term_id) {
		if (get_option('w2gm_map_markers_type') == 'icons') {
			$icon_name = $this->getCategoryMarkerIcon($term_id);
			w2gm_renderTemplate('categories/select_marker_icon_link.tpl.php', array('term_id' => $term_id, 'icon_name' => $icon_name));
		}
	}
	public function getCategoryMarkerIcon($term_id) {
		if (($icons = get_option('w2gm_categories_marker_icons')) && is_array($icons) && isset($icons[$term_id]))
			return $icons[$term_id];
	}
	public function select_category_marker_icon() {
		if (isset($_POST['category_id']) && is_numeric($_POST['category_id']) && check_ajax_referer(W2GM_PATH, 'w2gm_marker_icon_image_nonce')) {
			$category_id = $_POST['category_id'];
			$markers_icons = get_option('w2gm_categories_marker_icons');
			if (isset($_POST['icon_name']) && $_POST['icon_name']) {
				$icon_name = $_POST['icon_name'];
				if (in_array($icon_name, w2gm_get_fa_icons_names())) {
					$markers_icons[$category_id] = $icon_name;
					update_option('w2gm_categories_marker_icons', $markers_icons);
					echo $category_id;
				}
			} else {
				if (isset($markers_icons[$category_id]))
					unset($markers_icons[$category_id]);
				update_option('w2gm_categories_marker_icons', $markers_icons);
			}
		}
		die();
	}

	// Category Map Marker Color
	public function select_marker_color_form($term) {
		w2gm_renderTemplate('categories/select_marker_color_form.tpl.php', array('term' => $term));
	}
	public function choose_marker_icon_color($term_id) {
		$color = $this->getCategoryMarkerColor($term_id);
		w2gm_renderTemplate('categories/select_marker_color_link.tpl.php', array('term_id' => $term_id, 'color' => $color));
	}
	public function getCategoryMarkerColor($term_id) {
		if (($colors = get_option('w2gm_categories_marker_colors')) && is_array($colors) && isset($colors[$term_id]))
			return $colors[$term_id];
	}
	public function select_category_marker_color() {
		if (isset($_POST['category_id']) && is_numeric($_POST['category_id']) && check_ajax_referer(W2GM_PATH, 'w2gm_marker_color_nonce')) {
			$category_id = $_POST['category_id'];
			$markers_colors = get_option('w2gm_categories_marker_colors');
			if (isset($_POST['color']) && $_POST['color']) {
				$color = sanitize_hex_color($_POST['color']);
				$markers_colors[$category_id] = $color;
				update_option('w2gm_categories_marker_colors', $markers_colors);
				echo $category_id;
			} else {
				if (isset($markers_colors[$category_id]))
					unset($markers_colors[$category_id]);
				update_option('w2gm_categories_marker_colors', $markers_colors);
			}
		}
		die();
	}
	
	public function admin_enqueue_category_edit_scripts() {
		wp_enqueue_script('w2gm_categories_edit_scripts');
		wp_localize_script(
				'w2gm_categories_edit_scripts',
				'categories_icons',
				array(
						'categories_markers_images_png_url' => W2GM_MAP_ICONS_URL . 'icons/',
						'ajax_dialog_title' => __('Select category icon', 'W2GM'),
						'ajax_marker_dialog_title' => __('Select marker', 'W2GM'),
				)
		);
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
	}
	
	public function admin_enqueue_scripts_styles() {
		wp_enqueue_script('w2gm_categories_scripts');

		if ($listing = w2gm_getCurrentListingInAdmin()) {
			if (get_option('w2gm_unlimited_categories'))
				$categories_number = 'unlimited';
			else 
				$categories_number = get_option('w2gm_categories_number');

			wp_localize_script(
					'w2gm_categories_scripts',
					'categories_options',
					array(
							'number' => $categories_number,
							'notice_number' => sprintf(__('Sorry, you can not choose more than %d categories!', 'W2GM'), $categories_number)
					)
			);
		}
	}
}

?>