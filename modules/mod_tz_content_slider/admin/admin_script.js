/**
* @version 1.0.0
* @package Tz Module Widget
* @copyright (C) 2012 www.themezart.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

jQuery.noConflict();
jQuery(document).ready(function(fn){
	showhide();	
	// Apply jquery UI Radio element style
    // Turn radios into btn-group
    fn('#jform_showtitle').addClass('btn-group');

    fn('.radio.btn-group label').addClass('btn');
    fn(".btn-group label:not(.active)").click(function() {
        var label = fn(this);
        var input = fn('#' + label.attr('for'));

        if (!input.prop('checked')) {
            label.closest('.btn-group').find("label").removeClass('active btn-danger btn-primary');
            if(input.val()== '') {
                    label.addClass('active btn-primary');
             } else if(input.val()==0) {
                    label.addClass('active btn-danger');
             } else {
            label.addClass('active btn-primary');
             }
            input.prop('checked', true);
        }
    });

    fn(".btn-group input[checked=checked]").each(function() {
        if(fn(this).val()== '') {
           fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-primary');
        } else if(fn(this).val()==0) {
           fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-danger');
        } else {
            fn("label[for=" + fn(this).attr('id') + "]").addClass('active btn-primary');
        }
    });

    //Bootsrap button on Joomla2.5 toolbar button
    fn('#toolbar li a').addClass('btn');

    // Bootstrap button for position
    var pos = fn('#jform_position-lbl').closest('li');
    pos.addClass('input-append').find('a').addClass('btn');

    // Boostraped alert message
    fn('#system-message ul li').addClass('alert alert-info');

    //Chosen Multiple selector
    fn(".chzn-select").chosen();

    fn('.cs-list a').popover({
        placement : 'right'
    });	


    //remove label li and push it to previous element
    fn('div.remove-lbl').each(function(){
       var content = fn(this);
        //push it to previous li
        fn(this).closest('li')
            .prev()
            .append(content);
        //remove paren li
        fn(this).closest('li').next().remove();
    });
	
	
	//module spacific
	
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
			jQuery("#jform_params_k2catids-lbl,#jform_params_article_extra_fields").parent().css("display", "block");		
		} else {
			jQuery("#jform_params_catids").parent().css("display", "block");	
			jQuery("#jform_params_k2catids-lbl,#jform_params_article_extra_fields").parent().css("display", "none");		
		}
		
		//Virtuemart
		if (jQuery("#jform_params_content_source").val()=="vm") {
			jQuery(".vm,#jform_params_vmcat-lbl").parent().css("display", "block");
			jQuery("#jform_params_ordering,#jform_params_ordering_direction-lbl,#jform_params_k2catids-lbl,#jform_params_catids,#jform_params_user_id-lbl,#jform_params_show_featured-lbl").parent().css("display", "none");
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
    
