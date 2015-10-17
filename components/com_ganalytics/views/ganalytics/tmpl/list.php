<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$profile = $this->profile;

if (empty($profile)) {
	return;
}

GAnalyticsHelper::loadjQuery();

$params = $this->params;
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
$document->addStyleSheet(JURI::base().'components/com_ganalytics/views/ganalytics/tmpl/ganalytics.css');
$document->addScript(JURI::base().'components/com_ganalytics/views/ganalytics/tmpl/ganalytics.js');

$showDateSelection = $params->get('showDateSelection', 'yes') == 'yes';

echo GAnalyticsHelper::render($this->titleFormat, array('accountname' => $profile->accountName, 'profilename' => $profile->profileName, 'username' => JFactory::getUser()->username));
?>
<?php if($showDateSelection) {?>
<table class="ganalytics-table">
	<tr>
		<td style="padding-right:20px">
		<?php
		echo JText::_('COM_GANALYTICS_CHART_VIEW_SELECT_DATE_FROM');
		echo JHtml::_('calendar', $this->startDate->format('Y-m-d'), 'date_from', 'date_from', '%Y-%m-%d', array('size' => 10));
		?>
		</td>
		<td style="padding-right:20px">
		<?php
		echo JText::_('COM_GANALYTICS_CHART_VIEW_SELECT_DATE_TO');
		echo JHtml::_('calendar', $this->endDate->format('Y-m-d'), 'date_to', 'date_to', '%Y-%m-%d', array('size' => 10));
		?>
		</td>
		<td style="padding-right:20px"><button onclick="showCharts();" class="btn"><?php echo JText::_('COM_GANALYTICS_CHART_VIEW_BUTTON_UPDATE');?></button></td>
	</tr>
</table>
<?php
}

$scriptCode = "gajQuery(document).ready(function(){\n";
if($params->get('showVisitors', 'yes') == 'yes'){?>
<fieldset class="date-range-container"><legend><?php echo GAnalyticsHelper::translate('ga:visits');?></legend>
	<div class="date-range-toolbar">
		<img src="media/com_ganalytics/images/dayrange/month-disabled-32.png" class="date-range-button date-range-month"
			alt="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_MONTH');?>"
			title="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_MONTH');?>" />
		<img src="media/com_ganalytics/images/dayrange/week-disabled-32.png" class="date-range-button date-range-week"
			alt="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_WEEK');?>"
			title="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_WEEK');?>" />
		<img src="media/com_ganalytics/images/dayrange/day-32.png" class="date-range-button date-range-day"
			alt="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_DAY');?>"
			title="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_DAY');?>" />
	</div>

	<div id="ga-visitors-chart-<?php echo $profile->id;?>" class="ga-chart"></div>
</fieldset>
	<?php
	$scriptCode .= "gajQuery('#ga-visitors-chart-".$profile->id."').gaChart({
		chartVisDivID: 'gaChartDiv".$profile->id."',
		url: 'index.php?option=com_ganalytics&view=data&source=component&format=raw&type=visitor&Itemid=".JFactory::getApplication()->getMenu()->getActive()->id."',
		start: '".$this->startDate->format('Y-m-d')."',
		end: '".$this->endDate->format('Y-m-d')."',
		pathPrefix: '".JURI::root()."'
	});\n";
}

?>
<fieldset class="date-range-container">
	<legend><?php
	$dims = array();
	foreach ($this->dimensions as $dim) {
		$dims[] = GAnalyticsHelper::translate($dim);
	}
	$metrs = array();
	foreach ($this->metrics as $metr) {
		$metrs[] = GAnalyticsHelper::translate($metr);
	}
	echo implode(' | ', $dims).' -- '.implode(' | ', $metrs);?>
	</legend>
	<?php
	if(in_array('ga:date', $this->dimensions) !== false){?>
	<div class="date-range-toolbar">
		<img src="media/com_ganalytics/images/dayrange/month-disabled-32.png" class="date-range-button date-range-month"
			alt="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_MONTH');?>"
			title="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_MONTH');?>" />
		<img src="media/com_ganalytics/images/dayrange/week-disabled-32.png" class="date-range-button date-range-week"
			alt="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_WEEK');?>"
			title="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_WEEK');?>" />
		<img src="media/com_ganalytics/images/dayrange/day-32.png" class="date-range-button date-range-day"
			alt="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_DAY');?>"
			title="<?php echo JText::_('COM_GANALYTICS_CHART_VIEW_IMAGE_DATE_RANGE_DAY');?>" />
	</div>
	<?php } ?>
	<div id="ga-main-chart-<?php echo $profile->id;?>" class="ga-chart"></div>
</fieldset>
<?php
$scriptCode .= "gajQuery('#ga-main-chart-".$profile->id."').gaChart({
	chartDivID: 'gaChartDiv".$profile->id."',
	url: 'index.php?option=com_ganalytics&view=data&source=component&format=raw&Itemid=".JFactory::getApplication()->getMenu()->getActive()->id."',
	start: '".$this->startDate->format('Y-m-d')."',
	end: '".$this->endDate->format('Y-m-d')."',
	pathPrefix: '".JURI::root()."'
});\n";

$scriptCode .= "showCharts();});\n";
$document->addScriptDeclaration($scriptCode);

if(!GAnalyticsHelper::isProMode()){
	echo "<div style=\"text-align:center;margin-top:10px\"><a href=\"http://joomla.digital-peak.com\" target=\"_blank\">GAnalytics</a></div>\n";
}