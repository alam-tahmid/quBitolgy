<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class GAnalyticsModelDashboard extends JModelLegacy
{

	private $statsViews;

	private $profiles;

	private $groups;

	public function getStatsViews ()
	{
		if (empty($this->statsViews))
		{
			$this->statsViews = $this->_getList('SELECT * FROM #__ganalytics_stats order by `group_id` asc, `column` asc, `position` asc');
		}
		return $this->statsViews;
	}

	public function getGroups ()
	{
		if (empty($this->groups))
		{
			$this->groups = $this->_getList('SELECT * FROM #__ganalytics_stats_groups order by `position` asc');
		}
		return $this->groups;
	}

	public function getProfiles ()
	{
		if (empty($this->profiles))
		{
			$this->profiles = $this->_getList('SELECT * FROM #__ganalytics_profiles');
		}
		return $this->profiles;
	}

	public function saveStructure (array $structure)
	{
		foreach ($structure as $index => $column)
		{
			foreach ($column as $row => $id)
			{
				$table = JTable::getInstance('StatsView', 'GAnalyticsTable');
				$table->save(array(
						'id' => $id,
						'column' => $index,
						'position' => $row
				));
			}
		}
		return true;
	}

	public function addgroup ($name)
	{
		$table = $this->getTable('StatsGroup', 'GAnalyticsTable');
		$this->getDbo()->setQuery('select max(position) as max from #__ganalytics_stats_groups');
		$max = $this->getDbo()->loadObjectList();
		$table->save(array(
				'name' => $this->getDbo()
					->escape($name),
				'position' => (int) $max[0]->max + 1
		));

		return $table;
	}

	public function deletegroup ($id)
	{
		$table = $this->getTable('StatsGroup', 'GAnalyticsTable');
		$table->load($id);
		$success = $table->delete($id);
		if (! $success)
		{
			return $success;
		}
		$this->getDbo()->setQuery('update #__ganalytics_stats_groups set `position` = (`position` -1) where `position`> ' . $table->position);
		$this->getDbo()->query();

		$this->getDbo()->setQuery('delete from #__ganalytics_stats where `group_id` = ' . $id);
		$this->getDbo()->query();

		return $success;
	}

	public function addcolumn ($id, $column)
	{
		$table = $this->getTable('StatsGroup', 'GAnalyticsTable');
		$table->load($id);
		$table->column_count = $table->column_count + 1;
		$success = $table->store();
		if (! $success)
		{
			return $success;
		}
		$this->getDbo()->setQuery(
				'update #__ganalytics_stats set `column` = (`column` +1) where `group_id`=' . $table->id . ' and `column`>' . $column);
		$this->getDbo()->query();

		return $success;
	}

	public function deletecolumn ($id, $column)
	{
		$table = $this->getTable('StatsGroup', 'GAnalyticsTable');
		$table->load($id);
		$table->column_count = $table->column_count - 1;
		$success = $table->store();
		if (! $success)
		{
			return $success;
		}
		$this->getDbo()->setQuery('delete from #__ganalytics_stats where `group_id`=' . $table->id . ' and `column`=' . $column);
		$this->getDbo()->query();

		$this->getDbo()->setQuery(
				'update #__ganalytics_stats set `column` = (`column` -1) where `group_id`=' . $table->id . ' and `column`>' . $column);
		$this->getDbo()->query();

		return $success;
	}

	public function savewidget ()
	{
		$table = $this->getTable('StatsView', 'GAnalyticsTable');

		JRequest::setVar('dimensions', implode(',', JRequest::getVar('dimensions', array())));
		JRequest::setVar('metrics', implode(',', JRequest::getVar('metrics', array())));
		JRequest::setVar('sort', implode(',', JRequest::getVar('sort', array())));

		return $table->save(JRequest::get());
	}

	public function addwidget ()
	{
		$table = $this->getTable('StatsView', 'GAnalyticsTable');

		JRequest::setVar('dimensions', implode(',', JRequest::getVar('dimensions', array())));
		JRequest::setVar('metrics', implode(',', JRequest::getVar('metrics', array())));
		JRequest::setVar('sort', implode(',', JRequest::getVar('sort', array())));

		$success = $table->save(JRequest::get());
		if (! $success)
		{
			return $success;
		}

		$this->getDbo()->setQuery('select max(position) as max from #__ganalytics_stats where group_id=' . $table->group_id);
		$max = $this->getDbo()->loadObjectList();
		$table->save(array(
				'position' => (int) $max[0]->max + 1
		));

		return $success;
	}

	public function deletewidget ($id)
	{
		$table = $this->getTable('StatsView', 'GAnalyticsTable');
		$table->load($id);
		$success = $table->delete($id);

		if (! $success)
		{
			return $success;
		}

		$this->getDbo()->setQuery(
				'update #__ganalytics_stats set `position` = (`position` -1) where `group_id`=' . $table->group_id . ' and `column`=' . $table->column .
						 ' and `position`>' . $table->position);
		$this->getDbo()->query();
		return $success;
	}

	public function getStatsView ()
	{
		$view = JTable::getInstance('StatsView', 'GAnalyticsTable');
		$view->load(JRequest::getInt('id'));
		return $view;
	}

	public function getProfile ()
	{
		$profile = JTable::getInstance('Profile', 'GAnalyticsTable');
		$profile->load(JRequest::getInt('gaid', 0));
		return $profile;
	}

	public function getStatsData ()
	{
		$view = JTable::getInstance('StatsView', 'GAnalyticsTable');
		$view->load(JRequest::getInt('id'));

		$profile = JTable::getInstance('Profile', 'GAnalyticsTable');
		$profile->load(JRequest::getInt('gaid', 0));

		$s = JRequest::getVar('start-date', null);
		$start = $s == null ? null : JFactory::getDate($s);

		$e = JRequest::getVar('end-date', null);
		$end = $e == null ? null : JFactory::getDate($e);

		return GAnalyticsDataHelper::getData($profile, $view->dimensions, $view->metrics, $start, $end, $view->sort, $view->filter, $view->max_result);
	}

	public function reset ()
	{
		$this->getDbo()->setQuery('delete from #__ganalytics_stats_groups');
		$this->getDbo()->query();
		$this->getDbo()->setQuery(
				"
			INSERT INTO `#__ganalytics_stats_groups` (`id`, `name`, `position`, `column_count`) VALUES
			(1, 'Overview', 0, 2),
			(2, 'Visitors', 0, 2),
			(3, 'Pages', 0, 2),
			(4, 'Sources', 0, 2),
			(5, 'Demographics', 0, 2),
			(6, 'System', 0, 2),
			(7, 'Speed', 0, 2);
		");
		$this->getDbo()->query();

		$this->getDbo()->setQuery('delete from #__ganalytics_stats');
		$this->getDbo()->query();

		$this->getDbo()->setQuery(
				"
INSERT INTO `#__ganalytics_stats` (`id`, `group_id`, `column`, `position`, `type`, `name`, `metrics`, `dimensions`, `sort`, `filter`, `max_result`) VALUES
(1, 2, 0, 0, 'chart', 'Visitors per day', 'ga:visits,ga:newVisits', 'ga:date', 'ga:date', '', 1000),
(2, 3, 0, 0, 'list', 'Top pages', 'ga:newVisits,ga:visits,ga:bounces', 'ga:pagePath', '-ga:visits', '', 10),
(3, 4, 0, 0, 'chart', 'Referring Sites', 'ga:visits', 'ga:source', '-ga:visits', '', 10),
(4, 5, 0, 1, 'list', 'Countrys', 'ga:newVisits,ga:visits', 'ga:country', '-ga:visits', '', 300),
(5, 6, 0, 0, 'chart', 'Browsers', 'ga:visits', 'ga:browser', '-ga:visits', '', 10),
(7, 2, 0, 1, 'chart', 'Time on Site', 'ga:timeOnPage,ga:avgTimeOnPage', 'ga:date', 'ga:date', '', 1000),
(8, 2, 1, 1, 'chart', 'Page views per day', 'ga:pageviews,ga:uniquePageviews,ga:pageviewsperVisit', 'ga:date', 'ga:date', '', 1000),
(9, 2, 1, 0, 'chart', 'New vs returning', 'ga:visitors', 'ga:visitorType', '', '', 1000),
(10, 3, 1, 1, 'list', 'Page load time', 'ga:pageLoadTime', 'ga:pagePath', '-ga:pageLoadTime', '', 1000),
(11, 4, 1, 1, 'chart', 'Referring medium', 'ga:visits', 'ga:medium', '-ga:visits', '', 1000),
(12, 4, 0, 2, 'list', 'Search keywords', 'ga:newVisits,ga:visits', 'ga:keyword', '-ga:visits', '', 10),
(13, 4, 1, 3, 'chart', 'Campaign', 'ga:visits', 'ga:campaign', '-ga:visits', '', 10),
(14, 5, 0, 0, 'chart', 'Country', 'ga:visits', 'ga:country', '-ga:visits', '', 100),
(15, 5, 1, 2, 'chart', 'Continent', 'ga:visits,ga:newVisits', 'ga:continent', '-ga:visits', '', 10),
(16, 5, 1, 3, 'chart', 'Network', 'ga:visits,ga:newVisits', 'ga:networkDomain', '-ga:visits', '', 10),
(17, 6, 1, 1, 'chart', 'OS', 'ga:visits', 'ga:operatingSystem', '-ga:visits', '', 10),
(18, 6, 0, 2, 'chart', 'Screen resolution', 'ga:visits', 'ga:screenResolution', '-ga:visits', '', 10),
(19, 6, 1, 3, 'chart', 'Mobile', 'ga:visits', 'ga:isMobile', '', '', 10),
(20, 3, 0, 2, 'chart', 'Language', 'ga:visits', 'ga:language', '-ga:visits', '', 10),
(21, 3, 1, 3, 'list', 'Landing page', 'ga:newVisits,ga:visits', 'ga:landingPagePath', '-ga:visits', '', 10),
(23, 1, 0, 1, 'chart', 'Visitors per day', 'ga:visits,ga:newVisits', 'ga:date', 'ga:date', '', 1000),
(24, 1, 1, 2, 'chart', 'Country', 'ga:visits', 'ga:country', '-ga:visits', '', 10),
(25, 1, 0, 3, 'chart', 'Source', 'ga:visits', 'ga:source', '-ga:visits', '', 10),
(26, 1, 1, 4, 'list', 'Top pages', 'ga:newVisits,ga:visits,ga:bounces', 'ga:pagePath', '-ga:visits', '', 10),
(27, 7, 0, 1, 'chart', 'Server connection', 'ga:serverConnectionTime,ga:serverResponseTime', 'ga:date', 'ga:date', '', 1000),
(28, 7, 0, 2, 'chart', 'Country', 'ga:pageDownloadTime', 'ga:country', '-ga:pageDownloadTime', '', 1000),
(29, 7, 1, 3, 'chart', 'Browser', 'ga:avgPageLoadTime', 'ga:browser', '', '', 10),
(30, 7, 1, 4, 'chart', 'Average speed time', 'ga:avgPageDownloadTime,ga:avgServerConnectionTime,ga:avgServerResponseTime', 'ga:pageTitle', '-ga:avgPageDownloadTime', '', 10);
		");
		$this->getDbo()->query();

		return true;
	}

	public function refreshUpdateSite ()
	{
		if (! GAnalyticsHelper::isPROMode())
		{
			return;
		}

		JLoader::import('joomla.application.component.helper');
		$params = JComponentHelper::getParams('com_ganalytics');

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$dlid = $params->get('downloadid', '');
		}
		else
		{
			$dlid = $params->getValue('downloadid', '');
		}
		$dlid = trim($dlid);

		$extra_query = null;

		// If I have a valid Download ID I will need to use a non-blank
		// extra_query in Joomla! 3.2+
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			$extra_query = 'dlid=' . $dlid;
		}

		// Create the update site definition we want to store to the database
		$update_site = array(
				'enabled' => 1,
				'last_check_timestamp' => 0,
				'extra_query' => $extra_query
		);

		if (version_compare(JVERSION, '3.0.0', 'lt'))
		{
			unset($update_site['extra_query']);
		}

		$db = $this->getDbo();

		// Get the extension ID to ourselves
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q('package'))
			->where($db->qn('element') . ' = ' . $db->q('pkg_ganalytics'));
		$db->setQuery($query);

		$extension_id = $db->loadResult();

		if (empty($extension_id))
		{
			return;
		}

		// Get the update sites for our extension
		$query = $db->getQuery(true)
			->select($db->qn('update_site_id'))
			->from($db->qn('#__update_sites_extensions'))
			->where($db->qn('extension_id') . ' = ' . $db->q($extension_id));
		$db->setQuery($query);

		$updateSiteIDs = $db->loadColumn(0);
		if (count($updateSiteIDs))
		{
			// Loop through all update sites
			foreach ($updateSiteIDs as $id)
			{
				$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__update_sites'))
					->where($db->qn('update_site_id') . ' = ' . $db->q($id));
				$db->setQuery($query);
				$aSite = $db->loadObject();

				// Do we have the extra_query property (J 3.2+) and does it
				// match?
				if (property_exists($aSite, 'extra_query'))
				{
					if ($aSite->extra_query == $update_site['extra_query'])
					{
						continue;
					}
				}
				else
				{
					// Joomla! 3.1 or earlier. Updates may or may not work.
					continue;
				}

				$update_site['update_site_id'] = $id;
				$newSite = (object) $update_site;
				$db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
			}
		}
	}
}