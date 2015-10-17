<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');

class GAnalyticsControllerImport extends JControllerAdmin {

	public function getModel($name = 'Import', $prefix = 'GAnalyticsModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function import() {
		$this->setRedirect('index.php?option=com_ganalytics&view=import');
	}

	public function save() {
		$model = $this->getModel();

		if ($model->store()) {
			$msg = JText::_('COM_GANALYTICS_IMPORT_VIEW_CONTROLLER_SAVE_SUCCESS');
		} else {
			$msg = JText::_('COM_GANALYTICS_IMPORT_VIEW_CONTROLLER_SAVE_ERROR');
		}

		$link = 'index.php?option=com_ganalytics&view=profiles';
		$this->setRedirect($link, $msg);
	}

	public function cancel() {
		$msg = JText::_('COM_GANALYTICS_IMPORT_VIEW_CONTROLLER_SAVE_ABORT');
		$this->setRedirect( 'index.php?option=com_ganalytics&view=profiles', $msg );
	}
}