<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

GAnalyticsHelper::loadjQuery();

echo $title;

$document = JFactory::getDocument();
$document->addScript((JBrowser::getInstance()->isSSLConnection() ? 'https' : 'http').'://www.google.com/jsapi');

$document->addScript(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/fancybox/jquery.easing-1.3.pack.js');
$document->addScript(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/fancybox/jquery.mousewheel-3.0.4.pack.js');
$document->addScript(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/fancybox/jquery.fancybox-1.3.4.pack.js');
$document->addStyleSheet(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/fancybox/jquery.fancybox-1.3.4.css');

if(GAnalyticsHelper::isPROMode()){
	$document->addScript(JURI::base().'administrator/components/com_ganalytics/libraries/jquery/ganalytics/chart.js');
}else{
	$document->addScript(JURI::base().'administrator/components/com_ganalytics/libraries/jquery/ganalytics/list.js');
}
$document->addStyleSheet(JURI::base().'modules/mod_ganalytics_stats/tmpl/ganalytics.css');
$document->addScript(JURI::base().'modules/mod_ganalytics_stats/tmpl/ganalytics.js');

$showDateSelection = $params->get('showDateSelection', 'yes') == 'yes';
?>
<?php if($showDateSelection) {?>
<div>
<table class="mod-ganalytics-table">
	<tr>
		<td style="padding-right:20px">
		<?php
		echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_SELECT_DATE_FROM');
		echo JHtml::_('calendar', $startDate->format('Y-m-d'), 'mod_date_from', 'mod_date_from_'.$module->id, '%Y-%m-%d', array('size' => 10));
		?>
		</td>
		<td style="padding-right:20px">
		<?php
		echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_SELECT_DATE_TO');
		echo JHtml::_('calendar', $endDate->format('Y-m-d'), 'mod_date_to', 'mod_date_to_'.$module->id, '%Y-%m-%d', array('size' => 10));
		?>
		</td>
		<td style="padding-right:20px"><button onclick="showModCharts();"><?php echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_BUTTON_UPDATE');?></button></td>
	</tr>
</table>
<?php
}
$scriptCode = "gajQuery(document).ready(function(){\n";
?>
<fieldset class="mod-date-range-container">
<legend><?php
	$dims = array();
	foreach ($dimensions as $dim) {
		$dims[] = GAnalyticsHelper::translate($dim);
	}
	$metrs = array();
	foreach ($metrics as $metr) {
		$metrs[] = GAnalyticsHelper::translate($metr);
	}
	echo implode(' | ', $dims).' -- '.implode(' | ', $metrs);?>
	</legend>
	<?php
	if(in_array('ga:date', $dimensions) !== false){?>
	<div class="mod-date-range-toolbar">
		<img src="media/com_ganalytics/images/dayrange/month-disabled-32.png" class="date-range-button date-range-month"
			alt="<?php echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_IMAGE_DATE_RANGE_MONTH');?>"
			title="<?php echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_IMAGE_DATE_RANGE_MONTH');?>" />
		<img src="media/com_ganalytics/images/dayrange/week-disabled-32.png" class="date-range-button date-range-week"
			alt="<?php echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_IMAGE_DATE_RANGE_WEEK');?>"
			title="<?php echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_IMAGE_DATE_RANGE_WEEK');?>" />
		<img src="media/com_ganalytics/images/dayrange/day-32.png" class="date-range-button date-range-day"
			alt="<?php echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_IMAGE_DATE_RANGE_DAY');?>"
			title="<?php echo JText::_('MOD_GANALYTICS_STATS_CHART_VIEW_IMAGE_DATE_RANGE_DAY');?>" />
	</div>
	<?php } ?>
	<div id="ga-mod-chart-<?php echo $module->id;?>" class="mod-ga-chart"></div>
</fieldset>
</div>
<?php
$scriptCode .= "gajQuery('#ga-mod-chart-".$module->id."').gaChart({
	chartDivID: 'gaChartDiv".$module->id."',
	url: 'index.php?option=com_ganalytics&source=module&view=data&format=raw&moduleid=".$module->id."',
	start: '".$startDate->format('Y-m-d')."',
	end: '".$endDate->format('Y-m-d')."',
	pathPrefix: '".JURI::root()."'
});\n";

$scriptCode .= "showModCharts();});\n";
$document->addScriptDeclaration($scriptCode);