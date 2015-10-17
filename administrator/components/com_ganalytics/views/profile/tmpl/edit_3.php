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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$input = JFactory::getApplication()->input;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'profile.cancel' || document.formvalidator.isValid(document.id('profile-form'))) {
			Joomla.submitform(task, document.getElementById('profile-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_ganalytics&layout=edit&id='.(int) $profile->id); ?>" method="post" name="adminForm" id="profile-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_GANALYTICS_PROFILE_VIEW_TITLE');?></a></li>
			</ul>
			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<div class="row-fluid">
						<div class="span6">
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('profileName'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('profileName'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('profileID'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('profileID'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('accountName'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('accountName'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('accountID'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('accountID'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('webPropertyId'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('webPropertyId'); ?>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<?php echo $this->form->getLabel('startDate'); ?>
								</div>
								<div class="controls">
									<?php echo $this->form->getInput('startDate'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $input->getCmd('return');?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<!-- End Content -->
	</div>
</form>

<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>