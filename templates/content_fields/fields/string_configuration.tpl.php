<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Configure text string field',  'W2GM'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Max length',  'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="max_length"
						type="text"
						size="2"
						value="<?php echo esc_attr($content_field->max_length); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('PHP RegEx template',  'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="regex"
						type="text"
						class="regular-text"
						value="<?php echo esc_attr($content_field->regex); ?>" />
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(__('Save changes', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>