<?php 

class w2gm_content_field_categories extends w2gm_content_field {
	protected $can_be_required = true;
	protected $is_categories = false;
	protected $is_slug = false;
	
	public function isNotEmpty($listing) {
		if (has_term('', W2GM_CATEGORIES_TAX, $listing->post->ID))
			return true;
		else
			return false;
	}

	public function renderOutput($listing) {
		w2gm_renderTemplate('content_fields/fields/categories_output.tpl.php', array('content_field' => $this, 'listing' => $listing));
	}
	
	public function renderOutputForMap($location, $listing) {
		return w2gm_renderTemplate('content_fields/fields/categories_output_map.tpl.php', array('content_field' => $this, 'listing' => $listing), true);
	}
}
?>