<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

echo GAnalyticsDataHelper::convertToJsonResponse($this->profile, $this->data, JRequest::getVar('source') == 'referrer' ? 'list' : 'image');