<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JHtml::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_ganalytics'); ?>" method="post" name="adminForm" id="adminForm">
<table class="table table-striped" id="eventList">
	<thead>
		<tr>
			<th width="1%" class="hidden-phone">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="title">
				<?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_ACCOUNT_NAME'); ?>
			</th>
			<th width="20%">
				<?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_PROFILE_NAME'); ?>
			</th>
			<th width="20%">
				<?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_ACCOUNT_ID'); ?>
			</th>
			<th width="20%">
				<?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_PROFILE_ID'); ?>
			</th>
			<th width="20%">
				<?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_WEB_PROFILE_ID'); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->items as $i => $item) {?>
		<tr class="row<?php echo $i % 2; ?>">
				<td class="center hidden-phone">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="nowrap has-context">
					<a href="<?php echo JRoute::_( 'index.php?option=com_ganalytics&task=profile.edit&id='. $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT');?>">
						<?php echo $this->escape($item->accountName); ?>
					</a>
				</td>
				<td class="nowrap has-context">
					<?php echo $this->escape($item->profileName); ?>
				</td>
				<td class="nowrap has-context">
					<?php echo $this->escape($item->accountID); ?>
				</td>
				<td class="nowrap has-context">
					<?php echo $this->escape($item->profileID); ?>
				</td>
				<td class="nowrap has-context">
					<?php echo $this->escape($item->webPropertyId); ?>
				</td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>