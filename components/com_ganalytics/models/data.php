<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class GAnalyticsModelData extends JModelLegacy {

	private $profile = null;

	public function getProfile() {
		if($this->profile == null) {
			$params = $this->getState('params');

			$profile = JTable::getInstance('Profile', 'GAnalyticsTable');
			if($params->get('accountids', 0) == 0) {
				JFactory::getDbo()->setQuery('select id from #__ganalytics_profiles', 0, 1);
				JFactory::getDbo()->query();
				$tmp = JFactory::getDbo()->loadObject('GAnalyticsTableProfile');
				if($tmp != null) {
					$profile->load($tmp->id);
				}
			} else {
				$profile->load($params->get('accountids', 0));
			}
			$this->profile = $profile;
		}
		return $this->profile;
	}

	public function getStatsData() {
		$params = $this->getState('params');

		$startDate = JFactory::getDate();
		$startDate->modify('-1 day');
		$endDate = JFactory::getDate();
		$endDate->modify('-1 day');
		if($params->get('daterange', 'month') == 'advanced') {
			$tmp = $params->get('advancedDateRange', null);
			if(!empty($tmp)) {
				$startDate = JFactory::getDate(strtotime($tmp));
			} else {
				$tmp = $params->get('startdate', null);
				if(!empty($tmp)) {
					$startDate = JFactory::getDate($tmp);
				}
				$tmp = $params->get('enddate', null);
				if(!empty($tmp)) {
					$endDate = JFactory::getDate($tmp);
				}
			}
		} else {
			$range = '';
			switch ($params->get('daterange', 'month')) {
				case 'day':
					$range = '-1 day';
					break;
				case 'week':
					$range = '-1 week';
					break;
				case 'month':
					$range = '-1 month';
					break;
				case 'year':
					$range = '-1 year';
					break;
			}
			$startDate = JFactory::getDate(strtotime($range));
		}

		$dimensions = array();
		$metrics = array();
		$sort = array();
		if($params->get('type', 'visitsbytraffic') == 'advanced') {
			$dimensions = $params->get('dimensions', array('ga:date'));
			$metrics = $params->get('metrics', array('ga:visits'));
			$sort = $params->get('sort', array());
		} else {
			switch ($params->get('type', 'visitsbytraffic')) {
				case 'visits':
					$dimensions[] = 'ga:date';
					$metrics[] = 'ga:visits';
					$metrics[] = 'ga:newVisits';
					$sort[] = 'ga:date';
					break;
				case 'visitsbytraffic':
					$dimensions[] = 'ga:source';
					$metrics[] = 'ga:visits';
					$metrics[] = 'ga:newVisits';
					$sort[] = '-ga:visits';
					break;
				case 'visitsbybrowser':
					$dimensions[] = 'ga:browser';
					$metrics[] = 'ga:visits';
					$metrics[] = 'ga:newVisits';
					$sort[] = '-ga:visits';
					break;
				case 'visitsbycountry':
					$dimensions[] = 'ga:country';
					$metrics[] = 'ga:visits';
					$sort[] = '-ga:visits';
					break;
				case 'timeonsite':
					$dimensions[] = 'ga:region';
					$metrics[] = 'ga:timeOnSite';
					$sort[] = '-ga:timeOnSite';
					break;
				case 'toppages':
					$dimensions[] = 'ga:pagePath';
					$metrics[] = 'ga:pageviews';
					$sort[] = '-ga:pageviews';
					break;
			}
		}
		$max = $params->get('max', 1000);

		if(JRequest::getVar('type', null) == 'visitor') {
			$dimensions = array('ga:date');
			$metrics = array('ga:newVisits','ga:visits');
			$sort = array('ga:date');
			$max = 1000;
		}

		if(JRequest::getVar('start-date', null) != null) {
			$startDate = JFactory::getDate(JRequest::getVar('start-date', null));
			$startDate->setTime(0, 0);
		}
		if(JRequest::getVar('end-date', null) != null) {
			$endDate = JFactory::getDate(JRequest::getVar('end-date', null));
			$endDate->setTime(0, 0);
		}

		$filter = null;
		if($params->get('filterType', '') == 'page') {
			$uri = JFactory::getURI();
			$filter = 'ga:pagePath=='.$uri->getPath().'?'.$uri->getQuery();
		}
		if($params->get('filterType', '') == 'advanced') {
			$filter = GAnalyticsHelper::render($params->get('filter', null), array('userId' => JFactory::getUser()->id,
					'username' => strtolower(JFactory::getUser()->username)));
		}

		return GAnalyticsDataHelper::getData($this->getProfile(), $dimensions, $metrics, $startDate, $endDate, $sort, $filter, $max);
	}
}