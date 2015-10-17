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
<form action="<?php echo JRoute::_('index.php?option=com_ganalytics'); ?>" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="20"><input type="checkbox" name="toggle" value=""
					onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>
				<th><?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_ACCOUNT_NAME') ?></th>
				<th><?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_PROFILE_NAME') ?></th>
				<th><?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_ACCOUNT_ID') ?></th>
				<th><?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_PROFILE_ID') ?></th>
				<th><?php echo JText::_('COM_GANALYTICS_IMPORT_VIEW_COLUMN_WEB_PROFILE_ID') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->items as $i => $item){?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->profileID); ?>
						<input type="hidden" name="accounts[<?php echo $item->profileID;?>][id]" value="<?php echo $item->accountID;?>" />
						<input type="hidden" name="accounts[<?php echo $item->profileID;?>][profile]" value="<?php echo $item->profileID;?>" />
						<input type="hidden" name="accounts[<?php echo $item->profileID;?>][property]" value="<?php echo $item->webPropertyId;?>" />
						<input type="hidden" name="accounts[<?php echo $item->profileID;?>][name]" value="<?php echo base64_encode($item->accountName);?>" />
						<input type="hidden" name="accounts[<?php echo $item->profileID;?>][profileName]" value="<?php echo base64_encode($item->profileName);?>" />
						<input type="hidden" name="accounts[<?php echo $item->profileID;?>][startDate]" value="<?php echo base64_encode($item->startDate);?>" />
						<input type="hidden" name="accounts[<?php echo $item->profileID;?>][token]" value="<?php echo base64_encode($item->token);?>" />
					</td>
					<td><?php echo $item->accountName; ?></td>
					<td><?php echo $item->profileName; ?></td>
					<td><?php echo $item->accountID; ?></td>
					<td><?php echo $item->profileID; ?></td>
					<td><?php echo $item->webPropertyId; ?></td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8">
					<br/><br/>
					<div align="center" style="clear: both">
						<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>