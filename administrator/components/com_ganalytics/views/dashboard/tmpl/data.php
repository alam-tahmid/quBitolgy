<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ganalytics'.DS.'libraries'.DS.'gviz'.DS.'gviz_api.php');

$data = isset($this->data) ? $this->data : null;
echo GAnalyticsDataHelper::convertToJsonResponse($this->profile, $data, $this->statsView->type);