<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php _e('Import From Web 2.0 Directory plugin', 'W2GM'); ?>
</h2>

<?php _e('We have found that Web 2.0 Directory plugin was installed on your WordPress site. Do you want to import categories, locations, tags, listings or settings from directory? You will not lose any data, everything will be copied from directory tables.', 'W2GM'); ?>
<br />
<?php _e('Would be better to import all data at once.', 'W2GM'); ?>
<br />
<strong><?php _e('Recommended to make database backup before import.', 'W2GM'); ?></strong>

<form method="POST" action="">
	<table class="form-table">
		<tbody>
			<tr>
				<td>
					<label>
						<input
							name="import_categories"
							type="checkbox"
							checked
							value="1" />
		
						<?php _e('Import directory categories', 'W2GM'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input
							name="import_locations"
							type="checkbox"
							checked
							value="1" />
		
						<?php _e('Import directory locations', 'W2GM'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input
							name="import_tags"
							type="checkbox"
							checked
							value="1" />
		
						<?php _e('Import directory tags', 'W2GM'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input
							name="import_fields"
							type="checkbox"
							checked
							value="1" />
		
						<?php _e('Import directory content fields with fields groups and listings fields data', 'W2GM'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input
							name="import_listings"
							type="checkbox"
							checked
							value="1" />
		
						<?php _e('Import directory listings', 'W2GM'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<input
							name="import_settings"
							type="checkbox"
							checked
							value="1" />
		
						<?php _e('Import similar directory settings', 'W2GM'); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<?php _e('Choose directory level from which to import similar settings', 'W2GM'); ?>
						<select name="import_level">
							<option value="0"><?php _e('- Do not import -', 'W2GM'); ?></option>
							<?php
							global $w2dc_instance;
							foreach ($w2dc_instance->levels->levels_array AS $level)
								echo "<option value=".$level->id.">".$level->name."</option>"; 
							?>
						</select>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
<?php submit_button(__('Import', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>