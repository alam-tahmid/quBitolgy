/*------------------------------------------------------------------------
# TZ Content Slider
# ------------------------------------------------------------------------
# Author    ThemeZart http://www.ThemeZart.com
# Copyright (C) 2010 - 2012 ThemeZart.com. All Rights Reserved.
# @license - GNU/GPL V2 for PHP files. CSS / JS are Copyrighted Commercial
# Websites: http://www.ThemeZart.com
-------------------------------------------------------------------------*/
jQuery.noConflict();
jQuery(document).ready(function(){
	showhide();	
	jQuery("#jform_params_article_count_title_text,#jform_params_article_count_intro_text,#jform_params_article_more_text,#jform_params_article_image_float,#jform_params_links_title_count,#jform_params_links_intro_count,#jform_params_links_more_text,#jform_params_links_image_float").parent().css("display", "none");
	jQuery('#jform_params_article_count_title_text').insertBefore(jQuery('#jform_params_article_title_text_limit'));
	jQuery('#jform_params_article_count_intro_text').insertBefore(jQuery('#jform_params_article_intro_text_limit'));
	jQuery('#jform_params_article_image_float').insertAfter(jQuery('#jform_params_article_image_pos'));
	jQuery('#jform_params_article_more_text').insertBefore(jQuery('#jform_params_article_show_more'));
	jQuery('#jform_params_links_title_count').insertBefore(jQuery('#jform_params_links_title_text_limit'));
	jQuery('#jform_params_links_intro_count').insertBefore(jQuery('#jform_params_links_intro_text_limit'));
	jQuery('#jform_params_links_more_text').insertBefore(jQuery('#jform_params_links_more'));
	jQuery('#jform_params_links_image_float').insertAfter(jQuery('#jform_params_links_image_pos'));
	jQuery('#jform_params_content_source,#jform_params_article_animation,#jform_params_links_animation').change(function() {showhide()});
	jQuery('#jform_params_content_source,#jform_params_article_animation,#jform_params_links_animation').blur(function() {showhide()});
	function showhide(){
		if (jQuery("#jform_params_content_source").val()=="k2") {
			jQuery("#jform_params_catids").parent().css("display", "none");
			jQuery("#jformparamsk2catids,#jform_params_article_extra_fields").parent().css("display", "block");		
		} else {
			jQuery("#jform_params_catids").parent().css("display", "block");	
			jQuery("#jformparamsk2catids,#jform_params_article_extra_fields").parent().css("display", "none");		
		}
		
		//Virtuemart
		if (jQuery("#jform_params_content_source").val()=="vm") {
			jQuery(".vm,#jform_params_vmcat-lbl").parent().css("display", "block");
			jQuery("#jform_params_ordering,#jform_params_ordering_direction-lbl,#jformparamsk2catids,#jform_params_catids,#jform_params_user_id-lbl,#jform_params_show_featured-lbl").parent().css("display", "none");
		} else {
			jQuery(".vm,#jform_params_vmcat-lbl").parent().css("display", "none");
			jQuery("#jform_params_ordering,#jform_params_ordering_direction-lbl,#jform_params_user_id-lbl,#jform_params_show_featured-lbl").parent().css("display", "block");
		}
		
		//block1 animation
		if (jQuery("#jform_params_article_animation").val()=="disabled") {
			jQuery(".ani1").parent().css("display", "none");
		} else {
			jQuery(".ani1").parent().css("display", "block");
		}

		if (jQuery("#jform_params_links_animation").val()=="disabled") {
			jQuery(".ani2").parent().css("display", "none");
		} else {
			jQuery(".ani2").parent().css("display", "block");
		}
		
		jQuery('.pane-slider').css("height", "auto");
	}
	var empty = jQuery('#jform_params___field1-lbl');
	if (empty) empty.parent().remove();
});