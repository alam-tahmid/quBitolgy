<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');

class GAnalyticsControllerProfiles extends JControllerAdmin {

	public function getModel($name = 'Profiles', $prefix = 'GAnalyticsModel') {
		if(JRequest::getVar('task', '') == 'delete' || JRequest::getVar('task', '') == 'profiles.delete') {
			$name = 'Profile';
		}
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}