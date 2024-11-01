<?php 

class w2gm_content_field_radio extends w2gm_content_field_select {
	protected $can_be_searched = true;
	protected $is_search_configuration_page = true;

	public function renderInput() {
		w2gm_renderTemplate('content_fields/fields/radio_input.tpl.php', array('content_field' => $this));
	}
}
?>