<?php 

class w2gm_content_field_tags extends w2gm_content_field {
	protected $can_be_required = false;
	protected $is_categories = false;
	protected $is_slug = false;
	
	public function isNotEmpty($listing) {
		if (has_term('', W2GM_TAGS_TAX, $listing->post->ID))
			return true;
		else
			return false;
	}

	public function renderOutput($listing) {
		w2gm_renderTemplate('content_fields/fields/tags_output.tpl.php', array('content_field' => $this, 'listing' => $listing));
	}
	
	public function renderOutputForMap($location, $listing) {
		return w2gm_renderTemplate('content_fields/fields/tags_output.tpl.php', array('content_field' => $this, 'listing' => $listing), true);
	}
}
?>