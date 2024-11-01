<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php
	if ($locations_level_id)
		_e('Edit locations level', 'W2GM');
	else
		_e('Create new locations level', 'W2GM');
	?>
</h2>

<form method="POST" action="">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_locations_levels_nonce');?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('Level name', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="name"
						type="text"
						class="regular-text"
						value="<?php echo $locations_level->name; ?>" />
				</td>
			</tr>
			<!--<tr>
				<th scope="row">
					<label><?php //_e('In location widget', 'W2GM'); ?></label>
				</th>
				<td>
					<input type="checkbox" value="1" name="in_widget" <?php //if ($locations_level->in_widget) echo 'checked'; ?> />
					<p class="description"><?php //_e("Render locations of this level in select locations list widget", 'W2GM'); ?></p>
				</td>
			</tr> -->
			<tr>
				<th scope="row">
					<label><?php _e('In address line', 'W2GM'); ?></label>
				</th>
				<td>
					<input type="checkbox" value="1" name="in_address_line" <?php if ($locations_level->in_address_line) echo 'checked'; ?> />
					<p class="description"><?php _e("Render locations of this level in address line", 'W2GM'); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php
	if ($locations_level_id)
		submit_button(__('Save changes', 'W2GM'));
	else
		submit_button(__('Create locations level', 'W2GM'));
	?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>