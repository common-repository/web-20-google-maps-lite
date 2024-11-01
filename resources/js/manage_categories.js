(function($) {
	"use strict";
	
	$(function() {
		$('ul.w2gm-categorychecklist li').each(function() {
			if ($(this).children('ul').length > 0) {
				$(this).addClass('parent');
				$(this).prepend('<span class="w2gm-category-parent"></span>');
				if ($(this).find('ul input[type="checkbox"]:checked').length > 0)
					$(this).find('.w2gm-category-parent').prepend('<span class="w2gm-category-has-checked"></span>');
			} else
				$(this).prepend('<span class="w2gm-category-empty"></span>');
		});
		$('ul.w2gm-categorychecklist li ul').each(function() {
			$(this).hide();
		});
		$('ul.w2gm-categorychecklist li.parent > .w2gm-category-parent').click(function() {
			$(this).parent().toggleClass('active');
			$(this).parent().children('ul').slideToggle('fast');
		});
		$('ul.w2gm-categorychecklist li input[type="checkbox"]').change(function() {
			$('ul.w2gm-categorychecklist li').each(function() {
				if ($(this).children('ul').length > 0) {
					if ($(this).find('ul input[type="checkbox"]:checked').length > 0) {
						if ($(this).find('.w2gm-category-parent .w2gm-category-has-checked').length == 0)
							$(this).find('.w2gm-category-parent').prepend('<span class="w2gm-category-has-checked"></span>');
					} else
							$(this).find('.w2gm-category-parent .w2gm-category-has-checked').remove();
				}
			});
		});
		
		$("input[name=tax_input\\[w2gm-category\\]\\[\\]]").change(function() {w2gm_manageCategories($(this))});
		$("#w2gm-category-pop input[type=checkbox]").change(function() {w2gm_manageCategories($(this))});
		
		function w2gm_manageCategories(checked_object) {
			if (checked_object.is(":checked") && categories_options.number != 'unlimited') {
				if ($("input[name=tax_input\\[w2gm-category\\]\\[\\]]:checked").length > categories_options.number) {
					alert(categories_options.notice_number);
					$("#in-w2gm-category-"+checked_object.val()).attr("checked", false);
					$("#in-popular-w2gm-category-"+checked_object.val()).attr("checked", false);
				}
			}
			return true;
		}
		
		$(".w2gm-expand-terms").click(function() {
			$('ul.w2gm-categorychecklist li.parent').each(function() {
				$(this).addClass('active');
				$(this).children('ul').slideDown('fast');
			});
		});
		$(".w2gm-collapse-terms").click(function() {
			$('ul.w2gm-categorychecklist li.parent').each(function() {
				$(this).removeClass('active');
				$(this).children('ul').slideUp('fast');
			});
		});
	});
})(jQuery);
