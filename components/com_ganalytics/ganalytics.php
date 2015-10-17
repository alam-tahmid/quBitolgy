<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('components.com_ganalytics.helper.ganalytics', JPATH_ADMINISTRATOR);

JFactory::getLanguage()->load('com_ganalytics', JPATH_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('GAnalytics');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();