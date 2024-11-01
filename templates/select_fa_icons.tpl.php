<script>
(function($) {
	"use strict";
	
	$(document).on('keyup', '#search_icon', function() {
		if ($(this).val()) {
			$(".w2gm-icons-theme-block .w2gm-fa-icon").hide();
			$(".w2gm-icons-theme-block .w2gm-fa-icon[id*='"+$(this).val()+"']").show();
		} else
			$(".w2gm-icons-theme-block .w2gm-fa-icon").show();
	});
})(jQuery);
</script>

<div class="w2gm-content">
	<div class="w2gm-row">
		<div class="w2gm-col-md-6 w2gm-form-group w2gm-pull-left">
			<input type="text" id="search_icon" class="w2gm-form-control" placeholder="<?php _e('Search Icon', 'W2GM'); ?>" />
		</div>
		<div class="w2gm-col-md-6 w2gm-form-group w2gm-pull-right w2gm-text-right">
			<input type="button" id="reset_fa_icon" class="w2gm-btn w2gm-btn-primary w2gm-form-control" value="<?php esc_attr_e('Reset Icon', 'W2GM'); ?>" />
		</div>
		<div class="w2gm-clearfix"></div>
	</div>

	<div class="w2gm-icons-theme-block">
	<?php foreach ($icons AS $icon): ?>
		<span class="w2gm-fa-icon w2gm-fa w2gm-fa-lg <?php echo $icon; ?>" id="<?php echo $icon; ?>" title="<?php echo $icon; ?>"></span>
	<?php endforeach;?>
	</div>
	<div class="w2gm-clearfix"></div>
</div>