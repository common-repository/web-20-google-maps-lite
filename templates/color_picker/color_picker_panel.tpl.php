	<script>
		(function($) {
			"use strict";

			$(function() {
				<?php $align = (is_rtl() ? 'right' : 'left' ); ?>
				$(document).on('mouseenter', '.w2gm-no-touch #w2gm-color-picker-panel', function () {
					$('#w2gm-color-picker-panel').stop().animate({<?php echo $align; ?>: "0px"}, 500);
				});
				$(document).on('mouseleave', '.w2gm-no-touch #w2gm-color-picker-panel', function () {
					var width = $('#w2gm-color-picker-panel').width() - 50;
					$('#w2gm-color-picker-panel').stop().animate({<?php echo $align; ?>: - width}, 500);
				});
	
				var panel_opened = false;
				$('html').on('click', '.w2gm-touch #w2gm-color-picker-panel-tools', function () {
					if (panel_opened) {
						var width = $('#w2gm-color-picker-panel').width() - 50;
						$('#w2gm-color-picker-panel').stop().animate({<?php echo $align; ?>: - width}, 500);
						panel_opened = false;
					} else {
						$('#w2gm-color-picker-panel').stop().animate({<?php echo $align; ?>: "0px"}, 500);
						panel_opened = true;
					}
				});
			});
		})(jQuery);
	</script>
	<div id="w2gm-color-picker-panel" class="w2gm-content">
		<fieldset id="w2gm-color-picker">
			<label><?php _e('Choose color palette:'); ?></label>
			<?php $selected_scheme = (isset($_COOKIE['w2gm_compare_palettes']) ? $_COOKIE['w2gm_compare_palettes'] : get_option('w2gm_color_scheme')); ?>
			<?php w2gm_renderTemplate('color_picker/color_picker_settings.tpl.php', array('selected_scheme' => $selected_scheme)); ?>
			<label><?php printf(__('Return to the <a href="%s">backend</a>', 'W2GM'), admin_url('admin.php?page=w2gm_settings#_customization')); ?></label>
		</fieldset>
		<div id="w2gm-color-picker-panel-tools" class="clearfix">
			<img src="<?php echo W2GM_RESOURCES_URL . 'images/settings.png'; ?>" />
		</div>
	</div>