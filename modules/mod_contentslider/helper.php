<?php
/*------------------------------------------------------------------------
# Content Slider - Version 1.0
# Copyright (C) 2009-2010 YouTechClub.Com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Author: YouTechClub.Com
# Websites: http://www.youtechclub.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.helper' );
if (! class_exists("modContentSliderHelper") ) { 
require_once (dirname(__FILE__) .DS. 'assets' .DS.'contentslider.php');

class modContentSliderHelper {
	var $module_name = '';
	function process($params, $module) {
		
		$enable_cache 		=   $params->get('cache',1);
		$cachetime			=   $params->get('cache_time',0);
		//$this->module_name = $module->module;
		
		if($enable_cache==1) {		
			$conf =& JFactory::getConfig();
			$cache = &JFactory::getCache($module->module);
			$cache->setLifeTime( $params->get( 'cache_time', $conf->getValue( 'config.cachetime' ) * 60 ) );
			$cache->setCaching(true);
			//$cache->setCacheValidation(true);
			$items =  $cache->get( array('modContentSliderHelper', 'getList'), array($params, $module));
		} else {
			$items = modContentSliderHelper::getList($params, $module);
		}
		
		return $items;		
		
	}
	
	
	function getList ($params, $module) {
		
        $content = new ContentSlider();

        $content->featured                                      = $params->get('featured',2);
        $content->limit                                         = $params->get('total', 5);     
        
        $content->sort_order_field                              = $params->get('sort_order_field', "created");
        $content->thumb_height                                  = $params->get('thumb_height', "150");
        $content->thumb_width                                   = $params->get('thumb_width', "120");        
		$content->small_thumb_height                            = $params->get('small_thumb_height', "150");
		$content->small_thumb_width                             = $params->get('small_thumb_width', "120");  
		
        $content->resize_folder                                 = JPATH_CACHE.DS. $module->module .DS."images";
        $content->url_to_resize                                 = JURI::base() . "cache/". $module->module ."/images/";
        $content->imagesource                                   = $params->get('imagesource', 1);
        $content->cropresizeimage                               = $params->get('cropresizeimage', 1);
		$content->sec_cat_list                              	= $params->get('sec_cat_list', 1);
		$items = $content->getList();
        	
		return $items;
	}
}
			
} 


function pr($obj, $flag = 0) {
	if (is_object($obj) || is_array($obj)) {
		print '<pre>';
		print_r($obj);
		if ($flag) {
			print '</pre>';
		} else {
			die;
		}
	} else {
		echo $obj;
		if (!$flag) {
			die;
		}
	}
}

?>

