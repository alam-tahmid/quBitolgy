<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JLoader::import('joomla.plugin.plugin');

class plgSystemGAnalytics extends JPlugin
{

	public function onAfterRender ()
	{
		if (JFactory::getDocument()->getType() != 'html')
		{
			return;
		}

		if ($this->params->get('load-tr-code', 1) == 0)
		{
			return;
		}

		if (JFactory::getApplication()->isAdmin() && $this->params->get('load-tr-code', 1) != 2)
		{
			return;
		}

		$groups = $this->params->get('user-groups', array());

		if (count(array_intersect(JFactory::getUser()->groups, $groups)) > 0)
		{
			return;
		}

		JLoader::import('components.com_ganalytics.helper.ganalytics', JPATH_ADMINISTRATOR);

		if (! class_exists('GAnalyticsHelper'))
		{
			return;
		}

		$model = JModelLegacy::getInstance('Profiles', 'GAnalyticsModel');
		$model->setState('ids', $this->params->get('accountids', null));
		$results = $model->getItems();
		if (! empty($results))
		{
			$tracking = $this->params->get('tracking');
			$hostname = $this->params->get('hostname');

			foreach ($results as $result)
			{
				$script = "\n<script type=\"text/javascript\">";
				$script .= "\nvar _gaq = _gaq || [];";
				$script .= "\n" . "_gaq.push(['_setAccount', '" . $result->webPropertyId . "']);";
				if ($tracking == 'subdomains')
				{
					$script .= "\n" . "_gaq.push(['_setDomainName', '." . $hostname . "']);";
				}
				else if ($tracking == 'tld')
				{
					$script .= "\n" . "_gaq.push(['_setDomainName', 'none']);";
					$script .= "\n" . "_gaq.push(['_setAllowLinker', true]);";
				}
				$script .= "\n" . "_gaq.push(['_trackPageview']);";
				$script .= "\n" . "(function() {";
				$script .= "\n\t" . "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;";
				$script .= "\n\t" . "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
				$script .= "\n\t" . "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);";
				$script .= "\n" . "})();\n</script>\n";
			}

			$buffer = JResponse::getBody();
			$buffer = preg_replace("/<\/head>/", $script . "</head>", $buffer);
			JResponse::setBody($buffer);
		}
	}
}