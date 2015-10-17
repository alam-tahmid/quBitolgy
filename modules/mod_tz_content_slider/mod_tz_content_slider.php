<?php
/*------------------------------------------------------------------------
# TZ Content Slider 
# ------------------------------------------------------------------------
# Author    ThemeZart http://www.ThemeZart.com
# Copyright (C) 2010 - 2012 ThemeZart.com. All Rights Reserved.
# @license - GNU/GPL V2 for PHP files. CSS / JS are Copyrighted Commercial
# Websites: http://www.ThemeZart.com
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);

JHtml::_('behavior.framework', true);
//Basic
$moduleclass_sfx 					= $params->get('moduleclass_sfx');
$uniqid								= ($params->get('uniqid')=="") ? $module->id : $params->get('uniqid');
$content_source						= $params->get('content_source');
//Article Layout
$article_column						= $params->get('article_column');
$article_row						= $params->get('article_row');
$article_col_padding				= $params->get('article_col_padding');
$article_showtitle					= $params->get('article_showtitle');
$article_linkedtitle				= $params->get('article_linkedtitle');
$article_title_text_limit			= $params->get('article_title_text_limit');
$article_count_title_text			= $params->get('article_count_title_text');
$article_introtext					= $params->get('article_introtext');
$article_intro_text_limit			= $params->get('article_intro_text_limit');
$article_count_intro_text			= $params->get('article_count_intro_text');
$article_date_format				= $params->get('article_date_format');
$article_show_author				= $params->get('article_show_author');
$article_show_category				= $params->get('article_show_category');
$article_linked_category			= $params->get('article_linked_category');
$article_show_image					= $params->get('article_show_image');
$article_linked_image				= $params->get('article_linked_image');
$article_image_pos					= $params->get('article_image_pos');
$article_image_float				= $params->get('article_image_float');			
$article_image_margin				= $params->get('article_image_margin');
$article_thumb_width				= $params->get('article_thumb_width');
$article_thumb_height				= $params->get('article_thumb_height');
$article_thumb_ratio				= $params->get('article_thumb_ratio');
$article_extra_fields				= $params->get('article_extra_fields');
$article_show_more					= $params->get('article_show_more');
$article_more_text					= $params->get('article_more_text');
$article_comments					= $params->get('article_comments');
$article_hits						= $params->get('article_hits');
$article_show_ratings				= $params->get('article_show_ratings');
$article_animation					= $params->get('article_animation');
$article_slide_count				= $params->get('article_slide_count');
$article_pagination					= $params->get('article_pagination');
$article_arrows						= $params->get('article_arrows');
$article_autoplay					= $params->get('article_autoplay');
$article_play_button				= $params->get('article_play_button');
$article_activator					= $params->get('article_activator');
$article_animation_speed			= $params->get('article_animation_speed');
$article_animation_interval			= $params->get('article_animation_interval');
$article_animation_transition		= $params->get('article_animation_transition');
//Links Layout
$links_block						= $params->get('links_block');
$links_count						= $params->get('links_count');
$links_col_padding					= $params->get('links_col_padding');
$links_position						= $params->get('links_position');
$links_more							= $params->get('links_more');
$links_more_text					= $params->get('links_more_text');
$links_title_text_limit				= $params->get('links_title_text_limit');
$links_title_count					= $params->get('links_title_count');
$links_show_intro					= $params->get('links_show_intro');
$links_intro_text_limit				= $params->get('links_intro_text_limit');
$links_intro_count					= $params->get('links_intro_count');
$links_show_image					= $params->get('links_show_image');
$links_linked_image					= $params->get('links_linked_image');
$links_image_pos					= $params->get('links_image_pos');
$links_image_float					= $params->get('links_image_float');
$links_image_margin					= $params->get('links_image_margin');
$links_thumb_width					= $params->get('links_thumb_width');
$links_thumb_height					= $params->get('links_thumb_height');
$links_thumb_ratio					= $params->get('links_thumb_ratio');
$links_animation					= $params->get('links_animation');
$links_slide_count					= $params->get('links_slide_count');
$links_pagination					= $params->get('links_pagination');
$links_arrows						= $params->get('links_arrows');
$links_autoplay						= $params->get('links_autoplay');
$links_play_button					= $params->get('links_play_button');
$links_activator					= $params->get('links_activator');
$links_animation_speed				= $params->get('links_animation_speed');
$links_animation_interval			= $params->get('links_animation_interval');
$links_animation_transition			= $params->get('links_animation_transition');

$min_module_height			= $params->get('min_module_height');

//Virtuemart
$art_show_price 					= $params->get('art_show_price');
$links_show_price 					= $params->get('links_show_price');
$art_show_cart_button 				= $params->get('art_show_cart_button');
$links_show_cart_button 			= $params->get('links_show_cart_button');
	
//Calculated count	
if ($article_animation!="disabled") {
	$c_article_count				= $article_column*$article_row*$article_slide_count;
} else {
	$c_article_count				= $article_column*$article_row;
}

if ($links_block) {
	if ($links_animation!="disabled") {
		$c_links_count					= $links_count*$links_slide_count;
	} else {
		$c_links_count					= $links_count;
	}
} else {
	$c_links_count						= 0;
}

$c_count 							= $c_article_count + $c_links_count;

if (!class_exists("modTzContentSliderCommonHelper")) require_once('common.php');

if ($content_source=="joomla") {
	require_once (dirname(__FILE__).DS.'helper.php');
	$list 		= modTzContentSliderJHelper::getList($params, $c_count);
} elseif ($content_source=="vm") {
	if (!class_exists( 'VmModel' )) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmmodel.php');
	require_once (dirname(__FILE__).DS.'vmhelper.php');
	$list 		= modTzContentSliderVMHelper::getList($params, $c_count);	
} else {
	require_once (dirname(__FILE__).DS.'k2helper.php');
	$list 							= modTzContentSliderK2Helper::getList($params, $c_count);
}

$a_count 							= count($list);//actual count

if ($c_count>$a_count) {
	$c_count						= $a_count;
	if ($c_article_count>=$c_count) {
		$c_article_count			= $c_count;
		$c_links_count				= 0;
	} else {
		if ($c_links_count>$c_count-$c_article_count) {
			$c_links_count			= $c_count-$c_article_count;
		}	
	}
}

if (($content_source=="vm") && ($art_show_cart_button || $links_show_cart_button)) {
	vmJsApi::jQuery();
	vmJsApi::jPrice();
	vmJsApi::cssSite();
}

$doc 								= JFactory::getDocument();
$doc->addStylesheet(JURI::base(true) . '/modules/mod_tz_content_slider/assets/css/tzslider.css');
if ($article_animation!="disabled" || ($links_block && $c_links_count!=0 && $links_animation!="disabled")) {
	$doc->addScript(JURI::base(true) . '/modules/mod_tz_content_slider/assets/js/tzslider.js');
}

require JModuleHelper::getLayoutPath('mod_tz_content_slider', $params->get('layout', 'default'));