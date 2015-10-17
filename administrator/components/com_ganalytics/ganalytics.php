<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if (!JFactory::getUser()->authorise('core.manage', 'com_ganalytics')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::import('components.com_ganalytics.helper.ganalytics', JPATH_ADMINISTRATOR);

$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ganalytics'.DS.'ganalytics.xml';
if(file_exists($path)) {
	$manifest = simplexml_load_file($path);
	JRequest::setVar('GANALYTICS_VERSION', (string)$manifest->version);
} else {
	JRequest::setVar('GANALYTICS_VERSION', '');
}

$controller = JControllerLegacy::getInstance('GAnalytics');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();