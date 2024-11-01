<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Configure price field', 'W2GM'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Currency symbol', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="currency_symbol"
						type="text"
						size="1"
						value="<?php echo esc_attr($content_field->currency_symbol); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Decimal separator', 'W2GM'); ?></label>
				</th>
				<td>
					<select name="decimal_separator">
						<option value="." <?php if($content_field->decimal_separator == '.') echo 'selected'; ?>><?php _e('dot', 'W2GM')?></option>
						<option value="," <?php if($content_field->decimal_separator == ',') echo 'selected'; ?>><?php _e('comma', 'W2GM')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Thousands separator', 'W2GM'); ?></label>
				</th>
				<td>
					<select name="thousands_separator">
						<option value="" <?php if($content_field->thousands_separator == '') echo 'selected'; ?>><?php _e('no separator', 'W2GM')?></option>
						<option value="." <?php if($content_field->thousands_separator == '.') echo 'selected'; ?>><?php _e('dot', 'W2GM')?></option>
						<option value="," <?php if($content_field->thousands_separator == ',') echo 'selected'; ?>><?php _e('comma', 'W2GM')?></option>
						<option value=" " <?php if($content_field->thousands_separator == ' ') echo 'selected'; ?>><?php _e('space', 'W2GM')?></option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(__('Save changes', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>