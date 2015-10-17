<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

if (! defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

JLoader::import('components.com_ganalytics.helper.data', JPATH_ADMINISTRATOR);
if (file_exists(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ganalytics' . DS . 'helper' . DS . 'proutil.php'))
{
	JLoader::import('components.com_ganalytics.helper.proutil', JPATH_ADMINISTRATOR);
}

if (! class_exists('Mustache'))
{
	JLoader::import('components.com_ganalytics.libraries.mustache.Mustache', JPATH_ADMINISTRATOR);
}

JLoader::import('joomla.environment.browser');

JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ganalytics' . DS . 'tables');
JLoader::import('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ganalytics' . DS . 'models', 'GAnalyticsModel');

class GAnalyticsHelper
{

	private static $daysLong = null;

	private static $daysShort = null;

	private static $daysMin = null;

	private static $monthsLong = null;

	private static $monthsShort = null;

	public static function render ($output, $variables)
	{
		try
		{
			$m = new Mustache();
			return $m->render($output, $variables);
		}
		catch (Exception $e)
		{
			return '';
		}
	}

	public static function translate ($string)
	{
		if (empty($string))
		{
			return '';
		}
		if (! JFactory::getLanguage()->hasKey('COM_GANALYTICS'))
		{
			JFactory::getLanguage()->load('com_ganalytics', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ganalytics');
		}
		$string = str_replace(' ', '_', $string);
		$string = str_replace('(', '_', $string);
		$string = str_replace(')', '_', $string);

		return JText::_('COM_GANALYTICS_' . $string);
	}

	public static function getComponentParameter ($key, $default = null)
	{
		$params = JComponentHelper::getParams('com_ganalytics');
		return $params->get($key, $default);
	}

	public static function convertCountryNameToISO ($countryName)
	{
		static $countrys;
		if ($countrys == null)
		{
			$countrys = parse_ini_file(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_ganalytics' . DS . 'helper' . DS . 'countrys.txt');
		}
		$upperName = strtoupper($countryName);
		if (isset($countrys[$upperName]))
		{
			return $countrys[$upperName];
		}
		return '';
	}

	public static function isPROMode ()
	{
		return class_exists('GAnalyticsProUtil');
	}

	public static function getFadedColor ($color, $percentage = 85)
	{
		$percentage = 100 - $percentage;
		$rgbValues = array_map('hexDec', str_split(ltrim($color, '#'), 2));

		for ($i = 0, $len = count($rgbValues); $i < $len; $i ++)
		{
			$rgbValues[$i] = decHex(floor($rgbValues[$i] + (255 - $rgbValues[$i]) * ($percentage / 100)));
		}

		return implode('', $rgbValues);
	}

	public static function getActions ($statsViewId = 0)
	{
		$user = JFactory::getUser();
		$result = new JObject();

		if (empty($statsViewId))
		{
			$assetName = 'com_analytics';
		}
		else
		{
			$assetName = 'com_ganalytics.statsview.' . (int) $statsViewId;
		}

		$actions = array(
				'core.admin',
				'core.manage',
				'core.create',
				'core.edit',
				'core.edit.state',
				'core.edit.own',
				'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	public static function isJoomlaVersion ($version)
	{
		$j = new JVersion();
		return substr($j->RELEASE, 0, strlen($version)) == $version;
	}

	public static function loadjQuery ()
	{
		if (JFactory::getDocument()->getType() != 'html')
		{
			return;
		}

		if (self::isJoomlaVersion('2.5'))
		{
			JFactory::getDocument()->addScript(JURI::root() . 'administrator/components/com_ganalytics/libraries/jquery/jquery.min.js');
		}
		else
		{
			JHtml::_('jquery.framework');
		}
		JFactory::getDocument()->addScript(JURI::root() . 'administrator/components/com_ganalytics/libraries/jquery/ganalytics/gaNoConflict.js');
	}

	public static function trim ($text)
	{
		return preg_replace('#^(.{30})(.{6,})(.{20})$#u', '\1.....\3', $text);
	}
}