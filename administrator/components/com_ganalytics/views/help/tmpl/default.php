<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>
<table width="400px">
	<tr>
		<td valign="top">
		<h2>GAnaltics Help</h2>

		<p>GAnalytics connects the googla analytics service with your joomla
		powered web site. Easy to use and flexibility are the main targets of
		GAnalytics.<br />
		<br />
		<b>How Google advertises his analytics service:</b><br />
		<i> Google Analytics is the enterprise-class web analytics solution
		that gives you rich insights into your website traffic and marketing
		effectiveness. Powerful, flexible and easy-to-use features now let you
		see and analyze your traffic data in an entirely new way. With Google
		Analytics, you're more prepared to write better-targeted ads,
		strengthen your marketing initiatives and create higher converting
		websites.</i></p>

		<?php if(!GAnalyticsHelper::isProMode()){?>
		<h2>Donation</h2>
		There is more effort behind GAnalytics than you think... <br>
		<br>
		You get this extensions for free but the project depends on donations
		to support further releases and new features!! Please make a small
		donation with paypal.....<br>
		<br>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><input
			type="hidden" name="cmd" value="_s-xclick"> <input type="hidden"
			name="hosted_button_id" value="302238"> <input type="image"
			src="https://www.paypal.com/en_US/CH/i/btn/btn_donateCC_LG.gif"
			border="0" name="submit" alt=""> <img alt="" border="0"
			src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1"
			height="1"></form>
			<?php }?>
		<h2>Documentation and Support</h2>
		At <a href="http://joomla.digital-peak.com" target="_blank">joomla.digital-peak.com</a>
		you will find all the informations about the project as well as a
		forum to post questions.</td>
	</tr>
</table>

<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>