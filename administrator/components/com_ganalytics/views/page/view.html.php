<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('components.com_ganalytics.libraries.ganalytics.view', JPATH_ADMINISTRATOR);

class GAnalyticsViewPage extends GAnalyticsView {

	protected $profiles = null;
	protected $state = null;
	protected $entry = null;

	public function init() {
		$this->profiles = $this->get('Profiles');
		$this->state = $this->get('State');
		$this->entry = $this->get('Entry');
	}
}