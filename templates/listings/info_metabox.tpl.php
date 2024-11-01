<div id="misc-publishing-actions">
	<div class="misc-pub-section">
		<label for="post_level"><?php _e('Listing status', 'W2GM'); ?>:</label>
		<span id="post-level-display">
			<?php if ($listing->status == 'active'): ?>
			<span class="w2gm-badge w2gm-listing-status-active"><?php _e('active', 'W2GM'); ?></span>
			<?php elseif ($listing->status == 'expired'): ?>
			<span class="w2gm-badge w2gm-listing-status-expired"><?php _e('expired', 'W2GM'); ?></span><br />
			<a href="<?php echo admin_url('options.php?page=w2gm_renew&listing_id=' . $listing->post->ID); ?>"><img src="<?php echo W2GM_RESOURCES_URL; ?>images/page_refresh.png" class="w2gm-field-icon" /><?php echo apply_filters('w2gm_renew_option', __('renew listing', 'W2GM'), $listing); ?></a>
			<?php elseif ($listing->status == 'unpaid'): ?>
			<span class="w2gm-badge w2gm-listing-status-unpaid"><?php _e('unpaid ', 'W2GM'); ?></span>
			<?php elseif ($listing->status == 'stopped'): ?>
			<span class="w2gm-badge w2gm-listing-status-stopped"><?php _e('stopped', 'W2GM'); ?></span>
			<?php endif;?>
			<?php do_action('w2gm_listing_status_option', $listing); ?>
		</span>
	</div>

	<?php if (get_option('w2gm_enable_stats')): ?>
	<div class="misc-pub-section">
		<label for="post_level"><?php echo sprintf(__('Total clicks: %d', 'W2GM'), (get_post_meta($w2gm_instance->current_listing->post->ID, '_total_clicks', true) ? get_post_meta($w2gm_instance->current_listing->post->ID, '_total_clicks', true) : 0)); ?></label>
	</div>
	<?php endif; ?>

	<?php if (get_option('w2gm_eternal_active_period') || $listing->expiration_date): ?>
	<div class="misc-pub-section curtime">
		<span id="timestamp">
			<?php _e('Expiry on', 'W2GM'); ?>:
			<?php if (get_option('w2gm_eternal_active_period')): ?>
			<b><?php _e('Listing never expire', 'W2GM'); ?></b>
			<?php else: ?>
			<b><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), intval($listing->expiration_date)); ?></b>
			<?php endif; ?>
		</span>
	</div>
	<?php endif; ?>
</div>