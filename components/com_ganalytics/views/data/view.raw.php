<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class GAnalyticsViewData extends JViewLegacy {

	public function display($tpl = null) {
		$user = JFactory::getUser();
		$access = 0;
		$params = null;

		if(JRequest::getVar('source', 'component') == 'component') {
			$menu = JFactory::getApplication()->getMenu()->getItem(JRequest::getInt('Itemid'));
			$params = $menu->params;
			$params->set('mode', $menu->query['layout'] == 'image' ? 'image' : 'list');
			$params->set('filterType', 'advanced');
			$access = $menu->access;
		}
		if(JRequest::getVar('source', 'component') == 'module') {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('m.*');
			$query->from('#__modules AS m');
			$query->where('id = '.JRequest::getInt('moduleid'));
			$db->setQuery($query);
			$module = $db->loadObject();

			if($module != null) {
				$params = new JRegistry($module->params);
				$params->set('mode', $params->get('mode', GAnalyticsHelper::isPROMode() ? 'image' : 'list'));

				if($module->module == 'mod_ganalytics_admin_stats') {
					//default it to a different
					$params->set('type', $params->get('type', 'visits'));
				}

				$access = $module->access;
			}
		}
		if($user->authorise('core.admin') || in_array((int) $access, $user->getAuthorisedViewLevels())) {
			$this->getModel()->setState('params', $params);
			$this->params = $params;
			$this->data = $this->get('StatsData');
			$this->profile = $this->get('Profile');
		} else {
			$this->params = $params;
			$this->data = null;
			$this->profile = null;
			JError::raiseWarning(0, 'JERROR_ALERTNOAUTHOR');
		}
		parent::display($tpl);
	}
}