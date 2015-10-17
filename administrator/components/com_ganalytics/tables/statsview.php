<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.database.table');

class GAnalyticsTableStatsView extends JTable {

	public function __construct(&$db) {
		parent::__construct('#__ganalytics_stats', 'id', $db);
	}

	public function load($keys = null, $reset = true) {
		$success = parent::load($keys, $reset);

		if($success) {
			$this->dimensions = explode(',', $this->dimensions);
			$this->metrics = explode(',', $this->metrics);

			if(!empty($this->sort)) {
				$this->sort = explode(',', $this->sort);
			} else {
				$this->sort = null;
			}
		}

		return $success;
	}
}