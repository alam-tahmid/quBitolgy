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

JFactory::getLanguage()->load('com_ganalytics', JPATH_ADMINISTRATOR);

$profile = ModGAnalyticsStatsHelper::getSelectedProfile($params);
if (empty($profile))
	return;

$startDate = JFactory::getDate();
$startDate->modify('-1 day');
$endDate = JFactory::getDate();
$endDate->modify('-1 day');
if ($params->get('daterange', 'month') == 'advanced')
{
	$tmp = $params->get('advancedDateRange', null);
	if (! empty($tmp))
	{
		$startDate = JFactory::getDate(strtotime($tmp));
	}
	else
	{
		$tmp = $params->get('startdate', null);
		if (! empty($tmp))
		{
			$startDate = JFactory::getDate($tmp);
		}
		$tmp = $params->get('enddate', null);
		if (! empty($tmp))
		{
			$endDate = JFactory::getDate($tmp);
		}
	}
}
else
{
	$range = '';
	switch ($params->get('daterange', 'month'))
	{
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
if ($params->get('type', 'visits') == 'advanced')
{
	$dimensions = $params->get('dimensions', array(
			'ga:date'
	));
	$metrics = $params->get('metrics', array(
			'ga:visits'
	));
	$sort = $params->get('sort', array());
}
else
{
	switch ($params->get('type', 'visits'))
	{
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

$dateFormat = $params->get('dateFormat', '%d.%m.%Y');

$dims = array();
foreach ($dimensions as $dim)
{
	$dims[] = GAnalyticsHelper::translate($dim);
}
$metrs = array();
foreach ($metrics as $metr)
{
	$metrs[] = GAnalyticsHelper::translate($metr);
}
$title = GAnalyticsHelper::render($params->get('titleFormat', '<p>Dimension: {{dimensions}} <br/> Metric: {{metrics}}</p>'),
		array(
				'accountname' => $profile->accountName,
				'profilename' => $profile->profileName,
				'username' => JFactory::getUser()->username,
				'dimensions' => implode(' | ', $dims),
				'metrics' => implode(' | ', $metrs)
		));

$width = $params->get('width', '200px');
$height = $params->get('height', '200px');
$color = $params->get('color', null);

$mode = $params->get('mode', 'list');
if (! GAnalyticsHelper::isProMode())
{
	$mode = 'list';
}

require (JModuleHelper::getLayoutPath('mod_ganalytics_stats', $mode));