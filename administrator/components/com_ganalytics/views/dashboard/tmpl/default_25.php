<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if(empty($this->profiles)){
	echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_NO_PROFILES');
	return;
}
GAnalyticsHelper::loadjQuery();

jimport('joomla.form.form');

$document = JFactory::getDocument();
$document->addScript((JBrowser::getInstance()->isSSLConnection() ? 'https' : 'http').'://www.google.com/jsapi');
$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/ui/jquery-ui.custom.min.js');
$document->addStyleSheet(JURI::base().'components/com_ganalytics/libraries/jquery/themes/aristo/jquery-ui.custom.css');
$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/ext/jquery-cookie.js');
$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/pnotify/jquery.pnotify.min.js');
$document->addStyleSheet(JURI::base().'components/com_ganalytics/libraries/jquery/pnotify/jquery.pnotify.default.css');
$document->addScript(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/multiselect/jquery.multiselect2side.js');
$document->addStyleSheet(JURI::root().'administrator/components/com_ganalytics/libraries/jquery/multiselect/jquery.multiselect2side.css');

$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.easing-1.3.pack.js');
$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.mousewheel-3.0.4.pack.js');
$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.fancybox-1.3.4.pack.js');
$document->addStyleSheet(JURI::base().'components/com_ganalytics/libraries/jquery/fancybox/jquery.fancybox-1.3.4.css');


if (GAnalyticsHelper::isPROMode()) {
	$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/ganalytics/chart.js');
} else {
	$document->addScript(JURI::base().'components/com_ganalytics/libraries/jquery/ganalytics/list.js');
}
$document->addScript(JURI::base().'components/com_ganalytics/views/dashboard/tmpl/default.js');
$document->addStyleSheet(JURI::base().'components/com_ganalytics/views/dashboard/tmpl/default.css');
?>
<table><tr>
<td style="padding-right:20px"><?php echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_SELECT_PROFILES');?>
<div class="input-append">
	<select id="profiles">
<?php
$selected = false;
foreach ($this->profiles as $profile) {
	if(!$selected) $selected = true;
	echo "	<option ".($selected?'':"selected=\"true\"")." value=\"".$profile->id."\">".$profile->profileName."</option>\n";
}
?>
	</select></div>
</td>
<td style="padding-right:20px">
	<?php echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_SELECT_DATE_FROM');
	echo JHtml::_('calendar', strftime('%Y-%m-%d', strtotime('-1 month')), 'date_from', 'date_from', '%Y-%m-%d', array('size' => 10, 'class' => ''));
	?>
</td>
<td style="padding-right:20px">
	<?php echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_SELECT_DATE_TO');
	echo JHtml::_('calendar', strftime('%Y-%m-%d', strtotime('-1 day')), 'date_to', 'date_to', '%Y-%m-%d', array('size' => 10));
	?>
</td>
<td><?php echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_GOOGLE_ANALYTICS_LINK');?></td>
<tr></table>

<div id="tabs">
	<ul>
<?php foreach ($this->groups as $group) {?>
		<li><a href="#tabs-<?php echo $group->id;?>"><?php echo $group->name;?></a> <span class='ui-icon ui-icon-close'></span></li>
<?php }?>
<li><a href="javascript:;" id="add-tab-button"><span class='ui-icon ui-icon-plusthick'></span></a></li>
	</ul>
<?php
$scriptCode = "gajQuery(document).ready(function(){\n";
foreach ($this->groups as $group) {?>
<table id="tabs-<?php echo $group->id;?>" class="chart-table">
<thead>
<tr>
<?php for ($i = 0; $i < $group->column_count; $i++) {?>
	<td>
		<div class="column-action">
			<span class='ui-icon ui-icon-calculator'></span>
			<span class='ui-icon ui-icon-minusthick'></span>
			<span class='ui-icon ui-icon-plusthick'></span>
		</div>
	</td>
<?php }?>
</tr>
</thead>
<tbody>
<tr>
<?php for ($i = 0; $i < $group->column_count; $i++) {?>
	<td valign="top">
<?php foreach ($this->statsViews as $view) {
		if($view->group_id != $group->id || $view->column != $i){
			continue;
		}?>
		<div class="portlet">
			<div class="portlet-header"><div class="portlet-title"><?php echo $view->name;?></div></div>
			<div class="portlet-content">
		<?php
		$scriptCode .= "gajQuery('#widgetForm-".$view->id."').gaChart({
			chartDivID: 'gaChartDiv".$view->id."',
			gaid: gajQuery('#profiles').val(),
			url: 'index.php?option=com_ganalytics&view=dashboard&format=raw&layout=data&id=".$view->id."',
			start: gajQuery('#date_from').val(),
			end: gajQuery('#date_to').val(),
			colorStart: '".GAnalyticsHelper::getFadedColor('EB8F33', 0.2)."',
			colorEnd: 'EB8F33',
			pathPrefix: '".JURI::root()."'
		});\n";
		?>
		<form method="post" name="adminForm" class="adminForm" id="widgetForm-<?php echo $view->id;?>">
			<?php
			JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
			JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
			$form = JForm::getInstance('com_ganalytics.statsview', 'statsview');
			$form->bind($view);
			?>
			<fieldset class="adminform">
			<table class="adminformlist">
				<?php foreach($form->getFieldset('details') as $field){
 					if($field->__get('name') == 'dimensions[]'){
 						$field->setNotLoad();
 					}
 					if($field->__get('name') == 'metrics[]'){
 						$field->setNotLoad();
 					}
				?>
				<tr><td><?php echo $field->label;?></td><td><?php echo $field->input;?></td></tr>
				<?php }; ?>

			</table>
			</fieldset>
		</form>
		</div></div>
	<?php }?>
	</td>
<?php }?>
	</tr></tbody></table>
	<?php
}

$scriptCode .= "showCharts();\n});\n";
$document->addScriptDeclaration($scriptCode);
?>
</div>
<br/>
<div id="dialog-group" class="gadialog" title="<?php echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_NEW_TAB_TITLE');?>" style="display: none;">
	<form>
		<fieldset class="ui-helper-reset">
			<label for="tab_name"><?php echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_NEW_TAB_LABEL');?>: </label>
			<input type="text" name="tab_name" id="tab_name" value="" class="ui-widget-content ui-corner-all" />
		</fieldset>
		<div class="fltrt">
			<button type="button" id="dialog-group-save"><?php echo JText::_('JSAVE');?></button>
			<button type="button" id="dialog-group-cancel"><?php echo JText::_('JCANCEL');?></button>
		</div>
	</form>
</div>

<div id="dialog-widget" class="gadialog" title="<?php echo JText::_('COM_GANALYTICS_DASHBOARD_VIEW_NEW_WIDGET_TITLE');?>" style="display: none;">
<form>
			<?php
			JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
			JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
			$form = JForm::getInstance('com_ganalytics.statsview', 'statsview');
			$form->reset();
			?>
			<fieldset class="ui-helper-reset">
			<table class="adminformlist">
				<?php foreach($form->getFieldset('details') as $field){?>
				<tr><td><?php echo $field->label;?></td><td><?php echo $field->input;?></td></tr>
				<?php }; ?>

			</table>
			</fieldset>
			<div class="fltrt">
				<button type="button" id="dialog-widget-save"><?php echo JText::_('JSAVE');?></button>
				<button type="button" id="dialog-widget-cancel"><?php echo JText::_('JCANCEL');?></button>
			</div>
		</form>
</div>

<form name="adminForm" id="adminForm" method="post">
<div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_ganalytics" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>
<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>