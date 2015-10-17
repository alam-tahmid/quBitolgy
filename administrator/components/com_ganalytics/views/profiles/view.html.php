<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('components.com_ganalytics.libraries.ganalytics.view', JPATH_ADMINISTRATOR);

class GAnalyticsViewProfiles extends GAnalyticsView {

	protected $items = null;
	protected $pagination = null;

	public function init() {
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
	}

	protected function addToolBar() {
		$canDo = GAnalyticsHelper::getActions();

		if ($canDo->get('core.create')) {
			JToolBarHelper::custom('import.import', 'upload.png', 'upload.png', JText::_('COM_GANALYTICS_PROFILES_VIEW_IMPORT_BUTTON'), false);
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('profile.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'profiles.delete', 'JTOOLBAR_DELETE');
		}
		parent::addToolbar();
	}
}