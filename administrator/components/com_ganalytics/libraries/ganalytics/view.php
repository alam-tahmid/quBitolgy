<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

class GAnalyticsView extends JViewLegacy {

	protected $icon = 'analytics';
	protected $title = '';

	public function display($tpl = null) {
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if (GAnalyticsHelper::isJoomlaVersion('2.5')) {
			$this->setLayout($this->getLayout().'_25');
		}
		if (GAnalyticsHelper::isJoomlaVersion('3')) {
			$this->setLayout($this->getLayout().'_3');
		}

		$this->init();

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		$canDo	= GAnalyticsHelper::getActions();

		if (empty($this->title)) {
			$this->title = 'COM_GANALYTICS_'.strtoupper($this->getName()).'_VIEW_TITLE';
		}
		if (empty($this->icon)) {
			$this->icon = strtolower($this->getName());
		}
		JToolBarHelper::title(JText::_($this->title), $this->icon);
// 		JFactory::getDocument()->addStyleDeclaration('.icon-48-'.$this->icon.' {background-image: url(../media/com_ganalytics/images/admin/48-'.$this->icon.'.png);background-repeat: no-repeat;}');

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_ganalytics', 500);
			JToolBarHelper::divider();
		}
	}

	protected function init() {
	}
}