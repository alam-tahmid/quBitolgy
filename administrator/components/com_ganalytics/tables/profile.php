<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.database.table');
jimport('joomla.utilities.simplecrypt');

class GAnalyticsTableProfile extends JTable {

	public function __construct(&$db = null) {
		if($db == null) {
			$db = JFactory::getDbo();
		}
		parent::__construct('#__ganalytics_profiles', 'id', $db);
	}
}