<?php w2gm_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php echo apply_filters('w2gm_renew_option', __('Renew listing', 'W2GM'), $listing); ?>
</h2>

<p><?php _e('Listing will be renewed and raised up to the top of all lists, those ordered by date.', 'W2GM'); ?></p>

<?php do_action('w2gm_renew_html', $listing); ?>

<?php if ($action == 'show'): ?>
<a href="<?php echo admin_url('options.php?page=w2gm_renew&listing_id=' . $listing->post->ID . '&renew_action=renew&referer=' . urlencode($referer)); ?>" class="button button-primary"><?php _e('Renew listing', 'W2GM'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $referer; ?>" class="button button-primary"><?php _e('Cancel', 'W2GM'); ?></a>
<?php elseif ($action == 'renew'): ?>
<a href="<?php echo $referer; ?>" class="button button-primary"><?php _e('Go back ', 'W2GM'); ?></a>
<?php endif; ?>

<?php w2gm_renderTemplate('admin_footer.tpl.php'); ?>