<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2><?php _e('CSV Import'); ?></h2>

<p class="description"><?php _e('On this first step select CSV file for import, also you may import images in zip archive', 'W2GM'); ?></p>

<form method="POST" action="" enctype="multipart/form-data">
	<input type="hidden" name="action" value="import_settings">
	<?php wp_nonce_field(W2GM_PATH, 'w2gm_csv_import_nonce');?>
	
	<h3><?php _e('Import settings', 'W2GM'); ?></h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label><?php _e('CSV File', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="csv_file"
						type="file" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Images ZIP archive', 'W2GM'); ?>
				</th>
				<td>
					<input
						name="images_file"
						type="file" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Columns separator', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="columns_separator"
						type="text"
						size="2"
						value="<?php echo isset($columns_separator) ? esc_attr($columns_separator) : ','; ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e('Categories, Locations, Tags, Images, MultiValues separator', 'W2GM'); ?><span class="w2gm-red-asterisk">*</span></label>
				</th>
				<td>
					<input
						name="values_separator"
						type="text"
						size="2"
						value="<?php echo isset($values_separator) ? esc_attr($values_separator) : ';'; ?>" />
				</td>
			</tr>
		</tbody>
	</table>
	
	<?php w2gm_renderTemplate('csv_manager/import_instructions.tpl.php'); ?>
	
	<?php submit_button(__('Upload', 'W2GM')); ?>
</form>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>