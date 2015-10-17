<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class GAnalyticsViewPage extends JView {

	public function display($tpl = null) {
		$this->profiles = $this->get('Profiles');
		$this->state = $this->get('State');
		$this->entry = $this->get('Entry');

		JFactory::getLanguage()->load('com_ganalytics', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ganalytics');

		parent::display($tpl);
	}
}