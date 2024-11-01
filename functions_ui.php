<?php

function w2gm_tax_dropdowns_init($tax = 'category', $field_name = null, $term_id = null, $count = true, $labels = array(), $titles = array(), $uID = null) {
	// unique ID need when we place some dropdowns groups on one page
	if (!$uID)
		$uID = rand(1, 10000);

	$localized_data[$uID] = array(
			'labels' => $labels,
			'titles' => $titles
	);
	echo "<script>w2gm_js_objects['tax_dropdowns_" . $uID . "'] = " . json_encode($localized_data) . "</script>";

	if (!is_null($term_id) && $term_id != 0) {
		$chain = array();
		$parent_id = $term_id;
		while ($parent_id != 0) {
			if ($term = get_term($parent_id, $tax)) {
				$chain[] = $term->term_id;
				$parent_id = $term->parent;
			} else
				break;
		}
	}
	$path_chain = array();
	$chain[] = 0;
	$chain = array_reverse($chain);

	if (!$field_name) {
		$field_name = 'selected_tax[' . $uID . ']';
		$path_field_name = 'selected_tax_path[' . $uID . ']';
	} else {
		$path_field_name = $field_name . '_path';
	}

	echo '<div id="w2gm-tax-dropdowns-wrap-' . $uID . '" class="' . $tax . ' cs_count_' . (int)$count . ' w2gm-tax-dropdowns-wrap">';
	echo '<input type="hidden" name="' . $field_name . '" id="selected_tax[' . $uID . ']" class="selected_tax_' . $tax . '" value="' . $term_id . '" />';
	foreach ($chain AS $key=>$term_id) {
		if ($count)
			// there is a wp bug with pad_counts in get_terms function - so we use this construction
			$terms = wp_list_filter(get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => false)), array('parent' => $term_id));
		else
			$terms = get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => false, 'parent' => $term_id));
		if (!empty($terms)) {
			$level_num = $key + 1;
			echo '<div id="wrap_chainlist_' . $level_num . '_' .$uID . '" class="w2gm-row w2gm-form-group w2gm-location-input">';

				if (isset($labels[$key]))
					echo '<label class="w2gm-col-md-2 w2gm-control-label" for="chainlist_' . $level_num . '_' . $uID . '">' . $labels[$key] . '</label>';

				if (isset($labels[$key]))
				echo '<div class="w2gm-col-md-10">';
				else
				echo '<div class="w2gm-col-md-12">';
					echo '<select id="chainlist_' . $level_num . '_' . $uID . '" class="w2gm-form-control">';
					echo '<option value="">- ' . ((isset($titles[$key])) ? $titles[$key] : __('Select term', 'W2GM')) . ' -</option>';
					foreach ($terms as $term) {
						if ($count)
							$term_count = " ($term->count)";
						else
							 $term_count = '';
						if (isset($chain[$key+1]) && $term->term_id == $chain[$key+1]) {
							$selected = 'selected';
							$path_chain[] = $term->name;
						} else
							$selected = '';
						echo '<option id="' . $term->slug . '" value="' . $term->term_id . '" ' . $selected . '>' . $term->name . $term_count . '</option>';
					}
					echo '</select>';
				echo '</div>';
			echo '</div>';
		}
		echo '<input type="hidden" name="' . $path_field_name . '" id="selected_tax_path[' . $uID . ']" class="selected_tax_path_' . $tax . '" value="' . implode(', ', $path_chain) . '" />';
	}
	echo '</div>';
}

function w2gm_tax_dropdowns_updateterms() {
	$parentid = w2gm_getValue($_POST, 'parentid');
	$next_level = w2gm_getValue($_POST, 'next_level');
	$tax = w2gm_getValue($_POST, 'tax');
	$count = w2gm_getValue($_POST, 'count');
	if (!$label = w2gm_getValue($_POST, 'label'))
		$label = '';
	if (!$title = w2gm_getValue($_POST, 'title'))
		$title = __('Select term', 'W2GM');
	$uID = w2gm_getValue($_POST, 'uID');

	if ($count == 'cs_count_1')
		// there is a wp bug with pad_counts in get_terms function - so we use this construction
		$terms = wp_list_filter(get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => false)), array('parent' => $parentid));
	else
		$terms = get_categories(array('taxonomy' => $tax, 'pad_counts' => true, 'hide_empty' => false, 'parent' => $parentid));
	if (!empty($terms)) {
		echo '<div id="wrap_chainlist_' . $next_level . '_' . $uID . '" class="w2gm-row w2gm-form-group w2gm-location-input">';

			if ($label)
				echo '<label class="w2gm-col-md-2 w2gm-control-label" for="chainlist_' . $next_level . '_' . $uID . '">' . $label . '</label>';

			if ($label)
			echo '<div class="w2gm-col-md-10">';
			else 
			echo '<div class="w2gm-col-md-12">';
				echo '<select id="chainlist_' . $next_level . '_' . $uID . '" class="w2gm-form-control">';
				echo '<option value="">- ' . $title . ' -</option>';
				foreach ($terms as $term) {
					if ($count == 'cs_count_1') {
						$term_count = " ($term->count)";
					} else { $term_count = '';
					}
					echo '<option id="' . $term->slug . '" value="' . $term->term_id . '">' . $term->name . $term_count . '</option>';
				}
		
				echo '</select>';
			echo '</div>';
		echo '</div>';

	}
	die();
}

function w2gm_renderOptionsTerms($tax, $parent, $selected_terms, $level = 0) {
	$terms = get_terms($tax, array('parent' => $parent, 'hide_empty' => false));

	foreach ($terms AS $term) {
		echo '<option value="' . $term->term_id . '" ' . (($selected_terms && (in_array($term->term_id, $selected_terms) || in_array($term->slug, $selected_terms))) ? 'selected' : '') . '>' . (str_repeat('&nbsp;&nbsp;&nbsp;', $level)) . $term->name . '</option>';
		w2gm_renderOptionsTerms($tax, $term->term_id, $selected_terms, $level+1);
	}
	return $terms;
}
function w2gm_termsSelectList($name, $tax = 'category', $selected_terms = array()) {
	echo '<select multiple="multiple" name="' . $name . '[]" class="selected_terms_list w2gm-form-control w2gm-form-group" style="height: 300px">';
	echo '<option value="" ' . ((!$selected_terms) ? 'selected' : '') . '>' . __('- Select All -', 'W2GM') . '</option>';

	w2gm_renderOptionsTerms($tax, 0, $selected_terms);

	echo '</select>';
}

?>