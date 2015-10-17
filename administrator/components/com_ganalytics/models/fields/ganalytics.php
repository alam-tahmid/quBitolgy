<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');

class JFormFieldGAnalytics extends JFormFieldList
{

	protected $type = 'GAnalytics';

	protected function getOptions ()
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_ganalytics/models', 'GAnalyticsModel');
		$model = JModelLegacy::getInstance('Profiles', 'GAnalyticsModel');
		$accounts = $model->getItems();
		$options = array();
		foreach ($accounts as $account)
		{
			$options[] = JHtml::_('select.option', $account->id, $account->accountName . ' [' . $account->profileName . ']');
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}