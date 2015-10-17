<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

class GAnalyticsControllerStatsView extends JControllerForm {

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->view_list = 'config';
	}
}