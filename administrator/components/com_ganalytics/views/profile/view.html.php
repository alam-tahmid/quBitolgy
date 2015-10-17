<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('components.com_ganalytics.libraries.ganalytics.view', JPATH_ADMINISTRATOR);

class GAnalyticsViewProfile extends GAnalyticsView {

	protected $form = null;
	protected $profile = null;

	public function init(){
		$this->form = $this->get('Form');
		$this->profile = $this->get('Item');
	}

	public function addToolbar() {
		JRequest::setVar('hidemainmenu', true);
		$canDo = GAnalyticsHelper::getActions();
		if ($canDo->get('core.edit')) {
			JToolBarHelper::apply('profile.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('profile.save', 'JTOOLBAR_SAVE');
		}
		JToolBarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');

		parent::addToolbar();
	}
}
