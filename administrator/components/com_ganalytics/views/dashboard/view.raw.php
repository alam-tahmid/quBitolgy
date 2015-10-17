<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class GAnalyticsViewDashboard extends JViewLegacy {

	public function display($tpl = null){
		$this->statsView = $this->get('StatsView');
		if(JRequest::getVar('layout') == 'data'){
			$this->data = $this->get('StatsData');
		}
		$this->profile = $this->get('Profile');
		parent::display($tpl);
	}
}