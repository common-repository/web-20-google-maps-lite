(function($) {
	"use strict";
	
	var category_marker_icon_input, category_marker_icon_tag, category_marker_image_png_input;
	
	$(function() {
		var categories_markers_images_png_url = categories_icons.categories_markers_images_png_url;
		$(document).on("click", ".select_marker_png_image", function() {
			category_marker_image_png_input = $(this).parent().find('.marker_png_image');
			
			var dialog = $('<div id="select_marker_image_dialog"></div>').dialog({
				width: ($(window).width()*0.5),
				height: ($(window).height()*0.8),
				modal: true,
				resizable: false,
				draggable: false,
				title: categories_icons.ajax_dialog_title,
				open: function() {
					w2gm_ajax_loader_show();
					$.ajax({
						type: "POST",
						url: w2gm_js_objects.ajaxurl,
						data: {'action': 'w2gm_select_category_marker_png_image_dialog'},
						dataType: 'html',
						success: function(response_from_the_action_function){
							if (response_from_the_action_function != 0) {
								$('#select_marker_image_dialog').html(response_from_the_action_function);
								if (category_marker_image_png_input.val())
									$(".w2gm-png-image[icon_file='"+category_marker_image_png_input.val()+"']").addClass("w2gm-selected-icon");
							}
						},
						complete: function() {
							w2gm_ajax_loader_hide();
						}
					});
					$(document).on("click", ".ui-widget-overlay", function() { $('#select_marker_image_dialog').remove(); });
				},
				close: function() {
					$('#select_marker_image_dialog').remove();
				}
			});
		});
		$(document).on("click", ".w2gm-png-image", function() {
			$(".w2gm-selected-icon").removeClass("w2gm-selected-icon");
			var icon_file = $(this).attr('icon_file');
			w2gm_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2gm_js_objects.ajaxurl,
				data: {'action': 'w2gm_select_category_marker_png_image', 'image_name': icon_file, 'category_id': category_marker_image_png_input.parent().find(".category_id").val(), 'w2gm_marker_png_image_nonce': $("#w2gm_marker_png_image_nonce").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (response_from_the_action_function != 0) {
						if (category_marker_image_png_input) {
							category_marker_image_png_input.val(icon_file);
							category_marker_image_png_input.parent().find(".w2gm-marker-image-png-tag").attr('src', categories_markers_images_png_url+icon_file).show();
							category_marker_image_png_input = false;
						}
					}
				},
				complete: function() {
					$(this).addClass("w2gm-selected-icon");
					$('#select_marker_image_dialog').remove();
					w2gm_ajax_loader_hide();
				}
			});
		});
		$(document).on("click", "#reset_png_image", function() {
			$(".w2gm-selected-icon").removeClass("w2gm-selected-icon");
			w2gm_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2gm_js_objects.ajaxurl,
				data: {'action': 'w2gm_select_category_marker_png_image', 'category_id': category_marker_image_png_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (category_marker_image_png_input) {
						category_marker_image_png_input.val('');
						category_marker_image_png_input.parent().find(".w2gm-marker-image-png-tag").attr('src', '').hide();
						category_marker_image_png_input = false;
					}
				},
				complete: function() {
					$('#select_marker_image_dialog').remove();
					w2gm_ajax_loader_hide();
				}
			});
		});

		$(document).on("click", ".select_marker_icon_image", function() {
			category_marker_icon_input = $(this).parent().find('.marker_icon_image');
			category_marker_icon_tag = $(this).parent().find('.w2gm-marker-icon-tag');

			var dialog = $('<div id="select_marker_icon_dialog"></div>').dialog({
				width: ($(window).width()*0.5),
				height: ($(window).height()*0.8),
				modal: true,
				resizable: false,
				draggable: false,
				title: categories_icons.ajax_marker_dialog_title,
				open: function() {
					w2gm_ajax_loader_show();
					$.ajax({
						type: "POST",
						url: w2gm_js_objects.ajaxurl,
						data: {'action': 'w2gm_select_field_icon'},
						dataType: 'html',
						success: function(response_from_the_action_function){
							if (response_from_the_action_function != 0) {
								$('#select_marker_icon_dialog').html(response_from_the_action_function);
								if (category_marker_icon_input.val())
									$("#"+category_marker_icon_input.val()).addClass("w2gm-selected-icon");
							}
						},
						complete: function() {
							w2gm_ajax_loader_hide();
						}
					});
					$(document).on("click", ".ui-widget-overlay", function() { $('#select_marker_icon_dialog').remove(); });
				},
				close: function() {
					$('#select_marker_icon_dialog').remove();
				}
			});
		});
		$(document).on("click", ".w2gm-fa-icon", function() {
			$(".w2gm-selected-icon").removeClass("w2gm-selected-icon");
			category_marker_icon_input.val($(this).attr('id'));
			w2gm_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2gm_js_objects.ajaxurl,
				data: {'action': 'w2gm_select_category_marker_icon', 'icon_name': category_marker_icon_input.val(), 'category_id': category_marker_icon_input.parent().find(".category_id").val(), 'w2gm_marker_icon_image_nonce': $("#w2gm_marker_icon_image_nonce").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (response_from_the_action_function != 0) {
						if (category_marker_icon_input) {
							category_marker_icon_tag.removeClass().addClass('w2gm-marker-icon-tag w2gm-fa '+category_marker_icon_input.val());
							category_marker_icon_input = false;
						}
					}
				},
				complete: function() {
					$(this).addClass("w2gm-selected-icon");
					$('#select_marker_icon_dialog').remove();
					w2gm_ajax_loader_hide();
				}
			});
		});
		$(document).on("click", "#reset_fa_icon", function() {
			$(".w2gm-selected-icon").removeClass("w2gm-selected-icon");
			category_marker_icon_input.val('');
			w2gm_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2gm_js_objects.ajaxurl,
				data: {'action': 'w2gm_select_category_marker_icon', 'category_id': category_marker_icon_input.parent().find(".category_id").val()},
				dataType: 'html',
				success: function(response_from_the_action_function){
					if (category_marker_icon_input) {
						category_marker_icon_tag.removeClass().addClass('w2gm-marker-icon-tag');
						category_marker_icon_input = false;
					}
				},
				complete: function() {
					$('#select_marker_icon_dialog').remove();
					w2gm_ajax_loader_hide();
				}
			});
		});
		
		$(".marker_color").wpColorPicker();
		$(document).on('focus', '.marker_color', function(){
			var parent = $(this).parent();
            $(this).wpColorPicker()
            parent.find('.wp-color-result').click();
        }); 
		$(document).on("click", ".save_color", function() {
			var category_marker_color_input = $(this).parents(".w2gm-content").find(".marker_color");
			w2gm_ajax_loader_show();
			$.ajax({
				type: "POST",
				url: w2gm_js_objects.ajaxurl,
				data: {'action': 'w2gm_select_category_marker_color', 'color': category_marker_color_input.val(), 'category_id': category_marker_color_input.parents(".w2gm-content").find(".category_id").val(), 'w2gm_marker_color_nonce': $("#w2gm_marker_color_nonce").val()},
				dataType: 'html',
				complete: function() {
					w2gm_ajax_loader_hide();
				}
			});
		});
	});
})(jQuery);
