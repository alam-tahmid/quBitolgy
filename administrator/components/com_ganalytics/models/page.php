<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class GAnalyticsModelPage extends JModelList {

	public function getEntry() {
		$store = $this->getStoreId('getEntry');

		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$dimensions = array('ga:pageTitle', 'ga:pagePath');
		$metrics = array('ga:pageViews', 'ga:uniquePageViews', 'ga:timeOnPage', 'ga:entrances', 'ga:visitBounceRate', 'ga:exitRate', 'ga:totalValue', 'ga:avgPageLoadTime');
		$sort = array(($this->state->get('list.direction') == 'asc' ? '' : '-').$this->state->get('list.ordering'));

		$end = JFactory::getDate($this->state->get('filter.search_end'));
		$start = JFactory::getDate($this->state->get('filter.search_start'));

		$filter = 'ga:pagePath=='.base64_decode(JRequest::getVar('path', '', null, 'BASE64'));

		$entries = GAnalyticsDataHelper::getData($this->getProfile(), $dimensions, $metrics, $start, $end, $sort, $filter, 1);

		if(empty($entries)) {
			$entries = array();
		}
		$entry = null;

		foreach ($entries->getRows() as $tmp) {
			if ($tmp[0] != '(not set)') {
				$entry = $tmp;
				break;
			}
		}
		$this->cache[$store] = $entry;

		return $entry;
	}

	public function getDateStats() {
		return $this->getStats(array('ga:date'), array('ga:uniquePageViews', 'ga:pageViews'), array('ga:date'), 1000, 'getDateStats');
	}

	public function getSourceStats() {
		return $this->getStats(array('ga:medium'), array('ga:visits'), array('-ga:visits'), 5, 'getSourceStats');
	}

	public function getMobileStats() {
		return $this->getStats(array('ga:isMobile'), array('ga:visits'), array('-ga:visits'), 5, 'getMobileStats');
	}

	public function getLanguageStats() {
		return $this->getStats(array('ga:language'), array('ga:visits'), array('-ga:visits'), 5, 'getLanguageStats');
	}

	public function getReferralStats() {
		return $this->getStats(array('ga:source','ga:referralPath'), array('ga:visits'), array('-ga:visits'), 30, 'getReferralStats');
	}

	public function getProfile() {
		$profile = JTable::getInstance('Profile', 'GAnalyticsTable');
		$profile->load(JRequest::getInt('gaid', 0));
		if ($profile->id < 1) {
			$profiles = $this->getProfiles();
			if (empty($profiles)) {
				return array();
			}
			$profile->load($profiles[0]->id);
		}
		return $profile;
	}

	public function getProfiles() {
		$store = $this->getStoreId('getProfiles');

		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}
		$this->cache[$store] = $this->_getList('SELECT * FROM #__ganalytics_profiles');

		return $this->cache[$store];
	}

	protected function populateState($ordering = null, $direction = null) {
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		$date = JFactory::getDate();
		$search = $this->getUserStateFromRequest($this->context.'.filter.search_end', 'filter_search_end');
		$this->setState('filter.search_end', empty($search) ? $date->format('Y-m-d') : $search);
		$date->modify('-1 month');
		$search = $this->getUserStateFromRequest($this->context.'.filter.search_start', 'filter_search_start');
		$this->setState('filter.search_start', empty($search) ? $date->format('Y-m-d') : $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_ganalytics');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('ga:pageViews', 'desc');
	}

	protected function getStoreId($id = '') {
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}

	private function getStats($dimensions, $metrics, $sort, $max, $type) {
		$store = $this->getStoreId($type);

		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$start = JFactory::getDate($this->state->get('filter.search_start'));
		$end = JFactory::getDate($this->state->get('filter.search_end'));

		$filter = 'ga:pagePath=='.base64_decode(JRequest::getVar('path', '', null, 'BASE64'));

		$this->cache[$store] = GAnalyticsDataHelper::getData($this->getProfile(), $dimensions, $metrics, $start, $end, $sort, $filter, $max);

		return $this->cache[$store];
	}
}