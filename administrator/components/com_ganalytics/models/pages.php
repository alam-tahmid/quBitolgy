<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class GAnalyticsModelPages extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'pageTitle', 'ga:pageTitle',
					'pageViews', 'ga:pageViews',
					'uniquePageViews', 'ga:uniquePageViews',
					'timeOnPage', 'ga:timeOnPage',
					'entrances', 'ga:entrances',
					'visitBounceRate', 'ga:visitBounceRate',
					'exitRate', 'ga:exitRate',
					'totalValue', 'ga:totalValue'
			);
		}

		parent::__construct($config);
	}

	public function getEntries() {
		$store = $this->getStoreId('getEntries');

		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$dimensions = array('ga:pageTitle', 'ga:pagePath');
		$metrics = array('ga:pageViews', 'ga:uniquePageViews', 'ga:timeOnPage', 'ga:entrances', 'ga:visitBounceRate', 'ga:exitRate', 'ga:totalValue');
		$sort = array(($this->state->get('list.direction') == 'asc' ? '' : '-').$this->state->get('list.ordering'));

		$date = JFactory::getDate();
		$end = JFactory::getDate($this->state->get('filter.search_end'));

		$date->modify('-1 month');
		$start = JFactory::getDate($this->state->get('filter.search_start'));

		$filter = $this->state->get('filter.search');
		if(!empty($filter)) {
			$filter = 'ga:pageTitle=@'.$filter.',ga:pagePath=@'.$filter;
		} else {
			$filter = null;
		}
		$entries = GAnalyticsDataHelper::getData($this->getProfile(), $dimensions, $metrics, $start, $end, $sort, $filter, $this->getState('list.limit'), $this->getState('list.start') + 1);
		$this->cache[$store] = $entries;

		return $entries;
	}

	public function getDateStats() {
		return $this->getStats(array('ga:date'), array('ga:uniquePageViews', 'ga:pageViews'), array('ga:date'), 1000, 'getDateStats');
	}

	public function getBrowserStats() {
		return $this->getStats(array('ga:browser'), array('ga:uniquePageViews', 'ga:pageViews'), array('-ga:pageViews'), 5, 'getBrowserStats');
	}

	public function getCountryStats() {
		return $this->getStats(array('ga:country'), array('ga:pageViews'), array('-ga:pageViews'), 1000, 'getCountryStats');
	}

	public function getTotal() {
		$store = $this->getStoreId('getTotal');

		if (isset($this->cache[$store])) {
			return $this->cache[$store];
		}

		$results = $this->getEntries();
		$total = 0;
		if(!empty($results)) {
			$total = $results->getTotalResults();
		}
		$this->cache[$store] = $total;
		return $total;
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

		$filter = $this->state->get('filter.search');
		if(!empty($filter)) {
			$filter = 'ga:pageTitle=@'.$filter.',ga:pagePath=@'.$filter;
		} else {
			$filter = null;
		}
		$this->cache[$store] = GAnalyticsDataHelper::getData($this->getProfile(), $dimensions, $metrics, $start, $end, $sort, $filter, $max);

		return $this->cache[$store];
	}
}