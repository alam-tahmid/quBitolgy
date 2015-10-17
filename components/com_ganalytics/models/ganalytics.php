<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class GAnalyticsModelGAnalytics extends JModelLegacy {

	public function getSelectedProfile() {
		$model = JModelLegacy::getInstance('Profiles', 'GAnalyticsModel');
		$params = $this->getState('parameters.menu');
		if($params != null){
			$model->setState('ids', $params->get('accountids', null));
		}
		$items = $model->getItems();
		if(empty($items)){
			return null;
		}
		return $items[0];
	}
}