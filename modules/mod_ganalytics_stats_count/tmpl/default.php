<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_ganalytics/views/ganalytics/tmpl/ganalytics.css');

$output = "<table class=\"ganalytics-table\">\n";
if($params->get('selectTotalVisitors', 'yes') == 'yes' && $yearData !== null) {
	foreach ($yearData->getTotalsForAllResults() as $metricName => $metricTotal) {
		if($metricName == 'ga:visits') {
			$output .=  "<tr><td>".JText::_('MOD_GANALYTICS_STATS_COUNT_TOTAL_VISITORS').":</td><td>".$metricTotal."</td></tr>\n";
		}
	}
}
if($params->get('selectVisitorsDay', 'yes') == 'yes' && $yearData !== null) {
	$sum = 0;
	foreach ($yearData->getTotalsForAllResults() as $metricName => $metricTotal) {
		if($metricName == 'ga:visits') {
			$sum .=  $metricTotal;
		}
	}
	$days = ($endDate->format('U') - $startDate->format('U')) / 86400;
	if($days == 0) $days = 1;
	$output .=  "<tr><td>".JText::_('MOD_GANALYTICS_STATS_COUNT_VISITORS_A_DAY').":</td><td align=\"right\">".ceil($sum / $days)."</td></tr>\n";
}
$output .= "</table>\n";
echo $output;