<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Configure select/radio buttons/checkboxes field', 'W2GM'); ?>
</h2>

<script>
	(function($) {
		"use strict";
	
		$(function() {
			var max_index = <?php echo ((count(array_keys($content_field->selection_items)) ? max(array_keys($content_field->selection_items)) : 1)); ?>;
			$("#add_selection_item").click(function() {
				max_index = max_index+1;
				$("#selection_items_wrapper").append('<div class="selection_item"><input name="selection_items['+max_index+']" type="text" class="regular-text" value="" /><img class="w2gm-delete-selection-item" src="<?php echo W2GM_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove selection item', 'W2GM')?>" /><span class="w2gm-move-label"><?php esc_attr_e('move', 'W2GM'); ?></span><?php echo esc_js('(ID: ', 'W2GM'); ?>'+max_index+')</div>');
			});
			$(document).on("click", ".w2gm-delete-selection-item", function() {
				$(this).parent().remove();
			});

			$("#selection_items_wrapper").sortable({
				placeholder: "ui-sortable-placeholder",
				helper: function(e, ui) {
					ui.children().each(function() {
						$(this).width($(this).width());
					});
					return ui;
				},
				start: function(e, ui){
					ui.placeholder.height(ui.item.height());
				}
	    	});
		});
	})(jQuery);
</script>

<?php _e('You may order items by drag & drop.', 'W2GM'); ?>
<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Selection items:', 'W2GM'); ?></label>
				</th>
				<td>
					<div id="selection_items_wrapper">
						<?php if (count($content_field->selection_items)): ?>
						<?php foreach ($content_field->selection_items AS $key=>$item): ?>
						<div class="selection_item">
							<input
								name="selection_items[<?php echo $key; ?>]"
								type="text"
								class="regular-text"
								value="<?php echo $item; ?>" />
							<img class="w2gm-delete-selection-item" src="<?php echo W2GM_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove selection item', 'W2GM')?>" />
							<span class="w2gm-move-label"><?php _e('move', 'W2GM'); ?></span>
							<?php printf(__('(ID: %d)', 'W2GM'), $key); ?>
						</div>
						<?php endforeach; ?>
						<?php else: ?>
						<div class="selection_item">
							<input
								name="selection_items[1]"
								type="text"
								class="regular-text"
								value="" />
							<img class="w2gm-delete-selection-item" src="<?php echo W2GM_RESOURCES_URL . 'images/delete.png'?>" title="<?php esc_attr_e('Remove selection item', 'W2GM')?>" />
							<span class="w2gm-move-label"><?php _e('move', 'W2GM'); ?></span>
							<?php printf(__('(ID: %d)', 'W2GM'), 1); ?>
						</div>
						<?php endif; ?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="button" id="add_selection_item" class="button button-primary" value="<?php esc_attr_e('Add selection item', 'W2GM'); ?>" />
	
	<?php submit_button(__('Save changes', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>