<?php
/********************************************************************
Product		: Plotalot
Date		: 1 March 2015
Copyright	: Les Arbres Design 2009-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class PlotalotViewHelp extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('JHELP'), 'plotalot.png');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');

// we need a form for the trace controls and the cancel button

?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="option" value="<?php echo LAP_COMPONENT ;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="" />		
	</form>
<?php 
		
// build the help screen

	$help['name'] = LAP_COMPONENT_NAME;
	$help['prefix'] = 'COM_PLOTALOT';
	$help['current_version'] = LAP_trace::getComponentVersion();
	$help['reference'] = 'plotalot';
	$help['link_version'] = "http://www.lesarbresdesign.info/version-history/plotalot";
	$help['link_doc'] = "http://www.lesarbresdesign.info/extensions/plotalot/downloads";
	$help['link_rating'] = "http://extensions.joomla.org/extensions/financial/graphs-and-charts/11717";
	
	$this->draw_help($help);

// make the trace controls

	echo '<p></p>';		
	echo LAP_trace::make_trace_controls('help');		
}

//------------------------------------------------------------------------------
// draw the help screen - this is the same in all our components
//
function draw_help($help)
{
	echo '<h3>'.$help['name'].': '.JText::_($help['prefix'].'_HELP_TITLE').'</h3>';

	echo '<p><span style="font-size:120%;font-weight:bold;">'.JText::sprintf($help['prefix'].'_HELP_RATING', $help['name']).' ';
	echo JHTML::link($help['link_rating'], 'Joomla Extensions Directory', 'target="_blank"').'</span></p>';
	
	$version_info = $this->get_version_info($help['reference'], $help['current_version']);
	if (!empty($version_info['message']))
		echo $version_info['message'];

	$k = 0;
	echo '<table class="help_support">';
	
	echo '<tr class="row'.$k.'"><td>'.JText::_($help['prefix'].'_VERSION').'</td>';
	echo '<td>'.$help['current_version'].'</td></tr>';
	
	if (!empty($version_info['latest_version']))
		{
		$k = 1 - $k;
		echo '<tr class="row'.$k.'"><td>'.JText::_($help['prefix'].'_LATEST_VERSION').'</td>';
		echo '<td>'.$version_info['latest_version'].'</td></tr>';
		}

	$k = 1 - $k;
	echo '<tr class="row'.$k.'"><td>'.JText::_($help['prefix'].'_HELP_CHECK').'</td>';
	echo '<td>'.JHTML::link($help['link_version'], 'Les Arbres Design - '.$help['name'], 'target="_blank"').'</td></tr>';

	$pdf_icon = JHTML::_('image', JURI::root().'administrator/components/com_'.$help['reference'].'/assets/pdf_16.gif','',
		'style="vertical-align:middle;border:none;padding:0;margin:0;"');
	$k = 1 - $k;
	echo '<tr class="row'.$k.'"><td>'.$pdf_icon.' '.JText::_($help['prefix'].'_HELP_DOC').'</td>';
	echo '<td>'.JHTML::link($help['link_doc'], "www.lesarbresdesign.info", 'target="_blank"').'</td></tr>';

	$k = 1 - $k;
	echo '<tr class="row'.$k.'"><td>'.JText::_($help['prefix'].'_HELP_LES_ARBRES').'</td><td>'.
		JHTML::link("http://www.lesarbresdesign.info/", 'Les Arbres Design', 'target="_blank"').'</td></tr>';
		
	if (!empty($help['extra']))
		foreach($help['extra'] as $row)
			{
			$k = 1 - $k;
			echo '<tr class="row'.$k.'"><td>'.$row['left'].'</td><td>'.$row['right'].'</td></tr>';
			}

	echo '</table>';
}
					
//------------------------------------------------------------------------------
// get the latest version info
// the response message looks like this: ###latestversion=xxxxx&message=xxxx###
//
function get_version_info($product, $current_version)
{
	$version_info = array();
	$url = 'http://www.lesarbresdesign.info/index.php?option=com_laversion&task=get_version&format=raw&product='.$product.'&ver='.$current_version;
	$page = $this->getPage($url);
	$start_pos = strpos($page, '###');
	if ($start_pos === false)
		return $version_info;
	$end_pos = strrpos($page,'###');
	if ($end_pos === false)
		return $version_info;
	$length = $end_pos - $start_pos - 3;
	$message = substr($page,$start_pos + 3, $length);
	parse_str($message, $version_info);
	return $version_info;
}

//------------------------------------------------------------------------------
// try three different ways to get a web page
//
function getPage($url)
{
	$url = str_replace(" ", "+", $url);
	$page = @file_get_contents($url);
	if (!empty($page))
		return $page;

// file_get_contents() failed, try curl

	if (function_exists('curl_init'))
		{
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		$page = curl_exec($handle);
		if ($page === false)
			{
			$ret = curl_getinfo($crl, CURLINFO_HTTP_CODE);
			curl_close($handle);
			return curl_error($handle);
			}
		curl_close($handle);
		return $page;
		}
		
// try sockets

	$host = parse_url($url,PHP_URL_HOST);
	$path = parse_url($url,PHP_URL_PATH);
	$query = parse_url($url,PHP_URL_QUERY);
	if (!empty($query))
		$path .= '?'.$query;

	$errno = 0;
	$errdesc = '';
	$sock = @fsockopen($host, 80, $errno, $errdesc);
	if (!$sock)
		return "Error: fsockopen failed $errno $errdesc";

	$request = "GET $path HTTP/1.0\r\n";
	$request .= "Host: $host\r\n";
	$request .= "User-Agent: Joomla\r\n\r\n";
	$length = fwrite($sock, $request);
	if ($length === false)
		return("Error: fwrite failed");
	
	$hdrs = fgets($sock, 1024);
	if (strstr($hdrs,"200 OK") === false)
		{
		fclose($sock);
		return $hdrs;		// e.g. "HTTP/1.0 400 Bad Request "
		}
	$page = '';
	$headerdone = false;
	while (!feof($sock))
		{
		$buffer = fgets($sock, 1024);
		if ($headerdone)
			$page .= $buffer;					// accummulate the response
		if (strcmp($buffer, "\r\n") == 0)		// blank line indicates end of http header
			$headerdone = true;
		}
	fclose($sock);
	if ($page != '')
		return $page;

	return '?';
}


}
				
			


			
