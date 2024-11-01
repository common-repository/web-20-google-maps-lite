<?php 

class w2gm_content_field_string extends w2gm_content_field {
	public $max_length = 255;
	public $regex;
	
	protected $can_be_searched = true;
	protected $is_configuration_page = true;
	
	public function isNotEmpty($listing) {
		if ($this->value)
			return true;
		else
			return false;
	}

	public function configure() {
		global $wpdb, $w2gm_instance;

		if (w2gm_getValue($_POST, 'submit') && wp_verify_nonce($_POST['w2gm_configure_content_fields_nonce'], W2GM_PATH)) {
			$validation = new w2gm_form_validation();
			$validation->set_rules('max_length', __('Max length', 'W2GM'), 'required|is_natural_no_zero');
			$validation->set_rules('regex', __('PHP RegEx template', 'W2GM'));
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2gm_content_fields, array('options' => serialize(array('max_length' => $result['max_length'], 'regex' => $result['regex']))), array('id' => $this->id), null, array('%d')))
					w2gm_addMessage(__('Field configuration was updated successfully!', 'W2GM'));
				
				$w2gm_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->max_length = $validation->result_array('max_length');
				$this->regex = $validation->result_array('regex');
				w2gm_addMessage($validation->error_string(), 'error');

				w2gm_renderTemplate('content_fields/fields/string_configuration.tpl.php', array('content_field' => $this));
			}
		} else
			w2gm_renderTemplate('content_fields/fields/string_configuration.tpl.php', array('content_field' => $this));
	}
	
	public function buildOptions() {
		if (isset($this->options['max_length']))
			$this->max_length = $this->options['max_length'];

		if (isset($this->options['regex']))
			$this->regex = $this->options['regex'];
		
	}
	
	public function renderInput() {
		w2gm_renderTemplate('content_fields/fields/string_input.tpl.php', array('content_field' => $this));
	}
	
	public function validateValues(&$errors, $data) {
		$field_index = 'w2gm-field-input-' . $this->id;
		
		if (isset($_POST[$field_index]) && $_POST[$field_index] && $this->regex)
			if (@!preg_match('/^' . $this->regex . '$/', $_POST[$field_index]))
				$errors[] = sprintf(__("Field %s doesn't match template!", 'W2GM'), $this->name);

		$validation = new w2gm_form_validation();
		$rules = 'max_length[' . $this->max_length . ']';
		if ($this->canBeRequired() && $this->is_required)
			$rules .= '|required';
		$validation->set_rules($field_index, $this->name, $rules);
		if (!$validation->run())
			$errors[] = $validation->error_string();

		return $validation->result_array($field_index);
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
		w2gm_renderTemplate('content_fields/fields/string_output.tpl.php', array('content_field' => $this, 'listing' => $listing));
	}

	public function validateCsvValues($value, &$errors) {
		if (!is_string($value))
			$errors[] = sprintf(__('Field %s must be a string!', 'W2GM'), $this->name);
		elseif ($this->regex && @!preg_match('/^' . $this->regex . '$/', $value))
			$errors[] = sprintf(__("Field %s doesn't match template!", 'W2GM'), $this->name);
		elseif (strlen($value) > $this->max_length)
			$errors[] = sprintf(__('The %s field can not exceed %s characters in length.', 'W2GM'), $this->name, $this->max_length);
		else
			return $value;
	}
	
	public function renderOutputForMap($location, $listing) {
		return $this->value;
	}
}
?>