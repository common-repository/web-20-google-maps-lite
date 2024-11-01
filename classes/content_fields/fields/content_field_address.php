<?php 

class w2gm_content_field_address extends w2gm_content_field {
	protected $can_be_required = true;
	protected $is_categories = false;
	protected $is_slug = false;
	
	public function isNotEmpty($listing) {
		foreach ($listing->locations AS $location)
			if ($location->getWholeAddress())
				return true;

		return false;
	}

	public function renderOutput($listing) {
		if (get_option('w2gm_locations_number'))
			w2gm_renderTemplate('content_fields/fields/address_output.tpl.php', array('content_field' => $this, 'listing' => $listing));
	}
	
	public function renderOutputForMap($location, $listing) {
		if (get_option('w2gm_locations_number'))
			return $location->getWholeAddress();
	}

	public function renderOutputForSidebar($location, $listing) {
		if (get_option('w2gm_locations_number'))
			w2gm_renderTemplate('content_fields/fields/address_location_output.tpl.php', array('content_field' => $this, 'location' => $location));
	}
}
?>