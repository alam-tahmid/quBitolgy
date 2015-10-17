<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

class JFormFieldColor extends JFormFieldText
{

	protected $type = 'Color';

	public function getInput ()
	{
		JFactory::getDocument()->addScript(JURI::base() . 'components/com_ganalytics/libraries/jscolor/jscolor.js');
		return parent::getInput();
	}

	public function setup (SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		$this->element['class'] = $this->element['class'] . ' color';
		return $return;
	}
}