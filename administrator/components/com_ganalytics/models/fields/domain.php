<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JLoader::import('joomla.form.helper');
JFormHelper::loadFieldClass('text');

class JFormFieldDomain extends JFormFieldText
{

	protected $type = 'Domain';

	public function getInput ()
	{
		$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs))
		{
			$domain = $regs['domain'];
		}
		$value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		$this->value = ! empty($value) ? $value : $domain;

		return parent::getInput();
	}
}