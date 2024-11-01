<?php if (get_option('w2gm_listing_logo_enabled')): ?>
<?php
$img_width = get_option('thumbnail_size_w'); 
$img_height = get_option('thumbnail_size_h'); 
?>
<script>
	var images_number = 1;

	(function($) {
		"use strict";

		$(function() {
			$("#images_wrapper").on('click', '.delete_item', function() {
				$(this).parent().remove();
	
				if (images_number > $("#images_wrapper .w2gm-attached-item").length)
					$("#w2gm-upload-functions").show();
			});
		});
	})(jQuery);
</script>

<div id="w2gm-upload-wrapper">
	<h3>
		<?php _e('Listing image', 'W2GM'); ?>
	</h3>

	<div id="images_wrapper">
	<?php $src = wp_get_attachment_image_src($listing->logo_image, 'thumbnail'); ?>
	<?php $src_full = wp_get_attachment_image_src($listing->logo_image, 'full'); ?>
	<div class="w2gm-attached-item">
		<div class="w2gm-delete-attached-item delete_item" title="<?php esc_attr_e('remove image', 'W2GM'); ?>"></div>
		<input type="hidden" name="attached_image_id" value="<?php echo $listing->logo_image; ?>" />
		<div class="w2gm-img-div-border" style="width: <?php echo $img_width; ?>px; height: <?php echo $img_height; ?>px">
			<span class="w2gm-img-div-helper"></span><img src="<?php echo $src[0]; ?>" style="max-width: <?php echo $img_width; ?>px; max-height: <?php echo $img_height; ?>px" />
		</div>
	</div>
	</div>
	<div class="clear_float"></div>

	<?php if (current_user_can('upload_files')): ?>
	<script>
		(function($) {
			"use strict";
		
			$(function() {
				$('#upload_image').click(function(event) {
					event.preventDefault();
			
					var frame = wp.media({
			            title : '<?php echo esc_js(sprintf(__('Upload image (%d maximum)', 'W2GM'), get_option('w2gm_listing_logo_enabled'))); ?>',
			            multiple : true,
			            library : { type : 'image'},
			            button : { text : '<?php echo esc_js(__('Insert', 'W2GM')); ?>'},
			        });
					frame.on( 'select', function() {
					    var selection = frame.state().get('selection');
					    selection.each(function(attachment) {
					    	attachment = attachment.toJSON();
					    	if (attachment.type == 'image') {
					    		if (images_number > $("#images_wrapper .w2gm-attached-item").length) {
									w2gm_ajax_loader_show();
		
									if (typeof attachment.sizes.thumbnail != 'undefined')
										var attachment_url = attachment.sizes.thumbnail.url;
									else
										var attachment_url = attachment.sizes.full.url;
									var attachment_url_full = attachment.sizes.full.url;
									var attachment_id = attachment.id;
									$('<div class="w2gm-attached-item"><div class="w2gm-delete-attached-item delete_item" title="<?php esc_attr_e('remove image', 'W2GM'); ?>"></div><input type="hidden" name="attached_image_id" value="' + attachment_id + '" /><div class="w2gm-img-div-border" style="width: <?php echo $img_width; ?>px; height: <?php echo $img_height; ?>px"><span class="w2gm-img-div-helper"></span><img src="' + attachment_url + '" style="max-width: <?php echo $img_width; ?>px; max-height: <?php echo $img_height; ?>px" /></div></div>').appendTo("#images_wrapper");
	
									$.post(
										w2gm_js_objects.ajaxurl,
										{'action': 'w2gm_upload_media_image', 'attachment_id': attachment_id, 'post_id': <?php echo $listing->post->ID; ?>, '_wpnonce': '<?php echo wp_create_nonce('upload_images'); ?>'},
										function (response_from_the_action_function){
											w2gm_ajax_loader_hide();
										}
									);
								}
					    		if (images_number <= $("#images_wrapper .w2gm-attached-item").length)
									jQuery("#w2gm-upload-functions").hide();
					    	}
						});
					});
					frame.open();
				});
			});
		})(jQuery);
	</script>
	<div id="w2gm-upload-functions" class="w2gm-content" <?php if ($listing->logo_image): ?>style="display: none;"<?php endif; ?>>
		<div class="w2gm-upload-option">
			<input
				type="button"
				id="upload_image"
				class="w2gm-btn w2gm-btn-primary"
				value="<?php esc_attr_e('Upload image', 'W2GM'); ?>" />
		</div>
	</div>
	<?php else: ?>
	<script>
		(function($) {
			"use strict";
	
			window.addImageDiv = function(data) {
				var attachment_url = data.uploaded_file;
				var attachment_id = data.attachment_id;
				$('<div class="w2gm-attached-item"><div class="w2gm-delete-attached-item delete_item" title="<?php esc_attr_e('remove image', 'W2GM'); ?>"></div><input type="hidden" name="attached_image_id" value="' + attachment_id + '" /><div class="w2gm-img-div-border" style="width: <?php echo $img_width; ?>px; height: <?php echo $img_height; ?>px"><span class="w2gm-img-div-helper"></span><img src="' + attachment_url + '" style="max-width: <?php echo $img_width; ?>px; max-height: <?php echo $img_height; ?>px" /></div></div>').appendTo("#images_wrapper");
		
				if (images_number <= jQuery("#images_wrapper .w2gm-attached-item").length)
					$("#w2gm-upload-functions").hide();
			};
		})(jQuery);
	</script>
	<div id="w2gm-upload-functions" class="w2gm-content" <?php if ($listing->logo_image): ?>style="display: none;"<?php endif; ?>>
		<div class="w2gm-upload-option">
			<input id="browse_file" name="browse_file" type="file" size="45" />
		</div>
		<div class="w2gm-upload-option">
			<label><input type="checkbox" id="crop_image" value="1" /> <?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)', 'W2GM'); ?></label>
		</div>
		<div class="w2gm-upload-option">
			<input
				type="button"
				class="w2gm-btn w2gm-btn-primary"
				onclick="return w2gm_ajaxImageFileUploadToGallery(
					'browse_file',
					addImageDiv,
					jQuery('#crop_image').is(':checked'),
					'<?php echo admin_url('admin-ajax.php?action=w2gm_upload_image&post_id='.$listing->post->ID.'&_wpnonce='.wp_create_nonce('upload_images')); ?>',
					'<?php echo esc_js(__('Choose image to upload first!', 'W2GM')); ?>'
				);"
				value="<?php esc_attr_e('Upload image', 'W2GM'); ?>" />
		</div>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>