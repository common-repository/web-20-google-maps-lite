<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Configure select/checkbox/radio search field', 'W2GM'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Search input mode', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<select name="search_input_mode">
						<option value="checkboxes" <?php selected($search_field->search_input_mode, 'checkboxes'); ?>><?php _e('checkboxes', 'W2GM'); ?></option>
						<option value="selectbox" <?php selected($search_field->search_input_mode, 'selectbox'); ?>><?php _e('selectbox', 'W2GM'); ?></option>
						<option value="radiobutton" <?php selected($search_field->search_input_mode, 'radiobutton'); ?>><?php _e('radio buttons', 'W2GM'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Operator for the search', 'W2GM'); ?></label>
					<p class="description"><?php _e('Works only in checkboxes mode', 'W2GM'); ?></p>
				</th>
				<td>
					<label>
						<input
							name="checkboxes_operator"
							type="radio"
							value="OR"
							<?php checked($search_field->checkboxes_operator, 'OR'); ?> />
						<?php _e('OR - any item present is enough', 'W2GM')?>
					</label>
					<br />
					<label>
						<input
							name="checkboxes_operator"
							type="radio"
							value="AND"
							<?php checked($search_field->checkboxes_operator, 'AND'); ?> />
						<?php _e('AND - require all items', 'W2GM')?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(__('Save changes', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>