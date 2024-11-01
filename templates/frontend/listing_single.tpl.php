		<div class="w2gm-content" data-shortcode-hash="<?php echo $frontend_controller->hash; ?>">
			<?php w2gm_renderMessages(); ?>

			<?php if ($frontend_controller->listings): ?>
			<?php while ($frontend_controller->query->have_posts()): ?>
				<?php $frontend_controller->query->the_post(); ?>
				<?php $listing = $frontend_controller->listings[get_the_ID()]; ?>
				
				<?php w2gm_renderTemplate('frontend/frontpanel_buttons.tpl.php', array('listing' => $listing)); ?>

				<div id="<?php echo $listing->post->post_name; ?>">
					<?php if ($listing->title()): ?>
					<header class="w2gm-listing-header">
						<h2><?php echo $listing->title(); ?></h2><?php do_action('w2gm_listing_title_html', $listing); ?>
						<?php if (!get_option('w2gm_hide_listings_creation_date') || !get_option('w2gm_hide_author_link')): ?>
						<div class="w2gm-meta-data">
							<?php if (!get_option('w2gm_hide_listings_creation_date')): ?>
							<div class="w2gm-listing-date" datetime="<?php echo date("Y-m-d", mysql2date('U', $listing->post->post_date)); ?>T<?php echo date("H:i", mysql2date('U', $listing->post->post_date)); ?>"><?php echo get_the_date(); ?> <?php echo get_the_time(); ?></div>
							<?php endif; ?>
							<?php if (!get_option('w2gm_hide_author_link')): ?>
							<div class="w2gm-author-link">
								<?php _e('By', 'W2GM'); ?> <?php echo get_the_author_link(); ?>
							</div>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</header>
					<?php endif; ?>

					<article id="post-<?php the_ID(); ?>" class="w2gm-listing">
						<?php if (get_option('w2gm_listing_logo_enabled') && $listing->logo_image): ?>
						<?php
						$image_src = wp_get_attachment_image_src($listing->logo_image, 'full');
						?>
						<div class="w2gm-listing-logo-wrap w2gm-single-listing-logo-wrap" id="images">
							<?php do_action('w2gm_listing_pre_logo_wrap_html', $listing); ?>
							
							<div class="w2gm-content w2gm-slider-wrapper" style="max-width: 400px;">
								<div class="w2gm-big-slide-wrapper" style="height: 290px;">
									<div class="w2gm-big-slide" style="height: 280px; background-image: url('<?php echo $image_src[0]; ?>');"></div>
								</div>
							</div>
						</div>
						<?php endif; ?>

						<div class="w2gm-single-listing-text-content-wrap">
							<?php do_action('w2gm_listing_pre_content_html', $listing); ?>
					
							<?php $listing->renderContentFields(true); ?>
					
							<?php do_action('w2gm_listing_post_content_html', $listing); ?>
						</div>

						<script>
							(function($) {
								"use strict";
	
								$(function() {
									<?php if (get_option('w2gm_listings_tabs_order')): ?>
									if (1==2) var x = 1;
									<?php foreach (get_option('w2gm_listings_tabs_order') AS $tab): ?>
									else if ($('#<?php echo $tab; ?>').length)
										w2gm_show_tab($('.w2gm-listing-tabs a[data-tab="#<?php echo $tab; ?>"]'));
									<?php endforeach; ?>
									<?php else: ?>
									w2gm_show_tab($('.w2gm-listing-tabs a:first'));
									<?php endif; ?>
								});
							})(jQuery);
						</script>

						<?php if (
							($fields_groups = $listing->getFieldsGroupsOnTabs())
							|| (get_option('w2gm_enable_map_listing') && $listing->isMap() && $listing->locations)
							): ?>
						<ul class="w2gm-listing-tabs w2gm-nav w2gm-nav-tabs w2gm-clearfix" role="tablist">
							<?php if (get_option('w2gm_enable_map_listing') && $listing->isMap() && $listing->locations): ?>
							<li><a href="javascript: void(0);" data-tab="#addresses-tab" data-toggle="w2gm-tab" role="tab"><?php _e('Map', 'W2GM'); ?></a></li>
							<?php endif; ?>
							<?php
							foreach ($fields_groups AS $fields_group): ?>
							<li><a href="javascript: void(0);" data-tab="#field-group-tab-<?php echo $fields_group->id; ?>" data-toggle="w2gm-tab" role="tab"><?php echo $fields_group->name; ?></a></li>
							<?php endforeach; ?>
						</ul>

						<div class="w2gm-tab-content">
							<?php if (get_option('w2gm_enable_map_listing') && $listing->isMap() && $listing->locations): ?>
							<div id="addresses-tab" class="w2gm-tab-pane w2gm-fade" role="tabpanel">
								<?php $listing->renderMap($frontend_controller->hash, get_option('w2gm_show_directions'), false, false, get_option('w2gm_enable_clusters'), false, false); ?>
							</div>
							<?php endif; ?>

							<?php foreach ($fields_groups AS $fields_group): ?>
							<div id="field-group-tab-<?php echo $fields_group->id; ?>" class="w2gm-tab-pane w2gm-fade" role="tabpanel">
								<?php echo $fields_group->renderOutput($listing, true); ?>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</article>
				</div>
			<?php endwhile; endif; ?>
		</div>