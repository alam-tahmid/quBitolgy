<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

$u = JFactory::getURI();
$url = JRoute::_( $u->toString().'?option=com_ganalytics&view=tools&layout=');
?>

<ul>
<li><a href="<?php echo $url; ?>systemcheck">System Check!!</a></li>
</ul>

<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>