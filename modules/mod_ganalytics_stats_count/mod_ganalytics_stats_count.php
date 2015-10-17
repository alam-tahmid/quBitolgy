<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ganalytics' . DS . 'helper' . DS . 'ganalytics.php');

require_once (dirname(__FILE__) . DS . 'helper.php');

$profile = ModGAnalyticsStatsCountHelper::getSelectedProfile($params);
if (empty($profile))
{
	return;
}

$startDate = JFactory::getDate($profile->startDate);
$endDate = JFactory::getDate();
$endDate->modify('-1 days');
if ($endDate < $startDate)
{
	$endDate = $startDate;
}
$yearData = null;
if ($params->get('selectTotalVisitors', 'yes') == 'yes' || $params->get('selectVisitorsDay', 'yes') == 'yes')
{
	$dimensions = array(
			'ga:year'
	);
	$metrics = array(
			'ga:visits'
	);
	$filter = null;
	if ($params->get('pageOnly', 0) == 1)
	{
		$uri = JFactory::getURI();
		$filter = 'ga:pagePath==' . $uri->getPath() . '?' . $uri->getQuery();
		$dimensions[] = 'ga:pagePath';
	}
	$yearData = GAnalyticsDataHelper::getData($profile, $dimensions, $metrics, $startDate, $endDate, null, $filter, 1000);
}
require (JModuleHelper::getLayoutPath('mod_ganalytics_stats_count'));