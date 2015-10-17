<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');
	
class GAnalyticsViewHelp extends JViewLegacy {

	public function display($tpl = null) {
		JToolBarHelper::title(JText::_('COM_GANALYTICS_HELP_VIEW_TITLE'), 'analytics');
		parent::display($tpl);
	}
}