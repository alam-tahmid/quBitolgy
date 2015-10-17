<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$profile = $this->profile;

JHtml::_('behavior.tooltip');
?>

<form action="<?php echo JRoute::_('index.php?option=com_ganalytics&layout=edit&id='.(int) $profile->id); ?>" method="post" name="adminForm" id="ganalytics-form">
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_GANALYTICS_PROFILE_VIEW_TITLE'); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('details') as $field){ ?>
				<li><?php echo $field->label;echo $field->input;?></li>
<?php }; ?>
			</ul>
		</fieldset>
	</div>
	<div>
		<input type="hidden" name="task" value="profile.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>