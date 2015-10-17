<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$entry = $this->entry;
if(empty($entry)) {
	echo 'No entry found!!';
	return;
}
GAnalyticsHelper::loadjQuery();

$document = JFactory::getDocument();
$document->addScript((JBrowser::getInstance()->isSSLConnection() ? 'https' : 'http').'://www.google.com/jsapi');

if (GAnalyticsHelper::isPROMode()) {
	$document->addScript(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/ganalytics/chart.js');
} else {
	$document->addScript(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/ganalytics/list.js');
}

$scriptCode = "gajQuery(document).ready(function() {gajQuery('#ga-date-chart').gaChart({
	chartDivID: 'gaChartDivDate',
	chartHeight: '220px',
	url: 'index.php?option=com_ganalytics&view=page&source=date&format=raw&layout=data&path=".JRequest::getVar('path', '', null, 'BASE64')."',
	start: '".$this->escape($this->state->get('filter.search_start'))."',
	end: '".$this->escape($this->state->get('filter.search_end'))."',
	colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
	colorEnd: 'EB8F33',
	pathPrefix: '".JURI::root()."'
});\n";
$scriptCode .= "gajQuery('#ga-date-chart').gaChart('refresh');\n";

$scriptCode .= "gajQuery('#ga-source-chart').gaChart({
	chartDivID: 'gaChartDivSearch',
	chartType: 'pie',
	chartHeight: '150px',
	url: 'index.php?option=com_ganalytics&view=page&source=source&format=raw&layout=data&path=".JRequest::getVar('path', '', null, 'BASE64')."',
	start: '".$this->escape($this->state->get('filter.search_start'))."',
	end: '".$this->escape($this->state->get('filter.search_end'))."',
	colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
	colorEnd: 'EB8F33',
	pathPrefix: '".JURI::root()."'
});\n";
$scriptCode .= "gajQuery('#ga-source-chart').gaChart('refresh');\n";

$scriptCode .= "gajQuery('#ga-mobile-chart').gaChart({
	chartDivID: 'gaChartDivMobile',
	chartHeight: '150px',
	url: 'index.php?option=com_ganalytics&view=page&source=mobile&format=raw&layout=data&path=".JRequest::getVar('path', '', null, 'BASE64')."',
	start: '".$this->escape($this->state->get('filter.search_start'))."',
	end: '".$this->escape($this->state->get('filter.search_end'))."',
	colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
	colorEnd: 'EB8F33',
	pathPrefix: '".JURI::root()."'
});\n";
$scriptCode .= "gajQuery('#ga-mobile-chart').gaChart('refresh');\n";

$scriptCode .= "gajQuery('#ga-language-chart').gaChart({
	chartDivID: 'gaChartDivLanguage',
	chartType: 'pie',
	chartHeight: '150px',
	url: 'index.php?option=com_ganalytics&view=page&source=language&format=raw&layout=data&path=".JRequest::getVar('path', '', null, 'BASE64')."',
	start: '".$this->escape($this->state->get('filter.search_start'))."',
	end: '".$this->escape($this->state->get('filter.search_end'))."',
	colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
	colorEnd: 'EB8F33',
	pathPrefix: '".JURI::root()."'
});\n";
$scriptCode .= "gajQuery('#ga-language-chart').gaChart('refresh');\n";

$scriptCode .= "gajQuery('#ga-referrer-chart').gaChart({
	chartDivID: 'gaChartDivReferrer',
	url: 'index.php?option=com_ganalytics&view=page&source=referrer&format=raw&layout=data&path=".JRequest::getVar('path', '', null, 'BASE64')."',
	start: '".$this->escape($this->state->get('filter.search_start'))."',
	end: '".$this->escape($this->state->get('filter.search_end'))."',
	colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
	colorEnd: 'EB8F33',
	pathPrefix: '".JURI::root()."'
});\n";
$scriptCode .= "gajQuery('#ga-referrer-chart').gaChart('refresh');\n";

$scriptCode .= "});\n";
$document->addScriptDeclaration($scriptCode);

$document->addStyleSheet(JURI::root().'administrator/components/com_ganalytics/views/page/tmpl/default.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_ganalytics&view=page'); ?>" method="post" name="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-left">
			<label class="element-invisible" for="filter_search_start"><?php echo JText::_('COM_DPCALENDAR_VIEW_EVENTS_START_DATE_AFTER_LABEL'); ?>:</label>
			<?php echo JHtml::_('calendar', $this->escape($this->state->get('filter.search_start')), 'filter_search_start', 'filter_search_start', '%Y-%m-%d', array('class' => 'inputbox', 'maxlength' => '10', 'size' => '10'));?>
		</div>
		<div class="btn-group pull-left">
			<label class="element-invisible" for="filter_search_end"><?php echo JText::_('COM_DPCALENDAR_VIEW_EVENTS_END_DATE_BEFORE_LABEL'); ?>:</label>
			<?php echo JHtml::_('calendar', $this->escape($this->state->get('filter.search_end')), 'filter_search_end', 'filter_search_end', '%Y-%m-%d', array('class' => 'inputbox', 'maxlength' => '10', 'size' => '10'));?>
		</div>
		<div class="btn-group pull-left">
			<button class="btn tip" type="submit" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button class="btn tip" type="button" onclick="document.id('filter_search').value='';document.id('filter_search_start').value='';document.id('filter_search_end').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
		</div>
	</div>
	<div class="clearfix"> </div>
	<?php echo '<h4>'.GAnalyticsHelper::translate('ga:uniquePageViews').' -- '.GAnalyticsHelper::translate('ga:pageViews').'</h4>';?>
	<div id="ga-date-chart"></div>
	<div class="clr"> </div>
	<table class="table table-striped">
		<tbody>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGE_VIEW_LABEL_TITLE'); ?>: </td>
				<td class="nowrap has-context"><?php echo GAnalyticsHelper::trim($entry[0]).' [ '.GAnalyticsHelper::trim($entry[1]).' ]'; ?></td>
			</tr>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_VIEWS'); ?>: </td>
				<td class="nowrap has-context"><?php echo $entry[2]; ?></td>
			</tr>
			<tr><td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_UNIQUE_VIEWS'); ?>: </td>
				<td class="nowrap has-context"><?php echo $entry[3]; ?></td>
			</tr>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_TIME_ON_PAGE'); ?>: </td>
				<td class="nowrap has-context"><?php echo round(($entry[4]/1000), 2).' s'; ?></td>
			</tr>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_ENTRANCE'); ?>: </td>
				<td class="nowrap has-context"><?php echo $entry[5]; ?></td>
			</tr>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_BOUNCE'); ?>: </td>
				<td class="nowrap has-context"><?php echo round($entry[6], 2).' %'; ?></td>
			</tr>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_EXIT'); ?>: </td>
				<td class="nowrap has-context"><?php echo round($entry[7], 2).' %'; ?></td>
			</tr>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_VALUE'); ?>: </td>
				<td class="nowrap has-context"><?php echo $entry[8]; ?></td>
			</tr>
			<tr>
				<td class="nowrap has-context"><?php echo JText::_('COM_GANALYTICS_PAGE_VIEW_SPEED'); ?>: </td>
				<td class="nowrap has-context"><?php echo round($entry[9], 2) .' s'; ?></td>
			</tr>
		</tbody>
	</table>
		<table id="charts-table" class="table table-striped">
	<?php if (GAnalyticsHelper::isPROMode()) {?>
			<thead><tr>
				<th><?php echo JText::_('COM_GANALYTICS_PAGE_VIEW_SOURCE')?></th>
				<th><?php echo JText::_('COM_GANALYTICS_PAGE_VIEW_MOBILE')?></th>
				<th><?php echo JText::_('COM_GANALYTICS_PAGE_VIEW_LANGUAGE')?></th>
			</tr></thead>
			<tr>
				<td><div id="ga-source-chart"></div></td>
				<td><div id="ga-mobile-chart"></div></td>
				<td><div id="ga-language-chart"></div></td>
			</tr>
	<?php }?>
			<tr>
				<td colspan="3"><div id="ga-referrer-chart"></div></td>
			</tr>
		</table>
	<div>
		<input type="hidden" name="gaid" value="<?php echo JRequest::getInt('gaid')?>" />
		<input type="hidden" name="tmpl" value="<?php echo JRequest::getWord('tmpl')?>" />
		<input type="hidden" name="path" value="<?php echo JRequest::getVar('path', '', null, 'BASE64')?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>