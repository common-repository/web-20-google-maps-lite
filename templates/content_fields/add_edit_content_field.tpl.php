<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php
	if ($field_id)
		_e('Edit content field', 'W2GM');
	else
		_e('Create new content field', 'W2GM');
	?>
</h2>
<?php if ($content_field->is_core_field): ?>
<p class="description"><?php esc_attr_e("You can't select assigned categories for core fields such as content, excerpt, categories, tags and addresses", 'W2GM'); ?></p>
<?php endif; ?>

<script>
	(function($) {
		"use strict";
	
		$(function() {
			$("#content_field_name").keyup(function() {
				$("#content_field_slug").val(w2gm_make_slug($("#content_field_name").val()));
			});
	
			<?php if (!$content_field->is_core_field): ?>
			$("#type").change(function() {
				if (
					<?php
					foreach ($content_fields->fields_types_names AS $content_field_type=>$content_field_name){
						$field_class_name = 'w2gm_content_field_' . $content_field_type;
						if (class_exists($field_class_name)) {
							$_content_field = new $field_class_name;
							if (!$_content_field->canBeRequired()) {
					?>
					$(this).val() == '<?php echo $content_field_type; ?>' ||
					<?php
							}
						}
					} ?>
				'x'=='y')
					$("#is_required_block").hide();
				else
					$("#is_required_block").show();
			});
			<?php endif; ?>
	
			<?php if ($content_field->icon_image): ?>
			$(".w2gm-icon-tag").removeClass().addClass('w2gm-icon-tag w2gm-fa '+$("#icon_image").val());
			$(".w2gm-icon-tag").show();
			<?php else: ?>
			$(".w2gm-icon-tag").hide();
			<?php endif; ?>
	
			$(document).on("click", ".select_icon_image", function() {
				var dialog = $('<div id="select_field_icon_dialog"></div>').dialog({
					width: ($(window).width()*0.5),
					height: ($(window).height()*0.8),
					modal: true,
					resizable: false,
					draggable: false,
					title: '<?php echo esc_js(__('Select content field icon', 'W2GM')); ?>',
					open: function() {
						w2gm_ajax_loader_show();
						$.ajax({
							type: "POST",
							url: w2gm_js_objects.ajaxurl,
							data: {'action': 'w2gm_select_field_icon'},
							dataType: 'html',
							success: function(response_from_the_action_function){
								if (response_from_the_action_function != 0) {
									$('#select_field_icon_dialog').html(response_from_the_action_function);
									if ($("#icon_image").val())
										$("#"+$("#icon_image").val()).addClass("w2gm-selected-icon");
								}
							},
							complete: function() {
								w2gm_ajax_loader_hide();
							}
						});
						$(document).on("click", ".ui-widget-overlay", function() { $('#select_field_icon_dialog').remove(); });
					},
					close: function() {
						$('#select_field_icon_dialog').remove();
					}
				});
			});
			$(document).on("click", ".w2gm-fa-icon", function() {
				$(".w2gm-selected-icon").removeClass("w2gm-selected-icon");
				$("#icon_image").val($(this).attr('id'));
				$(".w2gm-icon-tag").removeClass().addClass('w2gm-icon-tag w2gm-fa '+$("#icon_image").val());
				$(".w2gm-icon-tag").show();
				$(this).addClass("w2gm-selected-icon");
				$('#select_field_icon_dialog').remove();
			});
			$(document).on("click", "#reset_fa_icon", function() {
				$(".w2gm-selected-icon").removeClass("w2gm-selected-icon");
				$(".w2gm-icon-tag").removeClass();
				$(".w2gm-icon-tag").hide();
				$("#icon_image").val('');
				$('#select_field_icon_dialog').remove();
			});
		});
	})(jQuery);
</script>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Field name', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="name"
						id="content_field_name"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->name); ?>" />
				</td>
			</tr>
			<?php if ($content_field->isSlug()) :?>
			<tr>
				<th scope="row">
					<label><?php _e('Field slug', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="slug"
						id="content_field_slug"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->slug); ?>" />
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th scope="row">
					<label><?php _e('Hide name', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="is_hide_name"
						type="checkbox"
						value="1"
						<?php checked($content_field->is_hide_name); ?> />
					<p class="description"><?php _e("Hide field name at the frontend?", 'W2GM'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Field description', 'W2GM'); ?></label>
				</th>
				<td>
					<textarea
						name="description"
						cols="60"
						rows="4" ><?php echo esc_textarea($content_field->description); ?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Icon image', 'W2GM'); ?></label>
				</th>
				<td>
					<span class="w2gm-icon-tag"></span>
					<input type="hidden" name="icon_image" id="icon_image" value="<?php echo esc_attr($content_field->icon_image); ?>">
					<div>
						<a class="select_icon_image" href="javascript: void(0);"><?php _e('Select field icon', 'W2GM'); ?></a>
					</div>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label><?php _e('Field type', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<select name="type" id="type" <?php disabled($content_field->is_core_field); ?>>
						<option value=""><?php _e('- Select field type -', 'W2GM'); ?></option>
						<?php if ($content_field->is_core_field) :?>
						<option value="excerpt" <?php selected($content_field->type, 'excerpt'); ?> ><?php echo $fields_types_names['excerpt']; ?></option>
						<option value="content" <?php selected($content_field->type, 'content'); ?> ><?php echo $fields_types_names['content']; ?></option>
						<option value="categories" <?php selected($content_field->type, 'categories'); ?> ><?php echo $fields_types_names['categories']; ?></option>
						<option value="tags" <?php selected($content_field->type, 'tags'); ?> ><?php echo $fields_types_names['tags']; ?></option>
						<option value="address" <?php selected($content_field->type, 'address'); ?> ><?php echo $fields_types_names['address']; ?></option>
						<?php endif; ?>
						<option value="string" <?php selected($content_field->type, 'string'); ?> ><?php echo $fields_types_names['string']; ?></option>
						<option value="textarea" <?php selected($content_field->type, 'textarea'); ?> ><?php echo $fields_types_names['textarea']; ?></option>
						<option value="number" <?php selected($content_field->type, 'number'); ?> ><?php echo $fields_types_names['number']; ?></option>
						<option value="select" <?php selected($content_field->type, 'select'); ?> ><?php echo $fields_types_names['select']; ?></option>
						<option value="radio" <?php selected($content_field->type, 'radio'); ?> ><?php echo $fields_types_names['radio']; ?></option>
						<option value="checkbox" <?php selected($content_field->type, 'checkbox'); ?> ><?php echo $fields_types_names['checkbox']; ?></option>
						<option value="website" <?php selected($content_field->type, 'website'); ?> ><?php echo $fields_types_names['website']; ?></option>
						<option value="email" <?php selected($content_field->type, 'email'); ?> ><?php echo $fields_types_names['email']; ?></option>
						<option value="datetime" <?php selected($content_field->type, 'datetime'); ?> ><?php echo $fields_types_names['datetime']; ?></option>
						<option value="price" <?php selected($content_field->type, 'price'); ?> ><?php echo $fields_types_names['price']; ?></option>
						<option value="hours" <?php selected($content_field->type, 'hours'); ?> ><?php echo $fields_types_names['hours']; ?></option>
					</select>
					<?php if ($content_field->is_core_field): ?>
					<p class="description"><?php esc_attr_e("You can't change the type of core fields", 'W2GM'); ?></p>
					<?php endif; ?>
				</td>
			</tr>

			<tr id="is_required_block" <?php if (!$content_field->canBeRequired()): ?>style="display: none;"<?php endif; ?>>
				<th scope="row">
					<label><?php _e('Is this field required?', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="is_required"
						type="checkbox"
						value="1"
						<?php checked($content_field->is_required); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('In map marker InfoWindow', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="on_map"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_map); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('In listing window', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="on_listing_page"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_listing_page); ?> />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('In listings sidebar', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="on_listing_sidebar"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_listing_sidebar); ?> />
				</td>
			</tr>
			
			<script>
				(function($) {
					"use strict";
	
					$(function() {
						<?php if (!$content_field->is_core_field): ?>
						$("#type").change(function() {
							if (
								<?php 
								foreach ($content_fields->fields_types_names AS $content_field_type=>$content_field_name){
									$field_class_name = 'w2gm_content_field_' . $content_field_type;
									if (class_exists($field_class_name)) {
										$_content_field = new $field_class_name;
										if (!$_content_field->canBeSearched()) {
								?>
								$(this).val() == '<?echo $content_field_type; ?>' ||
								<?php
										}
									}
								} ?>
							$(this).val() === '')
								$(".can_be_searched_block").hide();
							else
								$(".can_be_searched_block").show();
						});
						$("#on_search_form").click( function() {
							if ($(this).is(':checked'))
								$('input[name="advanced_search_form"]').removeAttr('disabled');
							else 
								$('input[name="advanced_search_form"]').attr('disabled', true);
						});
						<?php endif; ?>
					});
				})(jQuery);
			</script>
			<tr class="can_be_searched_block" <?php if (!$content_field->canBeSearched()): ?>style="display: none;"<?php endif; ?>>
				<th scope="row">
					<label><?php _e('Search by this field?', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						id="on_search_form"
						name="on_search_form"
						type="checkbox"
						value="1"
						<?php checked($content_field->on_search_form); ?> />
					<p class="description"><?php _e('Search options apply only in the search form of [webmap-search] shortcode', 'W2GM'); ?></p>
				</td>
			</tr>
			<tr class="can_be_searched_block" <?php if (!$content_field->canBeSearched()): ?>style="display: none;"<?php endif; ?>>
				<th scope="row">
					<label><?php _e('On advanced search panel?', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="advanced_search_form"
						type="checkbox"
						value="1"
						<?php checked($content_field->advanced_search_form); ?>
						<?php disabled(!$content_field->on_search_form)?> />
				</td>
			</tr>
			
			<?php do_action('w2gm_content_field_html', $content_field); ?>
			
			<?php if ($content_field->isCategories()): ?>
			<tr>
				<th scope="row">
					<label><?php _e('Assigned categories', 'W2GM'); ?></label>
					<?php echo w2gm_get_wpml_dependent_option_description(); ?>
				</th>
				<td>
					<?php w2gm_termsSelectList('categories_list', W2GM_CATEGORIES_TAX, $content_field->categories); ?>
				</td>
			</tr>
			<?php endif; ?>
			
		</tbody>
	</table>
	
	<?php
	if ($field_id)
		submit_button(__('Save changes', 'W2GM'));
	else
		submit_button(__('Create content field', 'W2GM'));
	?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>