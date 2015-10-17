<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

require_once (JPATH_ADMINISTRATOR . '/components/com_ganalytics/helper/ganalytics.php');

JFormHelper::loadFieldClass('groupedlist');

class JFormFieldDimensionsCombo extends JFormFieldGroupedList
{

	protected $type = 'DimensionsCombo';

	private $notLoad = false;

	protected function getGroups ()
	{
		JFactory::getLanguage()->load('com_ganalytics');

		if ($this->notLoad)
		{
			return array(
					array(
							JHtml::_('select.option', $this->value)
					)
			);
		}

		if ($this->value == null)
		{
			$this->value = array();
		}
		if (! is_array($this->value))
		{
			$this->value = array(
					$this->value
			);
		}

		GAnalyticsHelper::loadjQuery();
		JFactory::getDocument()->addScript(JURI::base() . 'components/com_ganalytics/libraries/jquery/multiselect/jquery.multiselect2side.js');
		JFactory::getDocument()->addStyleSheet(JURI::base() . 'components/com_ganalytics/libraries/jquery/multiselect/jquery.multiselect2side.css');
		JFactory::getDocument()->addScriptDeclaration("gajQuery(document).ready(function(){createMultiSelectCombo(gajQuery('#" . $this->id . "'));});");

		$groups = array(
				'' => array()
		);

		foreach ($this->value as $value)
		{
			$groups[''][] = JHtml::_('select.option', $value, GAnalyticsHelper::translate($value), 'value', 'text');
		}

		$xml = new SimpleXMLElement(JFile::read(JPATH_ADMINISTRATOR . '/components/com_ganalytics/helper/dimensions.xml'));
		foreach ($xml->div as $group)
		{
			$label = GAnalyticsHelper::translate($group->div);

			if (! isset($groups[$label]))
			{
				$groups[$label] = array();
			}

			foreach ($group->label as $value)
			{
				$v = (string) $value->attributes()->for;
				if (in_array($v, $this->value))
				{
					continue;
				}
				$groups[$label][] = JHtml::_('select.option', $v, GAnalyticsHelper::translate($v), 'value', 'text');
			}
		}
		return $groups;
	}

	public function setNotLoad ()
	{
		$this->notLoad = true;
	}
}