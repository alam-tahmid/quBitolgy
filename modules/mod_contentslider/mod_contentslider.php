<?php
/*------------------------------------------------------------------------
# Content Slider - Version 1.0
# Copyright (C) 2009-2010 YouTechClub.Com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Author: YouTechClub.Com
# Websites: http://www.youtechclub.com
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).DS.'helper.php');

jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");

/*-- Process---*/
$total                      = $params->get("total",1);
$featured 					= $params->get("featured", '1');
$sort_order_field           = $params->get("sort_order_field", 'created');
$thumb_width 				= $params->get("thumb_width", '800');
$thumb_height 				= $params->get("thumb_height", '350');
$cropresizeimage 			= $params->get("cropresizeimage", 1);
$auto_play 					= $params->get("auto_play", true);
$cache 						= $params->get("cache", '1');
$cache_time 				= $params->get("cache_time", '1');

$effect = $params->get('effect', 'random');
$slices = $params->get('slices', '15');
$animSpeed = $params->get('animSpeed', 500);
$pauseTime = $params->get('pauseTime', 3000);
$startSlide = $params->get('startSlide', 0);
$directionNav = $params->get('directionNav', 1);
$directionNavHide = $params->get('controlNav', 1);
$controlNav = $params->get('controlNav', 1);
$controlNavThumbs = $params->get('controlNavThumbs', 1);
$controlNavThumbsSearch = $params->get('controlNavThumbsSearch', '.jpg');
$controlNavThumbsReplace = $params->get('controlNavThumbsReplace', '_thumb.jpg');
$keyboardNav = $params->get('keyboardNav', 1);
$pauseOnHover = $params->get('pauseOnHover', 1);
$manualAdvance = $params->get('manualAdvance', 0);
$captionOpacity = $params->get('captionOpacity', '0.8');
$show_title = $params->get('show_title', 1);		
		
$items = modContentSliderHelper::process($params, $module);
//pr($items);

JHTML::script('jquery.min.js', JURI::base() . '/modules/'.$module->module.'/assets/');		
JHTML::script('noconflict.js', JURI::base() . '/modules/'.$module->module.'/assets/');
JHTML::script('jquery.nivo.slider.js', JURI::base() . '/modules/'.$module->module.'/assets/');		
JHTML::stylesheet('style.css', JURI::base() . '/modules/'.$module->module.'/assets/');

$path = JModuleHelper::getLayoutPath( 'mod_contentslider');
if (file_exists($path)) {
	require($path);
}
?>
