<?php 

class w2gm_content_field_select extends w2gm_content_field {
	public $selection_items = array();

	protected $is_configuration_page = true;
	protected $is_search_configuration_page = true;
	protected $can_be_searched = true;
	
	public function isNotEmpty($listing) {
		if ($this->value)
			return true;
		else
			return false;
	}
	
	public function __construct() {
		// adapted for WPML
		add_action('init', array($this, 'content_fields_options_into_strings'));
	}

	public function configure() {
		global $wpdb, $w2gm_instance;

		wp_enqueue_script('jquery-ui-sortable');

		if (w2gm_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2gm_configure_content_fields_nonce'], W2GM_PATH)) {
			$validation = new w2gm_form_validation();
			$validation->set_rules('selection_items[]', __('Selection items', 'W2GM'), 'required');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2gm_content_fields, array('options' => serialize(array('selection_items' => $result['selection_items[]']))), array('id' => $this->id), null, array('%d')))
					w2gm_addMessage(__('Field configuration was updated successfully!', 'W2GM'));
				
				do_action('w2gm_update_selection_items', $result['selection_items[]'], $this);
				
				$w2gm_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->selection_items = $validation->result_array('selection_items[]');
				w2gm_addMessage($validation->error_string(), 'error');

				w2gm_renderTemplate('content_fields/fields/select_configuration.tpl.php', array('content_field' => $this));
			}
		} else
			w2gm_renderTemplate('content_fields/fields/select_configuration.tpl.php', array('content_field' => $this));
	}
	
	public function buildOptions() {
		if (isset($this->options['selection_items']))
			$this->selection_items = $this->options['selection_items'];
	}
	
	public function renderInput() {
		w2gm_renderTemplate('content_fields/fields/select_input.tpl.php', array('content_field' => $this));
	}
	
	public function validateValues(&$errors, $data) {
		$field_index = 'w2gm-field-input-' . $this->id;

		$validation = new w2gm_form_validation();
		$rules = '';
		if ($this->canBeRequired() && $this->is_required)
			$rules .= '|required';
		$validation->set_rules($field_index, $this->name, $rules);
		if (!$validation->run())
			$errors[] = $validation->error_string();
		elseif ($selected_item = $validation->result_array($field_index)) {
			if (!in_array($selected_item, array_keys($this->selection_items)))
				$errors[] = sprintf(__("This selection option index \"%d\" doesn't exist", 'W2GM'), $selected_item);

			return $selected_item;
		}
	}
	
	public function saveValue($post_id, $validation_results) {
		return update_post_meta($post_id, '_content_field_' . $this->id, $validation_results);
	}
	
	public function loadValue($post_id) {
		$this->value = get_post_meta($post_id, '_content_field_' . $this->id, true);
		
		$this->value = apply_filters('w2gm_content_field_load', $this->value, $this, $post_id);
		return $this->value;
	}
	
	public function renderOutput($listing = null) {
		w2gm_renderTemplate('content_fields/fields/select_radio_output.tpl.php', array('content_field' => $this, 'listing' => $listing));
	}

	public function validateCsvValues($value, &$errors) {
		if ($value)
			if (!in_array($value, $this->selection_items))
				$errors[] = sprintf(__("This selection option \"%s\" doesn't exist", 'W2GM'), $value);
			else
				return array_search($value, $this->selection_items);
		else 
			return '';
	}
	
	public function renderOutputForMap($location, $listing) {
		if ($this->value && isset($this->selection_items[$this->value]))
			return $this->selection_items[$this->value];
	}

	// adapted for WPML
	public function content_fields_options_into_strings() {
		global $sitepress;

		if (function_exists('wpml_object_id_filter') && $sitepress) {
			foreach ($this->selection_items AS $key=>&$item) {
				$item = apply_filters('wpml_translate_single_string', $item, 'Web 2.0 Google Maps', 'The option #' . $key . ' of content field #' . $this->id);
			}
		}
	}
}

add_action('w2gm_update_selection_items', 'w2gm_update_selection_items', 10, 2);
function w2gm_update_selection_items($selection_items, $content_field) {
	global $sitepress;

	if (function_exists('wpml_object_id_filter') && $sitepress) {
		foreach ($selection_items AS $key=>&$item) {
			do_action('wpml_register_single_string', 'Web 2.0 Google Maps',  'The option #' . $key . ' of content field #' . $content_field->id, $item);
		}
	}
}
?>