<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class GAnalyticsViewPages extends JViewLegacy {

	public function display($tpl = null){
		switch(JRequest::getVar('source', 'date')) {
			case 'date':
				$this->data = $this->get('DateStats');
				break;
			case 'browser':
				$this->data = $this->get('BrowserStats');
				break;
			case 'country':
				$this->data = $this->get('CountryStats');
				break;
			default: $this->data = array();
		}
		$this->profile = $this->get('Profile');
		parent::display($tpl);
	}
}