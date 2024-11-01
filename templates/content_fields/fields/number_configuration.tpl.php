<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Configure number field', 'W2GM'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Is integer or decimal', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="is_integer"
						type="radio"
						value="1"
						<?php if($content_field->is_integer) echo 'checked'; ?> />
					<?php _e('integer', 'W2GM')?>
					&nbsp;&nbsp;
					<input
						name="is_integer"
						type="radio"
						value="0"
						<?php if(!$content_field->is_integer) echo 'checked'; ?> />
					<?php _e('decimal', 'W2GM')?>
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
			<tr>
				<th scope="row">
					<label><?php _e('Min', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="min"
						type="text"
						size="2"
						value="<?php echo esc_attr($content_field->min); ?>" />
					<p class="description"><?php _e("leave empty if you do not need to limit this field", 'W2GM'); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Max', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="max"
						type="text"
						size="2"
						value="<?php echo esc_attr($content_field->max); ?>" />
					<p class="description"><?php _e("leave empty if you do not need to limit this field", 'W2GM'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(__('Save changes', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>