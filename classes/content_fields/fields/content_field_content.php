<?php 

class w2gm_content_field_content extends w2gm_content_field {
	protected $is_categories = false;
	protected $is_slug = false;
	
	public function isNotEmpty($listing) {
		if (post_type_supports(W2GM_POST_TYPE, 'editor') && !empty($listing->post->post_content))
			return true;
		else
			return false;
	}

	public function validateValues(&$errors, $data) {
		$listing = w2gm_getCurrentListingInAdmin();
		if (post_type_supports(W2GM_POST_TYPE, 'editor') && $this->is_required && (!isset($data['post_content']) || !$data['post_content']))
			$errors[] = __('Listing content is required', 'W2GM');
		else
			return $listing->post->post_content;
	}
	
	public function renderOutput($listing) {
		w2gm_renderTemplate('content_fields/fields/content_output.tpl.php', array('content_field' => $this, 'listing' => $listing));
	}
	
	public function renderOutputForMap($location, $listing) {
		return wpautop($listing->post->post_content);
	}
}
?>