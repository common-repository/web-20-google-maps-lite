<script>
	(function($) {
		"use strict";
	
		$(function() {
			var fields_in_categories = new Array();
	<?php
	foreach ($content_fields AS $content_field): 
		if (!$content_field->is_core_field)
			if (!$content_field->isCategories() || $content_field->categories === array()) { ?>
				fields_in_categories[<?php echo $content_field->id?>] = [];
		<?php } else { ?>
				fields_in_categories[<?php echo $content_field->id?>] = [<?php echo implode(',', $content_field->categories); ?>];
		<?php } ?>
	<?php endforeach; ?>
	
			hideShowFields();
	
			$("input[name=tax_input\\[w2gm-category\\]\\[\\]]").change(function() {hideShowFields()});
			$("#w2gm-category-pop input[type=checkbox]").change(function() {hideShowFields()});
	
			function hideShowFields() {
				var selected_categories_ids = [];
				$.each($("input[name=tax_input\\[w2gm-category\\]\\[\\]]:checked"), function() {
					selected_categories_ids.push($(this).val());
				});
	
				$(".w2gm-field-input-block").hide();
				$.each(fields_in_categories, function(index, value) {
					var show_field = false;
					if (value != undefined) {
						if (value.length > 0) {
							var key;
							for (key in value) {
								var key2;
								for (key2 in selected_categories_ids)
									if (value[key] == selected_categories_ids[key2])
										show_field = true;
							}
						}
	
						if ((value.length == 0 || show_field) && $(".w2gm-field-input-block-"+index).length)
							$(".w2gm-field-input-block-"+index).show();
					}
				});
			}
		});
	})(jQuery);
</script>

<div class="w2gm-content">
	<div class="w2gm-content-fields-metabox w2gm-form-horizontal">
		<p class="w2gm-description-big"><?php _e('Content fields may be dependent on selected categories', 'W2GM'); ?></p>
		<?php
		foreach ($content_fields AS $content_field) {
			if (!$content_field->is_core_field)
				$content_field->renderInput();
		}
		?>
	</div>
</div>