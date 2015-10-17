<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.database.table');

class GAnalyticsTableStatsGroup extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__ganalytics_stats_groups', 'id', $db);
	}
}