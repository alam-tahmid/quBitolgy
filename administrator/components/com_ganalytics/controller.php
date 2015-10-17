<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class GAnalyticsController extends JControllerLegacy
{

	public function display ($cachable = false, $urlparams = array())
	{
		JRequest::setVar('view', JRequest::getCmd('view', 'dashboard'));
		parent::display($cachable, $urlparams);
		$view = JRequest::getVar('view', 'dashboard');

		JSubMenuHelper::addEntry(JText::_('COM_GANALYTICS_SUBMENU_DASHBOARD'), 'index.php?option=com_ganalytics', $view == 'dashboard');
		JSubMenuHelper::addEntry(JText::_('COM_GANALYTICS_SUBMENU_PAGES'), 'index.php?option=com_ganalytics&view=pages', $view == 'pages');
		JSubMenuHelper::addEntry(JText::_('COM_GANALYTICS_SUBMENU_PROFILES'), 'index.php?option=com_ganalytics&view=profiles', $view == 'profiles');
		JSubMenuHelper::addEntry(JText::_('COM_GANALYTICS_SUBMENU_TOOLS'), 'index.php?option=com_ganalytics&view=tools', $view == 'tools');
		JSubMenuHelper::addEntry(JText::_('COM_GANALYTICS_SUBMENU_HELP'), 'index.php?option=com_ganalytics&view=help', $view == 'help');
		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration(
				'.icon-48-analytics {background-image: url(../media/com_ganalytics/images/48-analytics.png);background-repeat: no-repeat;}');

		if (GAnalyticsHelper::getComponentParameter('client-id') == null || GAnalyticsHelper::getComponentParameter('client-secret') == null)
		{
			JError::raiseNotice(0, JText::_('COM_GANALYTICS_WARNING_NO_CLIENT_ID'));
		}
	}

	public function import ()
	{
		JRequest::setVar('view', 'import');
		JRequest::setVar('hidemainmenu', 0);

		parent::display();
	}
}