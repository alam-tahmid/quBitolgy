<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class GAnalyticsModelProfiles extends JModelList {

	protected function getListQuery(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');

		$ids = $this->getState('ids', null);
		if($ids !== null){
			if(is_array($ids)){
				$query->where('id in ('.implode(',', $ids).')');
			} else {
				$query->where('id = '.(int)$ids);
			}
		}

		$query->from('#__ganalytics_profiles');
		return $query;
	}
}