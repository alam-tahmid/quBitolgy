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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField
{
	protected	$type = 'Asset';
	
	protected function getInput() {
		$doc = JFactory::getDocument();	
		$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/elements/js/jquery.js');
		$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/elements/js/jquery.uniform.min.js');
		$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/elements/js/script.js');
		$doc->addStylesheet(JURI::root(true).'/modules/mod_tz_content_slider/elements/css/style.css');
		return null;
	}
} 
?>