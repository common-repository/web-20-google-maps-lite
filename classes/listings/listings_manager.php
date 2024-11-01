<?php 

class w2gm_listings_manager {
	public $current_listing;
	
	public function __construct() {
		global $pagenow;

		add_action('add_meta_boxes', array($this, 'addListingInfoMetabox'));
		add_action('add_meta_boxes', array($this, 'addExpirationDateMetabox'));

		add_action('admin_init', array($this, 'loadCurrentListing'));

		add_action('admin_init', array($this, 'initHooks'));
		
		add_filter('manage_'.W2GM_POST_TYPE.'_posts_columns', array($this, 'add_listings_table_columns'));
		add_filter('manage_'.W2GM_POST_TYPE.'_posts_custom_column', array($this, 'manage_listings_table_rows'), 10, 2);
		
		add_action('restrict_manage_posts', array($this, 'posts_filter_dropdown'));
		add_filter('request', array( $this, 'posts_filter'));
		
		add_action('admin_menu', array($this, 'addRenewPage'));
		add_action('admin_menu', array($this, 'addChangeDatePage'));

		if ((isset($_POST['publish']) || isset($_POST['save'])) && (isset($_POST['post_type']) && $_POST['post_type'] == W2GM_POST_TYPE)) {
			add_filter('wp_insert_post_data', array($this, 'validateListing'), 99, 2);
			add_filter('redirect_post_location', array($this, 'redirectAfterSave'));
			add_action('save_post_' . W2GM_POST_TYPE, array($this, 'saveListing'), 10, 3);
		}

		// adapted for WPML
		add_action('icl_make_duplicate', array($this, 'handle_wpml_make_duplicate'), 10, 4);

		add_action('post_updated', array($this, 'avoid_redirection_plugin'), 10, 1);
	}
	
	public function addListingInfoMetabox($post_type) {
		if ($post_type == W2GM_POST_TYPE && ($listing = w2gm_getCurrentListingInAdmin()) && $listing->listing_created) {
			add_meta_box('w2gm_listing_info',
					__('Listing Info', 'W2GM'),
					array($this, 'listingInfoMetabox'),
					W2GM_POST_TYPE,
					'side',
					'high');
		}
	}

	public function addExpirationDateMetabox($post_type) {
		$listing = w2gm_getCurrentListingInAdmin();
		if ($post_type == W2GM_POST_TYPE && !get_option('w2gm_eternal_active_period') && (get_option('w2gm_active_period_days') || get_option('w2gm_active_period_months') || get_option('w2gm_active_period_years')) && (get_option('w2gm_change_expiration_date') || current_user_can('manage_options'))) {
			add_meta_box('w2gm_listing_expiration_date',
					__('Listing expiration date', 'W2GM'),
					array($this, 'listingExpirationDateMetabox'),
					W2GM_POST_TYPE,
					'normal',
					'high');
		}
	}

	public function listingInfoMetabox($post) {
		global $w2gm_instance;

		$listing = w2gm_getCurrentListingInAdmin();
		w2gm_renderTemplate('listings/info_metabox.tpl.php', array('listing' => $listing));
	}
	
	public function listingExpirationDateMetabox($post) {
		global $w2gm_instance;

		$listing = w2gm_getCurrentListingInAdmin();
		if ($listing->status != 'expired') {
			wp_enqueue_script('jquery-ui-datepicker');

			if ($i18n_file = w2gm_getDatePickerLangFile(get_locale())) {
				wp_register_script('datepicker-i18n', $i18n_file, array('jquery-ui-datepicker'));
				wp_enqueue_script('datepicker-i18n');
			}

			// If new listing
			if (!$listing->expiration_date)
				$listing->expiration_date = w2gm_sumDates(time(), get_option('w2gm_active_period_days'), get_option('w2gm_active_period_months'), get_option('w2gm_active_period_years'));

			w2gm_renderTemplate('listings/change_date_metabox.tpl.php', array('listing' => $listing, 'dateformat' => w2gm_getDatePickerFormat()));
		} else {
			_e('Renew listing first!', 'W2GM');
			$renew_link = strip_tags(apply_filters('w2gm_renew_option', __('renew listing', 'W2GM'), $listing));
			echo '<br /><a href="' . admin_url('options.php?page=w2gm_renew&listing_id=' . $listing->post->ID) . '"><img src="' . W2GM_RESOURCES_URL . 'images/page_refresh.png" class="w2gm-field-icon" />' . $renew_link . '</a>';
		}
	}

	public function add_listings_table_columns($columns) {
		$w2gm_columns['w2gm_expiration_date'] = __('Expiration date', 'W2GM');
		$w2gm_columns['w2gm_status'] = __('Status', 'W2GM');

		return array_slice($columns, 0, 2, true) + $w2gm_columns + array_slice($columns, 2, count($columns)-2, true);
	}
	
	public function manage_listings_table_rows($column, $post_id) {
		switch ($column) {
			case "w2gm_expiration_date":
				$listing = new w2gm_listing();
				$listing->loadListingFromPost($post_id);
				if (get_option('w2gm_eternal_active_period'))
					_e('Eternal active period', 'W2GM');
				else {
					if (!get_option('w2gm_eternal_active_period') && (get_option('w2gm_active_period_days') || get_option('w2gm_active_period_months') || get_option('w2gm_active_period_years')) && (current_user_can('manage_options')) || $listing->status == 'active')
						echo '<a href="' . admin_url('options.php?page=w2gm_changedate&listing_id=' . $post_id) . '" title="' . esc_attr__('change expiration date', 'W2GM') . '">' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->expiration_date)) . '</a>';
					else
						echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->expiration_date));

					if ($listing->status == 'expired' && get_option('w2gm_enable_renew')) {
						$renew_link = apply_filters('w2gm_renew_option', __('renew listing', 'W2GM'), $listing);
						echo '<br /><a href="' . admin_url('options.php?page=w2gm_renew&listing_id=' . $post_id) . '"><img src="' . W2GM_RESOURCES_URL . 'images/page_refresh.png" class="w2gm-field-icon" />' . $renew_link . '</a>';
					}
				}
				break;
			case "w2gm_status":
				$listing = new w2gm_listing();
				$listing->loadListingFromPost($post_id);
				if ($listing->status == 'active')
					echo '<span class="w2gm-badge w2gm-listing-status-active">' . __('active', 'W2GM') . '</span>';
				elseif ($listing->status == 'expired')
					echo '<span class="w2gm-badge w2gm-listing-status-expired">' . __('expired', 'W2GM') . '</span>';
				elseif ($listing->status == 'unpaid')
					echo '<span class="w2gm-badge w2gm-listing-status-unpaid">' . __('unpaid', 'W2GM') . '</span>';
				elseif ($listing->status == 'stopped')
					echo '<span class="w2gm-badge w2gm-listing-status-stopped">' . __('stopped', 'W2GM') . '</span>';
				do_action('w2gm_listing_status_option', $listing);
				break;
		}
	}

	public function posts_filter_dropdown() {
		global $pagenow, $w2gm_instance;
		if ($pagenow === 'upload.php' || (isset($_GET['post_type']) && $_GET['post_type'] != W2GM_POST_TYPE))
			return;

		echo '<select name="w2gm_status_filter">';
		echo '<option value="">' . __('Any listings status', 'W2GM') . '</option>';
		echo '<option ' . selected(w2gm_getValue($_GET, 'w2gm_status_filter'), 'active', false ) . 'value="active">' . __('Active', 'W2GM') . '</option>';
		echo '<option ' . selected(w2gm_getValue($_GET, 'w2gm_status_filter'), 'expired', false ) . 'value="expired">' . __('Expired', 'W2GM') . '</option>';
		echo '<option ' . selected(w2gm_getValue($_GET, 'w2gm_status_filter'), 'unpaid', false ) . 'value="unpaid">' . __('Unpaid', 'W2GM') . '</option>';
		echo '</select>';
	}
	
	public function posts_filter($vars) {
		if (isset($_GET['w2gm_status_filter']) && $_GET['w2gm_status_filter']) {
			$vars = array_merge(
				$vars,
				array(
						'meta_query' => array(
								'relation' => 'AND',
								array(
										'key'     => '_listing_status',
										'value'   => $_GET['w2gm_status_filter'],
								)
						)
				)
			);
		}
		return $vars;
	}

	public function addRenewPage() {
		if (get_option('w2gm_enable_renew') || current_user_can('manage_options'))
			add_submenu_page('options.php',
					__('Renew listing', 'W2GM'),
					__('Renew listing', 'W2GM'),
					'publish_posts',
					'w2gm_renew',
					array($this, 'renewListing')
			);
	}
	
	public function renewListing() {
		if (isset($_GET['listing_id']) && ($listing_id = $_GET['listing_id']) && is_numeric($listing_id) && w2gm_current_user_can_edit_listing($listing_id)) {
			if ($this->loadCurrentListing($listing_id)) {
				$action = 'show';
				$referer = wp_get_referer();
				if (isset($_GET['renew_action']) && $_GET['renew_action'] == 'renew') {
					if ($this->current_listing->processActivate())
						w2gm_addMessage(__('Listing was renewed successfully!', 'W2GM'));
					$action = $_GET['renew_action'];
					$referer = $_GET['referer'];
				}
				w2gm_renderTemplate('listings/renew.tpl.php', array('listing' => $this->current_listing, 'referer' => $referer, 'action' => $action));
			} else
				exit();
		} else
			exit();
	}
	
	public function addChangeDatePage() {
		if (!get_option('w2gm_eternal_active_period') && (get_option('w2gm_active_period_days') || get_option('w2gm_active_period_months') || get_option('w2gm_active_period_years')) && (get_option('w2gm_change_expiration_date') || current_user_can('manage_options')))
			add_submenu_page('options.php',
					__('Change expiration date', 'W2GM'),
					__('Change expiration date', 'W2GM'),
					'publish_posts',
					'w2gm_changedate',
					array($this, 'changeDateListingPage')
			);
	}
	
	public function changeDateListingPage() {
		if (isset($_GET['listing_id']) && ($listing_id = $_GET['listing_id']) && is_numeric($listing_id) && w2gm_current_user_can_edit_listing($listing_id)) {
			if ($this->loadCurrentListing($listing_id)) {
				$action = 'show';
				$referer = wp_get_referer();
				if (isset($_GET['changedate_action']) && $_GET['changedate_action'] == 'changedate') {
					$this->changeExpirationDate();
					$action = $_GET['changedate_action'];
					$referer = $_GET['referer'];
				}
				wp_enqueue_script('jquery-ui-datepicker');

				w2gm_renderTemplate('listings/change_date.tpl.php', array('listing' => $this->current_listing, 'referer' => $referer, 'action' => $action, 'dateformat' => w2gm_getDatePickerFormat()));
			} else
				exit();
		} else
			exit();
	}
	
	public function changeExpirationDate() {
		$w2gm_form_validation = new w2gm_form_validation();
		$w2gm_form_validation->set_rules('expiration_date_tmstmp', __('Expiration date', 'W2GM'), 'required|integer');
		$w2gm_form_validation->set_rules('expiration_date_hour', __('Expiration hour', 'W2GM'), 'required|integer');
		$w2gm_form_validation->set_rules('expiration_date_minute', __('Expiration minute', 'W2GM'), 'required|integer');

		if ($w2gm_form_validation->run()) {
			if ($this->current_listing->saveExpirationDate($w2gm_form_validation->result_array())) {
				w2gm_addMessage(__('Expiration date of listing was changed successfully!', 'W2GM'));
				$this->current_listing->loadListingFromPost($this->current_listing->post->ID);
			}
		} elseif ($error_string = $w2gm_form_validation->error_string())
			w2gm_addMessage($error_string, 'error');
	}

	public function loadCurrentListing($listing_id = null) {
		global $w2gm_instance, $pagenow;

		if ($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == W2GM_POST_TYPE) {
			// New post
			$this->current_listing = new w2gm_listing;
			$w2gm_instance->current_listing = $this->current_listing;

			add_action('save_post', array($this, 'saveInitialDraft'), 10);
		} elseif (
			$listing_id
			||
			($pagenow == 'post.php' && isset($_GET['post']) && ($post = get_post($_GET['post'])) && $post->post_type == W2GM_POST_TYPE)
			||
			($pagenow == 'post.php' && isset($_POST['post_ID']) && ($post = get_post($_POST['post_ID'])) && $post->post_type == W2GM_POST_TYPE)
		) {
			if ((!isset($post) || !$post) && $listing_id)
				$post = get_post($listing_id);

			// Existed post
			$this->loadListing($post);
		}
		return $this->current_listing;
	}
	
	public function loadListing($listing_post) {
		global $w2gm_instance;

		$listing = new w2gm_listing();
		$listing->loadListingFromPost($listing_post);
		$this->current_listing = $listing;
		$w2gm_instance->current_listing = $listing;
		
		return $listing;
	}
	
	public function saveInitialDraft($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;
	
		global $w2gm_instance;
		$this->current_listing->loadListingFromPost($post_id);
		$w2gm_instance->current_listing = $this->current_listing;
	}

	public function validateListing($data, $postarr) {
		// this condition in order to avoid mismatch of post type for invoice - when new listing created,
		// then it redirects to create new invoice and here it calls this function because earlier we check post type by $_POST['post_type']
		if ($data['post_type'] == W2GM_POST_TYPE) {
			global $w2gm_instance;
	
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;
	
			$errors = array();
			
			if (!isset($postarr['post_title']) || !$postarr['post_title'] || $postarr['post_title'] == __('Auto Draft'))
				$errors[] = __('Listing title field required', 'W2GM');

			$post_categories_ids = array();
			if (get_option('w2gm_categories_number') > 0 || get_option('w2gm_unlimited_categories')) {
				$post_categories_ids = $w2gm_instance->categories_manager->validateCategories($postarr, $errors);
			}

			$w2gm_instance->content_fields->saveValues($this->current_listing->post->ID, $post_categories_ids, $errors, $data);

			if (get_option('w2gm_locations_number') > 0) {
				if ($validation_results = $w2gm_instance->locations_manager->validateLocations($errors)) {
					$w2gm_instance->locations_manager->saveLocations($this->current_listing->post->ID, $validation_results);
				}
			}
	
			if (get_option('w2gm_listing_logo_enabled')) {
				if ($validation_results = $w2gm_instance->media_manager->validateAttachments($errors))
					$w2gm_instance->media_manager->saveAttachments($this->current_listing->post->ID, $validation_results);
			}

			// only successfully validated listings can be completed
			if ($errors) {
				//$data['post_status'] = 'draft';
	
				foreach ($errors AS $error)
					w2gm_addMessage($error, 'error');
			}
		}
		return $data;
	}

	public function redirectAfterSave($location) {
		global $post;

		if ($post) {
			if (is_numeric($post))
				$post = get_post($post);
			if ($post->post_type == W2GM_POST_TYPE) {
				// Remove native success 'message'
				$uri = parse_url($location);
				$uri_array = wp_parse_args($uri['query']);
				if (isset($uri_array['message']))
					unset($uri_array['message']);
				$location = add_query_arg($uri_array, 'post.php');
			}
		}

		return $location;
	}
	
	public function saveListing($post_ID, $post, $update) {
		// only successfully validated listings can be completed
		if ($post->post_status == 'publish') {
			$this->loadCurrentListing($post_ID);
			if (!($listing_created = get_post_meta($this->current_listing->post->ID, '_listing_created', true))) {
				add_post_meta($this->current_listing->post->ID, '_listing_created', true);
				add_post_meta($this->current_listing->post->ID, '_listing_status', 'active');
	
				if (!get_option('w2gm_eternal_active_period')) {
					if (get_option('w2gm_change_expiration_date') || current_user_can('manage_options'))
						$this->changeExpirationDate();
					else {
						$expiration_date = w2gm_sumDates(time(), get_option('w2gm_active_period_days'), get_option('w2gm_active_period_months'), get_option('w2gm_active_period_years'));
						add_post_meta($this->current_listing->post->ID, '_expiration_date', $expiration_date);
					}
				}

				do_action('w2gm_listing_creation', $this->current_listing);
			} else {
				if (!get_option('w2gm_eternal_active_period') && (get_option('w2gm_change_expiration_date') || current_user_can('manage_options')))
					$this->changeExpirationDate();
					
				/* if ($this->current_listing->status != 'expired')
					update_post_meta($this->current_listing->post->ID, '_listing_status', 'active'); */
				elseif ($this->current_listing->status == 'expired') {
					w2gm_addMessage(esc_attr__("You can't publish listing until it has expired status! Renew listing first!", 'W2GM'), 'error');
				}
				
				do_action('w2gm_listing_update', $this->current_listing);
			}
		}
	}
	
	public function initHooks() {
		if (current_user_can('delete_posts'))
			add_action('delete_post', array($this, 'delete_listing_data'), 10);
	}
	
	public function delete_listing_data($post_id) {
		global $w2gm_instance, $wpdb;

		$w2gm_instance->locations_manager->deleteLocations($post_id);
		
		$ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_parent = $post_id AND post_type = 'attachment'");
		foreach ($ids as $id)
			wp_delete_attachment($id);
	}

	// adapted for WPML
	public function handle_wpml_make_duplicate($master_post_id, $lang, $post_array, $id) {
		global $wpdb;

		$listing = new w2gm_listing();
		if (get_post_type($master_post_id) == W2GM_POST_TYPE && $listing->loadListingFromPost($master_post_id)) {
			$wpdb->delete($wpdb->w2gm_locations_relationships, array('post_id' => $id));
			wp_delete_object_term_relationships($id, W2GM_LOCATIONS_TAX);
			foreach ($listing->locations AS $location) {
				$insert_values = array(
						'post_id' => $id,
						'location_id' => apply_filters('wpml_object_id', $location->selected_location, W2GM_LOCATIONS_TAX, true, $lang),
						'address_line_1' => $location->address_line_1,
						'address_line_2' => $location->address_line_2,
						'zip_or_postal_index' => $location->zip_or_postal_index,
						'additional_info' => $location->additional_info,
				);
				$insert_values['manual_coords'] = $location->manual_coords;
				$insert_values['map_coords_1'] = $location->map_coords_1;
				$insert_values['map_coords_2'] = $location->map_coords_2;
				$insert_values['map_icon_file'] = $location->map_icon_file;
				$keys = array_keys($insert_values);
				array_walk($keys, create_function('&$val', '$val = "`".$val."`";'));
				array_walk($insert_values, create_function('&$val', '$val = "\'".$val."\'";'));
				
				$wpdb->query("INSERT INTO {$wpdb->w2gm_locations_relationships} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $insert_values) . ")");
			}
		}
	}

	/* There is annoying problem from one redirection plugin */
	public function avoid_redirection_plugin($post_id) {
		if (get_post_type($post_id) == W2GM_POST_TYPE && isset($_POST['redirection_slug']))
			unset($_POST['redirection_slug']);
	}
}

?>