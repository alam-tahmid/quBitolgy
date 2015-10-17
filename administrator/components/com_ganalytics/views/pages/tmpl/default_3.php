<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if(!$this->entries){
	return;
}

GAnalyticsHelper::loadjQuery();

$document = JFactory::getDocument();
$document->addScript((JBrowser::getInstance()->isSSLConnection() ? 'https' : 'http').'://www.google.com/jsapi');

$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.easing-1.3.pack.js');
$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.mousewheel-3.0.4.pack.js');
$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.fancybox-1.3.4.pack.js');
$document->addStyleSheet(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.fancybox-1.3.4.css');

if (GAnalyticsHelper::isPROMode()) {
	$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/ganalytics/chart.js');

	$scriptCode = "gajQuery(document).ready(function() {gajQuery('#ga-date-chart').gaChart({
		chartDivID: 'gaChartDivDate',
		url: 'index.php?option=com_ganalytics&view=pages&source=date&format=raw&layout=data',
		start: '".$this->escape($this->state->get('filter.search_start'))."',
		end: '".$this->escape($this->state->get('filter.search_end'))."',
		colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
		colorEnd: 'EB8F33',
		pathPrefix: '".JURI::root()."'
	});\n";
	$scriptCode .= "gajQuery('#ga-date-chart').gaChart('refresh');\n";

	$scriptCode .= "gajQuery('#ga-browser-chart').gaChart({
		chartDivID: 'gaChartDivBrowser',
		chartType: 'pie',
		chartHeight: '150px',
		url: 'index.php?option=com_ganalytics&view=pages&source=browser&format=raw&layout=data',
		start: '".$this->escape($this->state->get('filter.search_start'))."',
		end: '".$this->escape($this->state->get('filter.search_end'))."',
		colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
		colorEnd: 'EB8F33',
		pathPrefix: '".JURI::root()."'
	});\n";
	$scriptCode .= "gajQuery('#ga-browser-chart').gaChart('refresh');\n";

	$scriptCode .= "gajQuery('#ga-country-chart').gaChart({
		chartDivID: 'gaChartDivCountry',
		chartHeight: '150px',
		url: 'index.php?option=com_ganalytics&view=pages&source=country&format=raw&layout=data',
		start: '".$this->escape($this->state->get('filter.search_start'))."',
		end: '".$this->escape($this->state->get('filter.search_end'))."',
		colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 20)."',
		colorEnd: 'EB8F33',
		pathPrefix: '".JURI::root()."'
	});\n";
	$scriptCode .= "gajQuery('#ga-country-chart').gaChart('refresh');\n";

	$scriptCode .= "});\n";
	$document->addScriptDeclaration($scriptCode);
}
$document->addScript(JURI::base().'components/com_ganalytics/views/pages/tmpl/default.js');
$document->addStyleSheet(JURI::base().'components/com_ganalytics/views/pages/tmpl/default.css');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>


<form action="<?php echo JRoute::_('index.php?option=com_ganalytics&view=pages'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-left">
			<label class="element-invisible" for="gaid"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_SELECT_PROFILE'); ?> </label>
			<select name="gaid">
			<?php
			$selected = false;
			foreach ($this->profiles as $profile) {
				if(!$selected) $selected = true;
				echo "	<option ".($selected?'':"selected=\"true\"")." value=\"".$profile->id."\">".$profile->profileName."</option>\n";
			}
			?>
			</select>
		</div>
		<div class="filter-search btn-group pull-left">
			<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_DPCALENDAR_SEARCH_IN_TITLE'); ?>" />
		</div>
		<div class="btn-group pull-left">
			<label class="element-invisible" for="filter_search_start"><?php echo JText::_('COM_DPCALENDAR_VIEW_EVENTS_START_DATE_AFTER_LABEL'); ?>:</label>
			<?php echo JHtml::_('calendar', $this->escape($this->state->get('filter.search_start')), 'filter_search_start', 'filter_search_start', '%Y-%m-%d', array('class' => 'inputbox', 'maxlength' => '10', 'size' => '10'));?>
		</div>
		<div class="btn-group pull-left">
			<label class="element-invisible" for="filter_search_end"><?php echo JText::_('COM_DPCALENDAR_VIEW_EVENTS_END_DATE_BEFORE_LABEL'); ?>:</label>
			<?php echo JHtml::_('calendar', $this->escape($this->state->get('filter.search_end')), 'filter_search_end', 'filter_search_end', '%Y-%m-%d', array('class' => 'inputbox', 'maxlength' => '10', 'size' => '10'));?>
		</div>
		<div class="btn-group pull-left hidden-phone">
			<button class="btn tip" type="submit"rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button class="btn tip" type="button" onclick="document.id('filter_search').value='';document.id('filter_search_start').value='';document.id('filter_search_end').value='';this.form.submit();" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
		</div>
	</div>
	<div class="clearfix"> </div>
	<?php
	if (GAnalyticsHelper::isPROMode()) {
		echo '<h4>'.GAnalyticsHelper::translate('ga:uniquePageViews').' -- '.GAnalyticsHelper::translate('ga:pageViews').'</h4>';?>
		<div class="row-fluid">
			<div class="span6">
				<div id="ga-date-chart"></div>
			</div>
			<div class="span6">
				<div id="ga-browser-chart"></div>
				<div id="ga-country-chart"></div>
			</div>
		</div>
		<hr/><br>
	<?php }?>
	<div class="clr"> </div>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_TITLE', 'ga:pageTitle', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_VIEWS', 'ga:pageViews', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_UNIQUE_VIEWS', 'ga:uniquePageViews', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_TIME_ON_PAGE', 'ga:timeOnPage', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_ENTRANCE', 'ga:entrances', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_BOUNCE', 'ga:visitBounceRate', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_EXIT', 'ga:exitRate', $listDirn, $listOrder); ?></th>
			<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_VALUE', 'ga:totalValue', $listDirn, $listOrder); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->entries->getRows() as $i => $entry) {?>
		<tr class="row<?php echo $i % 2; ?>">
			<td width="50%" class="nowrap has-context">
				<a href="<?php echo JRoute::_('index.php?option=com_ganalytics&view=page&tmpl=component&path='.base64_encode($entry[1]).'&gaid='.JRequest::getInt('gaid')); ?>">
					<?php echo GAnalyticsHelper::trim($entry[0]).' [ '.GAnalyticsHelper::trim($entry[1]).' ]'; ?>
				</a>
			</td>
			<td class="nowrap has-context"><?php echo $entry[2]; ?></td>
			<td class="nowrap has-context"><?php echo $entry[3]; ?></td>
			<td class="nowrap has-context"><?php echo round(($entry[4]/1000), 2).' s'; ?></td>
			<td class="nowrap has-context"><?php echo $entry[5]; ?></td>
			<td class="nowrap has-context"><?php echo round($entry[6], 2).' %'; ?></td>
			<td class="nowrap has-context"><?php echo round($entry[7], 2).' %'; ?></td>
			<td class="nowrap has-context"><?php echo $entry[8]; ?></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>

<?php return;?>

<form action="<?php echo JRoute::_('index.php?option=com_ganalytics&view=pages'); ?>" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="profile"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_SELECT_PROFILE'); ?> </label>
			<select name="gaid">
			<?php
			$selected = false;
			foreach ($this->profiles as $profile) {
				if(!$selected) $selected = true;
				echo "	<option ".($selected?'':"selected=\"true\"")." value=\"".$profile->id."\">".$profile->profileName."</option>\n";
			}
			?>
			</select>
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_SEARCH_IN_TITLE'); ?>" />

			<label class="filter-search-lbl" for="filter_search_start"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_DATE_START'); ?>:</label>
			<?php echo JHtml::_('calendar', $this->escape($this->state->get('filter.search_start')), 'filter_search_start', 'filter_search_start', '%Y-%m-%d', array('class' => 'inputbox', 'maxlength' => '10', 'size' => '10'));?>

			<label class="filter-search-lbl" for="filter_search_end"><?php echo JText::_('COM_GANALYTICS_PAGES_VIEW_DATE_END'); ?>:</label>
			<?php echo JHtml::_('calendar', $this->escape($this->state->get('filter.search_end')), 'filter_search_end', 'filter_search_end', '%Y-%m-%d', array('class' => 'inputbox', 'maxlength' => '10', 'size' => '10'));?>

			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';document.id('filter_search_start').value='';document.id('filter_search_end').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>
	<?php
	if (GAnalyticsHelper::isPROMode()) {
		echo '<h4>'.GAnalyticsHelper::translate('ga:uniquePageViews').' -- '.GAnalyticsHelper::translate('ga:pageViews').'</h4>';?>
		<table id="charts-table">
			<tr>
				<td rowspan="2" id="date-chart-cell"><div id="ga-date-chart"></div></td>
				<td><div id="ga-browser-chart"></div></td>
			</tr>
			<tr>
				<td><div id="ga-country-chart"></div></td>
			</tr>
		</table>
		<hr/><br>
	<?php }?>
	<div class="clr"> </div>
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_TITLE', 'ga:pageTitle', $listDirn, $listOrder); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_VIEWS', 'ga:pageViews', $listDirn, $listOrder); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_UNIQUE_VIEWS', 'ga:uniquePageViews', $listDirn, $listOrder); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_TIME_ON_PAGE', 'ga:timeOnPage', $listDirn, $listOrder); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_ENTRANCE', 'ga:entrances', $listDirn, $listOrder); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_BOUNCE', 'ga:visitBounceRate', $listDirn, $listOrder); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_EXIT', 'ga:exitRate', $listDirn, $listOrder); ?></th>
				<th><?php echo JHtml::_('grid.sort', 'COM_GANALYTICS_PAGES_VIEW_VALUE', 'ga:totalValue', $listDirn, $listOrder); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($this->entries->getRows() as $i => $entry){?>
			<tr class="row<?php echo $i % 2; ?>">
				<td width="50%">
					<a href="<?php echo JRoute::_('index.php?option=com_ganalytics&view=page&tmpl=component&path='.base64_encode($entry[1]).'&gaid='.JRequest::getInt('gaid')); ?>">
						<?php echo $entry[0].' [ '.$entry[1].' ]'; ?>
					</a>
				</td>
				<td><?php echo $entry[2]; ?></td>
				<td><?php echo $entry[3]; ?></td>
				<td><?php echo round(($entry[4]/1000), 2).' s'; ?></td>
				<td><?php echo $entry[5]; ?></td>
				<td><?php echo round($entry[6], 2).' %'; ?></td>
				<td><?php echo round($entry[7], 2).' %'; ?></td>
				<td><?php echo $entry[8]; ?></td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8">
				<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>