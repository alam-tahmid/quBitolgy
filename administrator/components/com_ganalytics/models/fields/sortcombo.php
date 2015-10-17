<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');

class JFormFieldSortcombo extends JFormFieldList
{

	protected $type = 'Sortcombo';

	protected function getInput ()
	{
		$params = '';
		if (isset($this->element['params-field']))
		{
			$params = 'params_';
		}
		$prefix = $this->form->getFormControl();
		if (! empty($prefix))
		{
			$prefix .= '_';
		}
		$id = $prefix . $params . $this->element['name'];

		GAnalyticsHelper::loadjQuery();
		$document = JFactory::getDocument();
		$document->addScript(JURI::base() . 'components/com_ganalytics/libraries/jquery/multiselect/jquery.multiselect2side.js');
		$document->addStyleSheet(JURI::base() . 'components/com_ganalytics/libraries/jquery/multiselect/jquery.multiselect2side.css');
		$document->addScript(JURI::base() . 'components/com_ganalytics/models/fields/sortcombo.js');

		return parent::getInput();
	}

	protected function getOptions ()
	{
		if (empty($this->value))
		{
			return array();
		}
		if (! is_array($this->value))
		{
			return array(
					JHtml::_('select.option', $this->value)
			);
		}
		return array(
				JHtml::_('select.option', implode(',', $this->value))
		);
	}
}