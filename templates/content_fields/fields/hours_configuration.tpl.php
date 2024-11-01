<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Configure opening hours field', 'W2GM'); ?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_configure_content_fields_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Time convention', 'W2GM'); ?></label>
				</th>
				<td>
					<label>
						<input
							name="hours_clock"
							type="radio"
							value="12"
							<?php if ($content_field->hours_clock == 12) echo 'checked'; ?> />
						<?php _e('12-hour clock', 'W2GM')?>
					</label>
					&nbsp;&nbsp;
					<label>
						<input
							name="hours_clock"
							type="radio"
							value="24"
							<?php if ($content_field->hours_clock == 24) echo 'checked'; ?> />
						<?php _e('24-hour clock', 'W2GM')?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php submit_button(__('Save changes', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>