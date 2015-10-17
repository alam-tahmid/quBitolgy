<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JLoader::import('components.com_ganalytics.libraries.ganalytics.view', JPATH_ADMINISTRATOR);

class GAnalyticsViewDashboard extends GAnalyticsView
{

	protected function addToolBar ()
	{
		$canDo = GAnalyticsHelper::getActions();
		if ($canDo->get('core.create'))
		{
			JToolBarHelper::custom('dashboard.reset', 'purge.png', 'purge.png', JText::_('COM_GANALYTICS_PROFILES_VIEW_DASHBOARD_RESET_BUTTON'),
					false);
		}
		parent::addToolbar();
	}

	public function init ()
	{
		$this->statsViews = $this->get('StatsViews');
		$this->groups = $this->get('Groups');
		$this->profiles = $this->get('Profiles');

		$this->getModel()->refreshUpdateSite();
	}
}