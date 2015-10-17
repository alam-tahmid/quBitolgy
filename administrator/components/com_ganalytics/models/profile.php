<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

class GAnalyticsModelProfile extends JModelAdmin {

	protected function allowEdit($data = array(), $key = 'id') {
		return JFactory::getUser()->authorise('core.edit', 'com_ganalytics.profile.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}

	public function getTable($type = 'Profile', $prefix = 'GAnalyticsTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_ganalytics.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData() {
		$data = JFactory::getApplication()->getUserState('com_ganalytics.edit.profile.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}
}