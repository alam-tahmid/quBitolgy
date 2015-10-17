<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('components.com_ganalytics.libraries.ganalytics.view', JPATH_ADMINISTRATOR);

class GAnalyticsViewImport extends GAnalyticsView {

	protected $title = 'COM_GANALYTICS_MANAGER_GANALYTICS';

	protected $items = null;

	public function init() {
		if (JRequest::getVar('code', null) == null) {
			header('Location: ' . GAnalyticsDataHelper::getClient()->createAuthUrl());
		} else {
			$this->items = $this->get('Items');
		}
	}

	protected function addToolbar() {
		if ($this->items !== null) {
			$canDo = GAnalyticsHelper::getActions();
			if ($canDo->get('core.create')){
				JToolBarHelper::custom('import.save', 'new.png', 'new.png', 'add', false);
			}
			JToolBarHelper::cancel('import.cancel', 'JTOOLBAR_CANCEL');
		}
		parent::addToolbar();
	}
}