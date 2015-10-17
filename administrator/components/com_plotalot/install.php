<?php
/********************************************************************
Product    : Plotalot
Date       : 7 October 2014
Copyright  : Les Arbres Design 2014
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access'); 

class com_plotalotInstallerScript
{
public function preflight($type, $parent) 
{
	$version = new JVersion();  			// get the Joomla version (JVERSION did not exist before Joomla 2.5)
	$joomla_version = $version->RELEASE.'.'.$version->DEV_LEVEL;

	if (version_compare($joomla_version,"2.5.5","<"))			// JDatabase::execute() was added in Joomla 2.5.5
		{
		Jerror::raiseWarning(null, "Plotalot requires at least Joomla 2.5.5");
		return false;
		}
		
	if (get_magic_quotes_gpc())
		{
		Jerror::raiseWarning(null, "Plotalot cannot run with PHP Magic Quotes ON. Please switch it off and re-install.");
		return false;
		}
		
	if (!function_exists('mysql_connect'))
		{
		Jerror::raiseWarning(null, "Plotalot cannot run on this server because it does not support the PHP mysql_ functions.");
		return false;
		}

	return true;
}

public function uninstall($parent)
{ 
	echo "<h2>Plotalot has been uninstalled</h2>";
	$app = JFactory::getApplication();
	$dbprefix = $app->getCfg('dbprefix');
	echo "<h2>The ".$dbprefix."plotalot table was NOT deleted</h2>";
}

//-------------------------------------------------------------------------------
// The main install function
//
public function postflight($type, $parent)
{
// check the Joomla version

	if (substr(JVERSION,0,1) > "3")				// if > 3
		echo "This version of Plotalot has not been tested on this version of Joomla.";
	
// get the component version from the component manifest xml file		

	$component_version = $parent->get('manifest')->version;
	
// delete redundant files from older versions

	@unlink(JPATH_SITE.'/administrator/components/com_plotalot/admin.plotalot.php');
	@unlink(JPATH_SITE.'/administrator/components/com_plotalot/joomla15.xml');
	@unlink(JPATH_SITE.'/administrator/components/com_plotalot/joomla16.xml');
	@unlink(JPATH_SITE.'/administrator/components/com_plotalot/tables/chart.php');
	@rmdir (JPATH_SITE.'/administrator/components/com_plotalot/tables');
	@unlink(JPATH_SITE.'/administrator/components/com_plotalot/install.sql');
	@unlink(JPATH_SITE.'/administrator/components/com_plotalot/uninstall.plotalot.php');
	@unlink(JPATH_SITE.'/administrator/components/com_plotalot/install.plotalot.php');

// create our database table if not already present

	$this->_db = JFactory::getDBO();
	$this->create_tables();

// add new columns if necessary

	$this->add_column('#__plotalot', 'y_format', "SMALLINT NOT NULL DEFAULT '0' AFTER `y_end`");
	$this->add_column('#__plotalot', 'y_labels', "SMALLINT NOT NULL DEFAULT '-1' AFTER `y_format`");
	$this->add_column('#__plotalot', 'extra_parms', "text NOT NULL DEFAULT '' AFTER `y_labels`");
	$this->add_column('#__plotalot', 'extra_columns', "varchar(255) NOT NULL DEFAULT '' AFTER `extra_parms`");		// 4.00
	$this->add_column('#__plotalot', 'sample_id', "smallint(6) NOT NULL DEFAULT '0' AFTER `chart_name`");			// 4.00
	$this->add_column('#__plotalot', 'state', "tinyint(1) NOT NULL DEFAULT '0' AFTER `sample_id`");					// 4.00

// Add sample_id's for samples created without them

	$this->add_sample_ids();

// Upgrade to version 3.00 if necessary

	if (!$this->column_exists('#__plotalot', 'chart_option'))
		$this->upgrade3();
		
// create any missing sample charts

	$this->create_samples();

// delete any cached chart files

	$app = JFactory::getApplication();
	$tmp_path = $app->getCfg('tmp_path');
	$mask = $tmp_path.'/plotalot_*.txt';
	$tmp_list = glob($mask);					// get an array of matching files
	if (is_array($tmp_list))					// .. but glob can return false
		array_map("unlink",$tmp_list);
	
// we are done
		
	echo "<h3>Plotalot version $component_version installed</h3>";
	return;
}

//-------------------------------------------------------------------------------
// Create our database table
//
function create_tables()
{
	$this->ladb_execute("CREATE TABLE IF NOT EXISTS `#__plotalot` (
		  `id` int(11) NOT NULL auto_increment,
		  `published` tinyint(1) NOT NULL default '1',
		  `chart_type` smallint(6) NOT NULL default '0',
		  `chart_option` smallint(6) NOT NULL default '0',
		  `chart_name` varchar(255) NOT NULL default '',
		  `sample_id` smallint(6) NOT NULL default '0',
		  `state` tinyint(1) NOT NULL default '0',
		  `chart_title` text NOT NULL,
		  `chart_title_colour` varchar(6) NOT NULL default '',
		  `chart_title_font_size` varchar(4) NOT NULL default '0',
		  `styles` text NOT NULL,
		  `back_colour` varchar(6) NOT NULL default '0',
		  `x_size` smallint(6) NOT NULL default '0',
		  `y_size` smallint(6) NOT NULL default '0',
		  `db_host` varchar(60) NOT NULL default '',
		  `db_name` varchar(60) NOT NULL default '',
		  `db_user` varchar(60) NOT NULL default '',
		  `db_pass` varchar(60) NOT NULL default '',
		  `num_plots` smallint(6) NOT NULL default '1',
		  `show_grid` tinyint(4) NOT NULL default '0',
		  `show_raw_data` tinyint(4) NOT NULL default '0',
		  `show_script` tinyint(4) NOT NULL default '0',
		  `legend_type` tinyint(4) NOT NULL default '0',
		  `x_title` text NOT NULL,
		  `x_start` text NOT NULL,
		  `x_end` text NOT NULL,
		  `x_format` smallint(6) NOT NULL default '0',
		  `x_labels` smallint(6) NOT NULL default '-1',
		  `y_title` text NOT NULL,
		  `y_start` text NOT NULL,
		  `y_end` text NOT NULL,
		  `y_format` smallint(6) NOT NULL default '0',
		  `y_labels` smallint(6) NOT NULL default '-1',
		  `extra_parms` text NOT NULL,
		  `extra_columns` varchar(255) NOT NULL default '',
		  `plots` text NOT NULL,
		  `date_created` date NOT NULL,
		  `date_updated` date NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;") ;
}

//-------------------------------------------------------------------------------
// Add sample charts
// - from version 4.x we always include a sample_id
//
function create_samples()
{
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 6");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 400, 1, 6,  'Sample Pie Chart 1: Your most popular articles', 'Most Popular Articles', '0000FF', '', '', 'FFFFFF', 500, 400, '', '', '', '', 1, 0, 0, 0, 20, '', '', '', 0, -1, '', '', '', 0, -1, '', 'a:1:{i:0;a:5:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:6:\"72E327\";s:5:\"style\";s:2:\"80\";s:5:\"query\";s:62:\"SELECT title, hits FROM #__content order by hits desc limit 10\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 7");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 410, 3, 7,  'Sample Pie Chart 2', 'Sample Pie Chart', '', '', '', 'FFFFFF', 500, 400, '', '', '', '', 1, 0, 0, 0, 40, '', '', '', 30, -1, '', '', '', 10, -1, '', 'a:1:{i:0;a:5:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"style\";s:1:\"0\";s:5:\"query\";s:105:\"SELECT ''Slice 1'', 42\r\nUNION SELECT ''Slice 2'', 65\r\nUNION SELECT ''Slice 3'', 27\r\nUNION SELECT ''Slice 4'', 134\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");

	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 8");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 300, 0, 8,  'Sample Bar Chart 1', 'Sample Bar Chart', '0000FF', '', '', '', 600, 400, '', '', '', '', 1, 1, 0, 0, 0, 'Days', '', '', 0, -1, 'Numbers', '0', '6', 0, 7, '', 'a:1:{i:0;a:5:{s:6:\"legend\";s:4:\"Days\";s:6:\"colour\";s:6:\"B5A621\";s:5:\"style\";s:1:\"0\";s:5:\"query\";s:130:\"SELECT ''Monday'', 1\r\nUNION SELECT ''Tuesday'', 3 \r\nUNION SELECT ''Wednesday'', 2 \r\nUNION SELECT ''Thursday'', 4\r\nUNION SELECT ''Friday'', 5\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 9");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 320, 0, 9,  'Sample Bar Chart 2: Two plots', '', '', '', '', '', 800, 400, '', '', '', '', 2, 1, 0, 0, 0, '', '', '', 0, -1, '', '', '', 10, -1, '', 'a:2:{i:0;a:5:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:79:\"SELECT ''Monday'', -1\r\nUNION SELECT ''Tuesday'', -3 \r\nUNION SELECT ''Wednesday'', -2 \";s:6:\"enable\";s:1:\"1\";}i:1;a:5:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:102:\"SELECT ''Thursday'', 1\r\nUNION SELECT ''Friday'', 3 \r\nUNION SELECT ''Saturday'', 2 \r\nUNION SELECT ''Sunday'', 4\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 10");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 300, 1, 10, 'Sample Bar Chart 3: Three plots stacked', 'Sample Stacked Bar Chart', '', '', '', 'E6E5E5', 600, 320, '', '', '', '', 3, 1, 0, 0, 40, '', '', '', 0, -1, '', '', '', 10, 9, ',bar:{groupWidth:45},chartArea:{left:''7%'',top:''10%'',width:''90%'',height:''80%''}', 'a:3:{i:0;a:5:{s:6:\"legend\";s:6:\"Apples\";s:6:\"colour\";s:6:\"00CC00\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:51:\"SELECT ''2010'', 3559.45\r\nUNION SELECT ''2012'', 2401.9\";s:6:\"enable\";s:1:\"1\";}i:1;a:5:{s:6:\"legend\";s:7:\"Bananas\";s:6:\"colour\";s:6:\"FFFF00\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:80:\"SELECT ''2010'', 1643.1\r\nUNION SELECT ''2012'', 1128.98\r\nUNION SELECT ''2009'', 5362.4\";s:6:\"enable\";s:1:\"1\";}i:2;a:5:{s:6:\"legend\";s:7:\"Oranges\";s:6:\"colour\";s:6:\"FAA500\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:108:\"SELECT ''2010'',2403.9\r\nUNION SELECT ''2009'', 1883.6\r\nUNION SELECT ''2012'', 3994.3\r\nUNION SELECT ''2011'', 2994.77\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), '2013-01-16')");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 11");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 100, 0, 11, 'Sample Line Chart', 'Sample Line Chart', '', '', '', 'FFFAE0', 700, 300, '', '', '', '', 2, 1, 0, 0, 10, 'X axis title', '0', '5', 20, 10, 'Y axis title', '0', '6', 20, 7, ',curveType:''function''', 'a:2:{i:0;a:5:{s:6:\"legend\";s:6:\"Plot 1\";s:6:\"colour\";s:6:\"FF0000\";s:5:\"style\";s:1:\"0\";s:5:\"query\";s:51:\"SELECT 1, 3\r\nUNION SELECT 2.5, 1\r\nUNION SELECT 4, 4\";s:6:\"enable\";s:1:\"1\";}i:1;a:5:{s:6:\"legend\";s:6:\"Plot 2\";s:6:\"colour\";s:6:\"0A33FF\";s:5:\"style\";s:1:\"0\";s:5:\"query\";s:91:\"SELECT 0.5, 2\r\nUNION SELECT 1, 3\r\nUNION SELECT 2.5, 5\r\nUNION SELECT 4,4\r\nUNION SELECT 4.5,3\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 12");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 110, 0, 12, 'Sample Area Chart', 'Sample Area Chart', 'FF0F0F', '0', '', '', 600, 340, '', '', '', '', 2, 0, 0, 0, 30, 'X axis title', '', '', 20, 10, 'Y axis title', '0', '5', 10, 7, '', 'a:2:{i:0;a:5:{s:6:\"legend\";s:6:\"Plot 1\";s:6:\"colour\";s:6:\"FF0000\";s:5:\"style\";s:1:\"0\";s:6:\"enable\";s:1:\"1\";s:5:\"query\";s:93:\"SELECT 1 as X, 3 as Y\rUNION SELECT 2, 0\rUNION SELECT 3, 3\rUNION SELECT 4, 4\rUNION SELECT 4, 4\";}i:1;a:5:{s:6:\"legend\";s:6:\"Plot 2\";s:6:\"colour\";s:6:\"2BFF0A\";s:5:\"style\";s:1:\"0\";s:6:\"enable\";s:1:\"1\";s:5:\"query\";s:68:\"SELECT 4, 1\rUNION SELECT 3, 4\rUNION SELECT 2, 2.5 \rUNION SELECT 1, 4\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 13");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 200, 0, 13, 'Sample Scatter Chart', 'Sample Scatter Chart', 'FF0F0F', '', '', 'FFFAE0', 700, 300, '', '', '', '', 2, 1, 0, 0, 40, 'X axis', '0', '6', 10, 7, 'Y axis', '0', '6', 10, 7, '', 'a:2:{i:0;a:5:{s:6:\"legend\";s:6:\"Plot 1\";s:6:\"colour\";s:6:\"FF0000\";s:5:\"style\";s:1:\"0\";s:5:\"query\";s:89:\"SELECT 1, 1\r\nUNION SELECT 2, 2\r\nUNION SELECT 3, 3.1\r\nUNION SELECT 4, 4\r\nUNION SELECT 5, 5\";s:6:\"enable\";s:1:\"1\";}i:1;a:5:{s:6:\"legend\";s:6:\"Plot 2\";s:6:\"colour\";s:6:\"0000FF\";s:5:\"style\";s:1:\"0\";s:5:\"query\";s:89:\"SELECT 1, 5\r\nUNION SELECT 2, 4\r\nUNION SELECT 3, 2.9\r\nUNION SELECT 4, 2\r\nUNION SELECT 5, 1\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 14");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 500, 0, 14, 'Sample Guages', '', '', '0', '', 'FFFAE0', 400, 200, '', '', '', '', 1, 0, 0, 0, 0, 'X axis title', '', '', 20, 10, 'Y axis title', '0', '100', 10, 7, ',redFrom:90,redTo:100,yellowFrom:75,yellowTo:90,minorTicks:10', 'a:1:{i:0;a:5:{s:6:\"legend\";b:0;s:6:\"colour\";b:0;s:5:\"style\";s:1:\"0\";s:6:\"enable\";s:1:\"1\";s:5:\"query\";s:46:\"SELECT ''Temp'', 42\rUNION SELECT ''Pressure'', 65\r\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 15");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 1,   0, 15, 'Sample Table: Recently updated articles', 'Recently Updated Articles', '', '', 'a:13:{s:8:\"pl_table\";s:6:\"plots1\";s:8:\"pl_title\";s:5:\"title\";s:7:\"pl_head\";s:7:\"heading\";s:6:\"pl_odd\";s:3:\"odd\";s:7:\"pl_even\";s:4:\"even\";s:7:\"gv_head\";s:0:\"\";s:6:\"gv_odd\";s:0:\"\";s:6:\"gv_row\";s:0:\"\";s:11:\"gv_selected\";s:0:\"\";s:8:\"gv_hover\";s:0:\"\";s:8:\"gv_hcell\";s:0:\"\";s:8:\"gv_tcell\";s:0:\"\";s:10:\"gv_numcell\";s:0:\"\";}', '', 512, 256, '', '', '', '', 1, 0, 0, 0, 1, '', '', '', 0, -1, '', '', '', 0, 10, '', 'a:1:{i:0;a:5:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:226:\"SELECT title AS Article, date(modified) AS Updated,\r\nCONCAT(''<a target=\"_blank\" href=\"%%J_ROOT_URI%%/index.php?option=com_content&view=article&id='',id,''\">'',title,''</a>'') AS Link\r\nFROM #__content ORDER BY modified DESC LIMIT 10\";s:6:\"enable\";s:0:\"\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 16");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 10,  0, 16, 'Sample Single Item: Your most recently modified article', '', '', '', '', '', 512, 1, '', '', '', '', 1, 0, 0, 0, 0, '', '', '', 0, -1, '', '', '', 0, -1, '', 'a:1:{i:0;a:5:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:104:\"SELECT Concat(\"Your most recently modified article is \",title) FROM #__content ORDER BY modified LIMIT 1\";s:6:\"enable\";s:0:\"\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 17");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 20,  1, 17, 'Sample Table: The jos_content table', '', '', '', 'a:13:{s:8:\"pl_table\";s:6:\"plots1\";s:8:\"pl_title\";s:5:\"title\";s:7:\"pl_head\";s:7:\"heading\";s:6:\"pl_odd\";s:3:\"odd\";s:7:\"pl_even\";s:4:\"even\";s:7:\"gv_head\";s:0:\"\";s:6:\"gv_odd\";s:0:\"\";s:6:\"gv_row\";s:0:\"\";s:11:\"gv_selected\";s:0:\"\";s:8:\"gv_hover\";s:0:\"\";s:8:\"gv_hcell\";s:0:\"\";s:8:\"gv_tcell\";s:0:\"\";s:10:\"gv_numcell\";s:0:\"\";}', '', 1000, 380, '', '', '', '', 1, 0, 0, 0, 1, '', '', '', 0, -1, '', '', '', 0, 250, '', 'a:1:{i:0;a:5:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"style\";s:0:\"\";s:5:\"query\";s:24:\"select * from #__content\";s:6:\"enable\";s:0:\"\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 18");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 520, 0, 18, 'Sample Timeline', '', '', '', '', 'F6FFF0', 1000, 256, '', '', '', '', 1, 1, 0, 0, 0, '', '', '', 0, -1, '', '', '', 10, -1, '', 'a:1:{i:0;a:4:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"query\";s:364:\"Select ''Row 1'', ''Bar 1'', UNIX_TIMESTAMP(''2014-01-01'' ), UNIX_TIMESTAMP(''2014-01-31'' )\r\nUnion select ''Row 1'', ''Bar 2'', UNIX_TIMESTAMP(''2014-02-01'' ), UNIX_TIMESTAMP(''2014-02-28'' )\r\nUnion select ''Row 2'', ''Bar 3'', UNIX_TIMESTAMP(''2014-01-15'' ), UNIX_TIMESTAMP(''2014-02-15'' )\r\nUnion select ''Row 3'', ''Bar 1'', UNIX_TIMESTAMP(''2014-02-15'' ), UNIX_TIMESTAMP(''2014-03-08'' )\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");
			
	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 19");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 530, 0, 19, 'Sample Bubble Chart', 'Sample Bubble Chart', '1C03FF', '', '', 'F8F2FF', 800, 400, '', '', '', '', 1, 1, 0, 0, 40, 'X title', '0', '90', 0, -1, 'Y title', '0', '100', 300, -1, ',colorAxis:{colors: [''blue'', ''red''], legend:{position:''top''}}', 'a:1:{i:0;a:4:{s:6:\"legend\";s:0:\"\";s:6:\"colour\";s:0:\"\";s:5:\"query\";s:154:\"select '''',  10 as X,  30 as Y,1 as Colour, 20 as Size\r\nunion select ''B'',   20, 50, 2, 30\r\nunion select ''C'', 50, 80, 3, 40\r\nunion select ''D'', 70, 60, 4, 50\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");

	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 20");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 550, 0, 20, 'Sample Combo Chart: Numeric X axis', '', '', '', '', '', 650, 350, '', '', '', '', 3, 1, 0, 0, 20, '', '', '', 180, 7, '', '', '', 10, 7, '', 'a:3:{i:0;a:5:{s:6:\"legend\";s:6:\"Data 1\";s:6:\"colour\";s:6:\"00FF00\";s:5:\"style\";s:2:\"60\";s:5:\"query\";s:440:\"select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 7 DAY)),20\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 6 DAY)),44\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 5 DAY)),82\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 4 DAY)),67\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 3 DAY)),51\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 2 DAY)),42\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 1 DAY)),55\";s:6:\"enable\";s:1:\"1\";}i:1;a:5:{s:6:\"legend\";s:6:\"Data 2\";s:6:\"colour\";s:6:\"0000FF\";s:5:\"style\";s:2:\"50\";s:5:\"query\";s:376:\"select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 7 DAY)),22\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 6 DAY)),41\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 5 DAY)),31\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 4 DAY)),36\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 3 DAY)),44\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 1 DAY)),48\";s:6:\"enable\";s:1:\"1\";}i:2;a:5:{s:6:\"legend\";s:6:\"Data 3\";s:6:\"colour\";s:6:\"FF0000\";s:5:\"style\";s:1:\"0\";s:5:\"query\";s:376:\"select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 7 DAY)),42\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 6 DAY)),71\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 5 DAY)),54\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 4 DAY)),73\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 3 DAY)),59\r\nunion select UNIX_TIMESTAMP(date_sub(now(),INTERVAL 1 DAY)),68\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");

	$count = $this->ladb_loadResult("SELECT count(*) FROM #__plotalot WHERE `sample_id` = 21");
	if ($count == 0)
		$this->ladb_execute("INSERT INTO `#__plotalot` (`published`, `chart_type`, `chart_option`, `sample_id`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`, `styles`, `back_colour`, `x_size`, `y_size`, `db_host`, `db_name`, `db_user`, `db_pass`, `num_plots`, `show_grid`, `show_raw_data`, `show_script`, `legend_type`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`, `y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `plots`, `date_created`, `date_updated`) VALUES
			(1, 540, 0, 21, 'Sample Combo Chart: Alphabetic X axis', 'Monthly Coffee Production by Country', '1808FF', '', '', 'FFFFFF', 900, 500, '', '', '', '', 6, 1, 0, 0, 20, 'Month', '', '', 10, -1, 'Cups', '', '', 10, -1, '',  'a:6:{i:0;a:5:{s:6:\"legend\";s:7:\"Bolivia\";s:6:\"colour\";s:6:\"3D44FF\";s:5:\"style\";s:2:\"60\";s:5:\"query\";s:132:\"select ''2004/05'',165 union\r\nselect ''2005/06'',135 union\r\nselect ''2006/07'',157 union\r\nselect ''2007/08'',139 union\r\nselect ''2008/09'',136\";s:6:\"enable\";s:1:\"1\";}i:1;a:5:{s:6:\"legend\";s:7:\"Ecuador\";s:6:\"colour\";s:6:\"FF213F\";s:5:\"style\";s:2:\"60\";s:5:\"query\";s:135:\"select ''2004/05'',938 union\r\nselect ''2005/06'',1120 union\r\nselect ''2006/07'',1167 union\r\nselect ''2007/08'',1110 union\r\nselect ''2008/09'',691\";s:6:\"enable\";s:1:\"1\";}i:2;a:5:{s:6:\"legend\";s:10:\"Madagascar\";s:6:\"colour\";s:6:\"FF7417\";s:5:\"style\";s:2:\"60\";s:5:\"query\";s:132:\"select ''2004/05'',522 union\r\nselect ''2005/06'',599 union\r\nselect ''2006/07'',587 union\r\nselect ''2007/08'',615 union\r\nselect ''2008/09'',629\";s:6:\"enable\";s:1:\"1\";}i:3;a:5:{s:6:\"legend\";s:16:\"Papua New Guinea\";s:6:\"colour\";s:6:\"45AD00\";s:5:\"style\";s:2:\"60\";s:5:\"query\";s:134:\"select ''2004/05'',998 union\r\nselect ''2005/06'',1268 union\r\nselect ''2006/07'',807 union\r\nselect ''2007/08'',968 union\r\nselect ''2008/09'',1026\";s:6:\"enable\";s:1:\"1\";}i:4;a:5:{s:6:\"legend\";s:6:\"Rwanda\";s:6:\"colour\";s:6:\"791585\";s:5:\"style\";s:2:\"60\";s:5:\"query\";s:132:\"select ''2004/05'',450 union\r\nselect ''2005/06'',288 union\r\nselect ''2006/07'',397 union\r\nselect ''2007/08'',215 union\r\nselect ''2008/09'',366\";s:6:\"enable\";s:1:\"1\";}i:5;a:5:{s:6:\"legend\";s:7:\"Average\";s:6:\"colour\";s:6:\"FF00CC\";s:5:\"style\";s:2:\"40\";s:5:\"query\";s:138:\"select ''2004/05'',614.6 union\r\nselect ''2005/06'',682 union\r\nselect ''2006/07'',623 union\r\nselect ''2007/08'',609.4 union\r\nselect ''2008/09'',569.6\";s:6:\"enable\";s:1:\"1\";}}', CURRENT_DATE(), CURRENT_DATE())");
;

}

//-------------------------------------------------------------------------------
// Add sample_id's for sample charts created before version 4.00
//
function add_sample_ids()
{
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 1  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Pie Chart - Your Most Popular Articles'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 2  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Bar Chart'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 3  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Line Graph'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 4  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Bar Chart with 2 plots'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 5  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Table'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 6  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Pie Chart 1: Your most popular articles'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 7  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Pie Chart 2'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 8  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Bar Chart 1'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 9  WHERE `sample_id` = 0 AND `chart_name` = 'Sample Bar Chart 2: Two plots'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 10 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Bar Chart 3: Three plots stacked'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 11 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Line Chart'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 12 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Area Chart'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 13 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Scatter Chart'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 14 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Guages'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 15 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Table: Recently updated articles'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 16 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Single Item: Your most recently modified article'");
	$this->ladb_execute("UPDATE `#__plotalot` SET `sample_id` = 17 WHERE `sample_id` = 0 AND `chart_name` = 'Sample Table: The jos_content table'");
}

//-------------------------------------------------------------------------------
// Upgrade the chart table to version 3.00 for the new Google Visualization API
//
function upgrade3()
{
	$this->add_column('#__plotalot', 'chart_option', "SMALLINT NOT NULL DEFAULT '0' AFTER `chart_type`");
	$this->add_column('#__plotalot', 'date_created', "DATE NOT NULL DEFAULT '".date('Y-m-d')."'");
	$this->add_column('#__plotalot', 'date_updated', "DATE NOT NULL DEFAULT '".date('Y-m-d')."'");
	$this->add_column('#__plotalot', 'show_script',  "tinyint(4) NOT NULL default '0'  AFTER `show_raw_data`");
	$this->ladb_execute_ignore("ALTER TABLE `#__plotalot` DROP `back_style`");
	$this->ladb_execute_ignore("ALTER TABLE `#__plotalot` DROP `back_colour_2`");
	$this->ladb_execute("ALTER TABLE `#__plotalot` CHANGE `chart_css_style` `styles` TEXT");

	$this->ladb_execute("UPDATE `#__plotalot` SET `y_labels` = `y_size` WHERE `chart_type`=1 AND `y_labels`=-1");	// for tables move max_rows to y_labels

	$rows = $this->ladb_loadObjectList("SELECT * FROM #__plotalot");
	if ($rows === false)
		{
		echo "rows false<br>";
		return false;
		}

	foreach ($rows as $row)
		{
		echo "Upgrading ".$row->chart_name;
		$plot_array = array();
		$this->expand_old_plots($row->plots, $plot_array);	// expand the old format plot data
		foreach ($plot_array as $plot)						// the dashed plot styles are no longer supported
			if ((isset($plot['style']))
			and (($plot['style'] == 10) or ($plot['style'] == 30) or ($plot['style'] == 50)))
				$plot['style'] = 0;
		$row->plots = serialize($plot_array);				// create the new format plot data
		$style_array = array();
		$query = "UPDATE `#__plotalot` SET `plots` = ".$this->_db->Quote($row->plots);
		if (substr($row->extra_parms,0,1) == '&')
			$query .= ",extra_parms=''";
		if ($row->chart_type == 1)							// for Plotalot tables ...
			{												// create the new format style data
			$style_array['pl_table'] = $this->change_class($row->styles);
			$style_array['pl_title'] = $this->change_class($row->y_start);
			$style_array['pl_head'] = $this->change_class($row->y_end);
			$style_array['pl_odd'] = $this->change_class($row->x_start);
			$style_array['pl_even'] = $this->change_class($row->x_end);
			$row->styles = serialize($style_array);
			$query .= ",`styles` = ".$this->_db->Quote($row->styles).",y_start='',y_end='',x_start='',x_end='',`x_size`=512,`y_size`=256";
			}
		else
			$query .= ",`styles`=''";
			
		if (($row->chart_type == 300) or ($row->chart_type == 310) or ($row->chart_type == 320) or ($row->chart_type == 330))
			if ($row->x_format == 900)				// bar charts with X FORMAT_ORDERED
				$query .= ",`chart_option`=1, `x_format`=0";	// are now handled by chart_option = 1
				
		$query .= ",`state`=1 WHERE `id` = ".$row->id;
		$result = $this->ladb_execute($query);
		if ($result === false)
			return false;
		echo " - done<br />";
		}
}

//-------------------------------------------------------------------------------
// for tables, change class="xxxx" to just xxxx
//
function change_class($str)
{
	if (substr($str,0,5) != 'class')	// if it's not 'class' just blank it out
		return '';
	$str = substr($str,5);				// strip off the 'class'
	return trim($str,' "=');			// strip spaces, = and "
}

//-------------------------------------------------------------------------------
// expand the old format plots field
//
function expand_old_plots(&$source, &$dest)
{
	$source = stripslashes($source);
	$params = explode("\n",$source);
	foreach ($params as $param)
		{
		$p = strpos($param,"=");
		if ($p === false)
			continue;			// should never happen
		$key = substr($param,0,$p);
		$value = substr($param,$p+1);
		$field_num = substr($key,5);
		$q = strrpos($field_num,"_");
		$plot_num = substr($field_num,$q+1);
		$field_name = substr($field_num,0,$q);
		$dest[$plot_num][$field_name] = $value;
		}
}

//===============================================================================
// Generic functions common to all our installers
//===============================================================================

//-------------------------------------------------------------------------------
// Check whether a table exists in the database. Returns TRUE if exists, FALSE if it doesn't
//
function table_exists($table)
{
	$tables = $this->_db->getTableList();
	$table = self::replaceDbPrefix($table);
	if (self::in_arrayi($table,$tables))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Check whether a column exists in a table. Returns TRUE if exists, FALSE if it doesn't
//
function column_exists($table, $column)
{
	$fields = $this->_db->getTableColumns($table);
		
	if ($fields === null)
		return false;
		
	if (array_key_exists($column,$fields))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Add a column if it doesn't exist (the table must exist)
//
function add_column($table, $column, $details)
{
	if ($this->column_exists($table, $column))
		return;
	$query = 'ALTER TABLE `'.$table.'` ADD `'.$column.'` '.$details;
	return $this->ladb_execute($query);
}

//-------------------------------------------------------------------------------
// Replace the generic database prefix #__ with the real one
//
static function replaceDbPrefix($sql)
{
	$app = JFactory::getApplication();
	$dbprefix = $app->getCfg('dbprefix');
	return str_replace('#__',$dbprefix,$sql);
}

//-------------------------------------------------------------------------------
// Case insensitive in_array()
//
static function in_arrayi($needle, $haystack)
{
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

//-------------------------------------------------------------------------------
// Execute a SQL query and return true if it worked, false if it failed
//
function ladb_execute($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		if ($this->_db->getErrorNum())
			{
			echo '<div style="color:red">'.$this->_db->stderr().'</div>';
			return false;
			}
		return true;
		}
		
// for Joomla 3.0 use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
	    echo '<div style="color:red">'.$e->getMessage().'</div>';
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// Execute a SQL query ignoring any errors
//
function ladb_execute_ignore($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		return;
		}
		
// for Joomla 3.0 use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
		return;
		}
	return;
}

//-------------------------------------------------------------------------------
// Get a single value from the database as an object and return it, or false if it failed
//
function ladb_loadResult($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		if ($this->_db->getErrorNum())
			{
			echo '<div style="color:red">'.$this->_db->stderr().'</div>';
			return false;
			}
		return $result;
		}

// for Joomla 3.0 use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		}
	catch (RuntimeException $e)
		{
	    echo '<div style="color:red">'.$e->getMessage().'</div>';
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Get an array of rows from the database and return it, or false if it failed
//
function ladb_loadObjectList($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($this->_db->getErrorNum())
			{
			echo '<div style="color:red">'.$this->_db->stderr().'</div>';
			return false;
			}
		return $result;
		}

// for Joomla 3.0 use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		}
	catch (RuntimeException $e)
		{
	    echo '<div style="color:red">'.$e->getMessage().'</div>';
		return false;
		}
	return $result;
}

} // class
