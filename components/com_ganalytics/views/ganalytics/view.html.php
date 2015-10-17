<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class GAnalyticsViewGAnalytics extends JViewLegacy {

	public function display($tpl = null) {
		$mainframe = JFactory::getApplication();
		$params = $mainframe->getParams();

		$profile = $this->get('SelectedProfile');
		$this->profile = $profile;

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
		if($params->get('type', 'visits') == 'advanced') {
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

		if(JRequest::getVar('start-date', null) != null) {
			$startDate = JFactory::getDate(JRequest::getVar('start-date', null));
			$startDate->setTime(0, 0);
		}
		if(JRequest::getVar('end-date', null) != null) {
			$endDate = JFactory::getDate(JRequest::getVar('end-date', null));
			$endDate->setTime(0, 0);
		}

		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->dimensions = $dimensions;
		$this->metrics = $metrics;
		$this->sort = $sort;
		$this->max = $max;
		$this->titleFormat = $params->get('titleFormat', '<h3>{{accountname}} [{{profilename}}]</h3>');
		$this->dateFormat = $params->get('dateFormat', '%d.%m.%Y');
		$this->params = $params;

		$this->prepareDocument();

		parent::display($tpl);
	}

	protected function prepareDocument() {
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_DPCALENDAR_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description')) {
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots')) {
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}