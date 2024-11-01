		<input type="button" id="reset_png_image" class="button button-primary button-large w2gm-btn w2gm-button-primary" value="<?php esc_attr_e('Reset image', 'W2GM'); ?>" />

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
			<?php $i = 0; ?>
			<?php foreach ($custom_map_images AS $theme=>$dir): ?>
				<?php if (is_array($dir) && count($dir)): ?>
				<?php $columns = 1; ?>
				<td align="left" valign="top" width="<?php echo 100/$columns; ?>%">
					<div class="w2gm-icons-theme-block">
						<div class="w2gm-icons-theme-name"><?php echo $theme; ?></div>
						<?php foreach ($dir AS $image): ?>
							<div class="w2gm-png-image" icon_file="<?php echo $theme . '/' . $image; ?>"><img src="<?php echo W2GM_MAP_ICONS_URL . 'icons/' . $theme . '/' . $image; ?>" title="<?php echo $theme . '/' . $image; ?>" /></div>
						<?php endforeach;?>
					</div>
					<div class="clear_float"></div>
				</td>
				<?php if ($i++ == $columns-1): ?>
					</tr><tr>
					<?php $i = 0; ?>
				<?php endif;?>
				<?php endif;?>
			<?php endforeach;?>
			</tr>
		</table>