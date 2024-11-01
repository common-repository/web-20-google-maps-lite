<?php 

class w2gm_content_field_number extends w2gm_content_field {
	public $is_integer = false;
	public $decimal_separator = ',';
	public $thousands_separator = ' ';
	public $min = 0;
	public $max;

	protected $is_configuration_page = true;
	protected $is_search_configuration_page = true;
	protected $can_be_searched = true;
	
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
			$validation->set_rules('is_integer', __('Is integer or decimal', 'W2GM'), 'required|natural');
			$validation->set_rules('decimal_separator', __('Decimal separator',  'W2GM'), 'required|max_length[1]');
			$validation->set_rules('thousands_separator', __('Thousands separator', 'W2GM'), 'max_length[1]');
			$validation->set_rules('min', __('Min', 'W2GM'), 'numeric');
			$validation->set_rules('max', __('Max', 'W2GM'), 'numeric');
			if ($validation->run()) {
				$result = $validation->result_array();
				if ($wpdb->update($wpdb->w2gm_content_fields, array('options' => serialize(array('is_integer' => $result['is_integer'], 'decimal_separator' => $result['decimal_separator'], 'thousands_separator' => $result['thousands_separator'], 'min' => $result['min'], 'max' => $result['max']))), array('id' => $this->id), null, array('%d')))
					w2gm_addMessage(__('Field configuration was updated successfully!', 'W2GM'));
				
				$w2gm_instance->content_fields_manager->showContentFieldsTable();
			} else {
				$this->is_integer = $validation->result_array('is_integer');
				$this->decimal_separator = $validation->result_array('decimal_separator');
				$this->thousands_separator = $validation->result_array('thousands_separator');
				$this->min = $validation->result_array('min');
				$this->max = $validation->result_array('max');
				w2gm_addMessage($validation->error_string(), 'error');

				w2gm_renderTemplate('content_fields/fields/number_configuration.tpl.php', array('content_field' => $this));
			}
		} else
			w2gm_renderTemplate('content_fields/fields/number_configuration.tpl.php', array('content_field' => $this));
	}
	
	public function buildOptions() {
		if (isset($this->options['is_integer']))
			$this->is_integer = $this->options['is_integer'];
		if (isset($this->options['decimal_separator']))
			$this->decimal_separator = $this->options['decimal_separator'];
		if (isset($this->options['thousands_separator']))
			$this->thousands_separator = $this->options['thousands_separator'];
		if (isset($this->options['min']))
			$this->min = $this->options['min'];
		if (isset($this->options['max']))
			$this->max = $this->options['max'];
	}
	
	public function renderInput() {
		w2gm_renderTemplate('content_fields/fields/number_input.tpl.php', array('content_field' => $this));
	}
	
	public function validateValues(&$errors, $data) {
		$field_index = 'w2gm-field-input-' . $this->id;

		$validation = new w2gm_form_validation();
		$rules = 'numeric';
		if ($this->canBeRequired() && $this->is_required)
			$rules .= '|required';
		if ($this->is_integer)
			$rules .= '|integer';
		if (is_numeric($this->min))
			$rules .= '|greater_than[' . $this->min . ']';
		if (is_numeric($this->max))
			$rules .= '|less_than[' . $this->max . ']';
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
		if (is_numeric($this->value)) {
			if ($this->is_integer)
				$decimals = 0;
			else 
				$decimals = 2;
			$formatted_number = number_format($this->value, $decimals, $this->decimal_separator, $this->thousands_separator);
	
			w2gm_renderTemplate('content_fields/fields/number_output.tpl.php', array('content_field' => $this, 'formatted_number' => $formatted_number, 'listing' => $listing));
		}
	}

	public function validateCsvValues($value, &$errors) {
		if (!is_numeric($value))
			$errors[] = sprintf(__('The %s field must contain only numbers.', 'W2GM'), $this->name);
		elseif ($this->is_integer && !ctype_digit($value))
			$errors[] = sprintf(__('The %s field must contain an integer.', 'W2GM'), $this->name);
		elseif (is_numeric($this->min) && $value < $this->min)
			$errors[] = sprintf(__('The %s field must contain a number greater than %s.', 'W2GM'), $this->name, $this->min);
		elseif (is_numeric($this->max) && $value > $this->max)
			$errors[] = sprintf(__('The %s field must contain a number less than %s.', 'W2GM'), $this->name, $this->max);

		return $value;
	}
	
	public function renderOutputForMap($location, $listing) {
		if (is_numeric($this->value)) {
			if ($this->is_integer)
				$decimals = 0;
			else 
				$decimals = 2;
			$formatted_number = number_format($this->value, $decimals, $this->decimal_separator, $this->thousands_separator);
	
			return $formatted_number;
		}
	}
}
?>