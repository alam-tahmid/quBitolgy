<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

class Com_GAnalyticsInstallerScript
{

	public function install ($parent)
	{
		// activate system plugin
		$this->run("update #__extensions set enabled=1 where type = 'plugin' and element = 'ganalytics'");

		// activate admin stats plugin
		$this->run("update #__modules set published=1, position='cpanel' where module like 'mod_ganalytics_admin_stats'");
		$this->run("insert into #__modules_menu (moduleid) select id as moduleid from #__modules where module like 'mod_ganalytics_admin_stats'");

		// set show on all pages
		$this->run(
				"insert into #__modules_menu (menuid, moduleid) select 0 as menuid, id as moduleid from #__modules where module like 'mod_ganalytics%' and module not like 'mod_ganalytics_admin_stats'");
	}

	function update ($parent)
	{
		$path = JPATH_ADMINISTRATOR . '/components/com_ganalytics/ganalytics.xml';
		$version = null;
		if (file_exists($path))
		{
			$manifest = simplexml_load_file($path);
			$version = (string) $manifest->version;
		}
		if (empty($version))
		{
			return;
		}

		if (! empty($version) && version_compare($version, '2.0.0') == - 1)
		{
			// profiles table
			$this->run("ALTER TABLE `#__ganalytics_profiles` ADD `username` VARCHAR( 255 ) NULL DEFAULT NULL;");
			$this->run("ALTER TABLE `#__ganalytics_profiles` ADD `password` text NULL DEFAULT NULL AFTER `username`;");

			$username = $this->getParam('username');
			if (! empty($username))
			{
				$this->run("update `#__ganalytics_profiles` set `username`= '" . $username . "';");
			}
			$password = $this->getParam('password');
			if (! empty($password))
			{
				jimport('joomla.utilities.simplecrypt');
				$cryptor = new JSimpleCrypt();
				$password = $cryptor->encrypt($password);
				$this->run("update `#__ganalytics_profiles` set `password`= '" . $password . "';");
			}

			// group table
			$this->run(
					"CREATE TABLE IF NOT EXISTS`#__ganalytics_stats_groups` (
					`id` int(11) NOT NULL auto_increment,
					`name` varchar(100) NOT NULL,
					`position` int(20) NOT NULL DEFAULT 0,
					`column_count` int(20) NOT NULL DEFAULT 1,
					PRIMARY KEY  (`id`)
			);");
			$this->run("insert into `#__ganalytics_stats_groups` (`name`) select `name` from `#__ganalytics_stats`;");

			// stats table
			$this->run("ALTER TABLE `#__ganalytics_stats` ADD `group_id` int(11) NOT NULL DEFAULT 1 AFTER `id`;");
			$this->run("ALTER TABLE `#__ganalytics_stats` ADD `column` int(20) NOT NULL DEFAULT 0 AFTER `group_id`;");
			$this->run("ALTER TABLE `#__ganalytics_stats` ADD `position` int(20) NOT NULL DEFAULT 0 AFTER `column`;");
			$this->run("ALTER TABLE `#__ganalytics_stats` ADD `type` varchar(250) NOT NULL DEFAULT 'list' AFTER `position`;");

			$this->run("update `#__ganalytics_stats` s set `group_id`= (select `id` from `#__ganalytics_stats_groups` g where s.name = g.name);");

			// activate system plugin
			$this->run("update #__extensions set enabled=1 where type = 'plugin' and element = 'ganalytics'");
			$this->run("update #__extensions set enabled=0 where type = 'plugin' and element = 'ganalyticstrcode'");

			// activate admin stats plugin
			$this->run("update #__modules set published=1, position='cpanel' where module like 'mod_ganalytics_admin_stats'");
			$this->run("insert into #__modules_menu (moduleid) select id as moduleid from #__modules where module like 'mod_ganalytics_admin_stats'");
		}

		if (! empty($version) && version_compare($version, '2.1.0') == - 1)
		{
			foreach (JFolder::files(JPATH_ADMINISTRATOR . DS . 'language', '.*ganalytics.*', true, true) as $file)
			{
				JFile::delete($file);
			}
			foreach (JFolder::files(JPATH_SITE . DS . 'language', '.*ganalytics.*', true, true) as $file)
			{
				JFile::delete($file);
			}
			$this->run("ALTER TABLE `#__ganalytics_stats` ADD `filter` varchar(250) DEFAULT NULL AFTER `sort`;");
		}

		if (! empty($version) && version_compare($version, '3.0.0') == - 1)
		{
			$this->run("ALTER TABLE `#__ganalytics_profiles` DROP `username`");
			$this->run("ALTER TABLE `#__ganalytics_profiles` CHANGE `password` `token` TEXT NULL DEFAULT NULL");

			$this->run("delete from `#__ganalytics_profiles`");
		}
	}

	public function uninstall ($parent)
	{
	}

	public function preflight ($type, $parent)
	{
		// Delete existing update sites, neccessary if upgrading eg. free to pro
		$this->run(
				"delete from #__update_sites_extensions where extension_id in (select extension_id from #__extensions where element = 'pkg_ganalytics')");
		$this->run("delete from #__update_sites where name like 'GAnalytics%'");
	}

	public function postflight ($type, $parent)
	{
	}

	private function run ($query)
	{
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	private function getParam ($name)
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_ganalytics"');
		$manifest = json_decode($db->loadResult(), true);
		if (! key_exists($name, $manifest))
		{
			return null;
		}
		return $manifest[$name];
	}
}