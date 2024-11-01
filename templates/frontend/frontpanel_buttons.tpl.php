<div class="w2gm-content">
	<div class="w2gm-directory-frontpanel">
		<?php do_action('w2gm_directory_frontpanel', (isset($listing)) ? $listing : null); ?>

		<?php if (isset($listing)): ?>
			<?php if (w2gm_show_edit_button($listing->post->ID)): ?>
			<a class="w2gm-edit-listing-link w2gm-btn w2gm-btn-primary" href="<?php echo w2gm_get_edit_listing_link($listing->post->ID); ?>"><span class="w2gm-glyphicon w2gm-glyphicon-pencil"></span> <?php _e('Edit listing', 'W2GM'); ?></a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>