<?php
/********************************************************************
Product		: Plotalot
Date		: 15 April 2014
Copyright	: Les Arbres Design 2014
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define("LAP_TRACE_FILE_NAME", 'trace.txt');
define("LAP_TRACE_FILE_PATH", JPATH_ROOT.'/components/com_plotalot/trace.txt');
define("LAP_TRACE_FILE_URL",  JURI::root().'components/com_plotalot/trace.txt');
define("LAP_MAX_TRACE_SIZE",  1000000);		// maximum trace file size (about 1Mb)
define("LAP_MAX_TRACE_AGE",   21600);		// maximum trace file age in seconds (6 hours)
define("LAP_UTF8_HEADER",     "\xEF\xBB\xBF");	// UTF8 file header

if (class_exists("LAP_trace"))
	return;

class LAP_trace
{

//-------------------------------------------------------------------------------
// Write an entry to the trace file
// Tracing is ON if the trace file exists
//
static function trace($data)
{
	if (@!file_exists(LAP_TRACE_FILE_PATH))
		return;
	if (filesize(LAP_TRACE_FILE_PATH) > LAP_MAX_TRACE_SIZE)
		{
		@unlink(LAP_TRACE_FILE_PATH);
		@file_put_contents(LAP_TRACE_FILE_PATH, LAP_UTF8_HEADER.date("d/m/y H:i").' New trace file created'."\n");
		}
	@file_put_contents(LAP_TRACE_FILE_PATH, $data."\n",FILE_APPEND);
}

//-------------------------------------------------------------------------------
// Start a new trace file
//
static function init_trace()
{
	self::delete_trace_file();
	@file_put_contents(LAP_TRACE_FILE_PATH, LAP_UTF8_HEADER.date("d/m/y H:i").' Tracing Initialised'."\n");
	
	$locale = setlocale(LC_ALL,0);
	$locale_string = print_r($locale, true);
	$langObj = JFactory::getLanguage();
	$language = $langObj->get('tag');
	$php_version = phpversion();

	self::trace('Plotalot version : '.self::getComponentVersion(), true);
	self::trace('Plotalot plugin  : '.self::getPluginStatus());
	self::trace("PHP version      : ".$php_version, true);
	self::trace("PHP locale       : ".$locale_string);
	self::trace("Server           : ".PHP_OS);
	self::trace("Joomla version   : ".JVERSION);
	self::trace("Joomla language  : ".$language);
}

//-------------------------------------------------------------------------------
// Trace an entry point
// Tracing is ON if the trace file exists
//
static function trace_entry_point($front=false)
{
	if (@!file_exists(LAP_TRACE_FILE_PATH))
		return;
		
// if the trace file is more than 6 hours old, delete it, which will switch tracing off
//  - we don't want trace to be left on accidentally

	$filetime = @filectime(LAP_TRACE_FILE_PATH);
	if (time() > ($filetime + LAP_MAX_TRACE_AGE))
		{
		self::delete_trace_file();
		return;
		}
		
	$date_time = date("d/m/y H:i").' ';	
	
	if ($front)
		self::trace($date_time.'================================ [Front Entry Point] ================================');
	else
		self::trace($date_time.'================================ [Admin Entry Point] ================================');
		
	if ($front)
		{
		if (isset($_SERVER["REMOTE_ADDR"]))
			$ip_address = '('.$_SERVER["REMOTE_ADDR"].')';
		else
			$ip_address = '';

		if (isset($_SERVER["HTTP_USER_AGENT"]))
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
		else
			$user_agent = '';

		if (isset($_SERVER["HTTP_REFERER"]))
			$referer = $_SERVER["HTTP_REFERER"];
		else
			$referer = '';

		self::trace("$ip_address $user_agent");
		if ($referer != '')
			self::trace('Referer: '.$referer, true);
		}

	if (!empty($_POST))
		self::trace("Post data: ".print_r($_POST,true));
	if (!empty($_GET))
		self::trace("Get data: ".print_r($_GET,true));
}

//-------------------------------------------------------------------------------
// Delete the trace file
//
static function delete_trace_file()
{
	if (@file_exists(LAP_TRACE_FILE_PATH))
		@unlink(LAP_TRACE_FILE_PATH);
}

//-------------------------------------------------------------------------------
// Return true if tracing is currently active
//
static function tracing()
{
	if (@file_exists(LAP_TRACE_FILE_PATH))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Make the html for the help and support page
// The controller must contain the trace_on() and trace_off() functions
//
static function make_trace_controls($controller='')
{
	$html = '<div>';
	$html .= 'Diagnostic Trace Mode ';
	$html .= '<img src="'.LAP_ADMIN_ASSETS_URL.'info-16.png" alt="" title="Create a trace file to send to support. Remember to switch off after use." />';
    $onclick = ' onclick="document.adminForm.controller.value=\''.$controller.'\'; document.adminForm.task.value=\'trace_on\'; document.adminForm.submit();"';
    $html .= ' <button  class="la_button"'.$onclick.'>On</button>';
	$onclick = ' onclick="document.adminForm.controller.value=\''.$controller.'\'; document.adminForm.task.value=\'trace_off\'; document.adminForm.submit();"';
    $html .= ' <button  class="la_button"'.$onclick.'>Off</button>';

	if (file_exists(LAP_TRACE_FILE_PATH))
		$html .= ' <a href="'.LAP_TRACE_FILE_URL.'" target="_blank"> Trace File</a>';
	else
		$html .= ' Tracing is currently OFF';

	$html .= '</div>';
	return $html;
}

//-------------------------------------------------------------------------------
// Get the component version from the component manifest XML file
//
static function getComponentVersion()
{
	$xml_array = JInstaller::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR.'/plotalot.xml');
	return $xml_array['version'];
}

//-------------------------------------------------------------------------------
// Get the plugin status
//
static function getPluginStatus()
{
	$plugin_path = '/plugins/content/plotalot/plotalot.xml';

	if (!file_exists(JPATH_ROOT.$plugin_path))
		return 'Not installed';
		
	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ROOT.$plugin_path);
	$version = $xml_array['version'];
		
	if (JPluginHelper::isEnabled('content', 'plotalot'))
		return 'Version '.$version.' installed and enabled';
		
	return 'Version '.$version.' installed but disabled';
}

} // class