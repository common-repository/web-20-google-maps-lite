<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Configure date-time field', 'W2GM'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Enable time in field', 'W2GM'); ?></label>
				</th>
				<td>
					<input
						name="is_time"
						type="checkbox"
						class="regular-text"
						value="1"
						<?php if($content_field->is_time) echo 'checked'; ?>/>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(__('Save changes', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>