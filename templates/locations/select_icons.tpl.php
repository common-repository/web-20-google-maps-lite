		<input type="button" id="reset_icon" class="button button-primary button-large w2gm-btn w2gm-button-primary" value="<?php esc_attr_e('Reset icon image', 'w2gm'); ?>" />

		<table width="100%" cellpadding="0" cellspacing="0">
			<tr>
			<?php $i = 0; ?>
			<?php foreach ($custom_map_icons AS $theme=>$dir): ?>
				<?php if (is_array($dir) && count($dir)): ?>
				<?php $columns = 1; ?>
				<td align="left" valign="top" width="<?php echo 100/$columns; ?>%">
					<div class="w2gm-icons-theme-block">
						<div class="w2gm-icons-theme-name"><?php echo $theme; ?></div>
						<?php foreach ($dir AS $icon): ?>
							<div class="w2gm-icon" icon_file="<?php echo $theme . '/' . $icon; ?>"><img src="<?php echo W2GM_MAP_ICONS_URL . 'icons/' . $theme . '/' . $icon; ?>" title="<?php echo $theme . '/' . $icon; ?>" /></div>
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