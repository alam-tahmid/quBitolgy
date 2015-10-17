<?php
/**
* @version 1.0.0
* @package Tz Module Widget
* @copyright (C) 2012 www.themezart.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*
**/

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldUtility extends JFormField{

    protected  $type = 'Utility';

    protected function getInput(){

        //if ( version_compare( JVERSION, '3.0', '<' ) == 1) {  
			$doc = JFactory::getDocument();

			//load all CSS first
			$doc->addStyleSheet(JURI::root(true).'/modules/mod_tz_content_slider/admin/assets/css/jquery-ui-1.8.16.custom.css');
			$doc->addStyleSheet(JURI::root(true).'/modules/mod_tz_content_slider/admin/assets/css/bootstrap.css');

			//load jquery and plugins
			$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/admin/assets/js/jquery-1.8.2.min.js');
			$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/admin/assets/js/jquery-ui-1.8.16.custom.min.js');
			$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/admin/assets/js/bootstrap.js');
			$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/admin/assets/js/bootstrap-modal.js');
			$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/admin/assets/js/chosen.jquery.min.js');

			//load admin script
			$doc->addScript(JURI::root(true).'/modules/mod_tz_content_slider/admin/admin_script.js');

		//}
    }

    protected function getLabel(){
        return '';
    }
}


