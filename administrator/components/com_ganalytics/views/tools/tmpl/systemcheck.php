<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
?>

<table class="adminlist">
	<thead>
		<tr>
			<th>Status</th>
			<th width="40">Name</th>
			<th>Description</th>
			<th>Solution</th>
		</tr>
	</thead>

	<?php
	$data = array();
	$data[] = checkRemoteConnection();
	//$data[] = checkPhpVersion();
	//$data[] = checkCache();
	$data[] = checkPROMode();
	$tmp = checkDB();
	$data = array_merge($data, $tmp);
	foreach ($data as $test) {
		echo "<tr>\n";
		$img = "components/com_ganalytics/views/tools/tmpl/ok.png";
		if($test['status']=="failure")
		$img = "components/com_ganalytics/views/tools/tmpl/failure.png";
		else if($test['status']=="warning")
		$img = "components/com_ganalytics/views/tools/tmpl/warning.png";
		echo "<td width=\"17\" align=\"center\"><img src=\"".$img."\" width=\"16\" height=\"16\"/></td>\n";
		echo "<td width=\"120\">".$test['name']."</td><td>".$test['description']."</td><td>".$test['solution']."</td>";
		echo "</tr>\n";
	}
	?>
</table>
<div align="center" style="clear: both">
	<?php echo sprintf(JText::_('COM_GANALYTICS_FOOTER'), JRequest::getVar('GANALYTICS_VERSION'));?>
</div>


<?php
function checkDB() {
	$tmp = array();
	$model = JModelLegacy::getInstance('Profiles', 'GAnalyticsModel');
	$results = $model->getItems();
	if (empty($results)) {
		$tmp[] = array('name'=>'DB Entries Check', 'description'=>'No profiles found.', 'status'=>'warning', 'solution'=>'Import your google analytics profiles.');
	} else {
		foreach ($results as $result) {
			$data = GAnalyticsDataHelper::getData($result, array('date'), array('visits'));

			if ($data === null){
				$message = array_shift(JFactory::getApplication()->getMessageQueue());
				if (key_exists('message', $message)) {
					$message = $message['message'];
				} else {
					$message = print_r($message, true);
				}
				$desc = "An error occurred when reading statistic data from profile ".$result->profileName.":<br>".$message;
				$solution = "<ul><li>If the error is the same as in the connection test use the solution described there.</li>";
				$solution .= "<li><b>If the problem still exists check the forum at <a href=\"http://joomla.digital-peak.com\">joomla.digital-peak.com</a>.</b></li></ul>";
				$status = 'failure';
			} else if(empty($data)) {
				$solution = 'Make sure the tracking plugin is enabled. If it is a brand new account come tomorrow again till google processed your data :-).';
				$status = 'warning';
				$desc = 'GAnalytics could fetch the statistics data without any problems from profile '.$result->profileName.'. But the result was empty.';
			} else {
				$solution = '';
				$status = 'ok';
				$desc = 'GAnalytics could read the statistic data without any problems from profile '.$result->profileName.'.';
			}
			$tmp[] = array('name'=>$result->profileName.' Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
		}
	}
	return $tmp;
}


function checkRemoteConnection() {
	$desc = '';
	$solution = '';
	$status = 'ok';
	if (function_exists('curl_exec')){
		$ch=curl_init();
		curl_setopt ($ch, CURLOPT_URL,'www.google.com');
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch,CURLOPT_VERBOSE,false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$page=curl_exec($ch);
		// $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if(curl_errno($ch)){
			$desc = 'Curl could not retrieve remote content from www.google.com. The following error occured:'.curl_error($ch);
			$solution = 'Please contact your web hoster and check if their firewall blocks curl http calls to google.com.';
			$status = 'failure';
		}else{
			$desc = 'Curl could sucessfully retrieve remote content from www.google.com.';
		}
		curl_close($ch);
	}else{
		$fp = fsockopen("www.google.com", 80, $errno, $errstr, 5);
		if (!$fp) {
			$desc = 'A connection to www.google.com could not be established. The following error occured:'.$errstr.' ('.$errno.')';
			$solution = 'Please contact your web hoster and check if their firewall blocks http calls to google.com.';
			$status = 'failure';
		} else {
			$desc = 'A connection to www.google.com could successfully be established.';
		}
	}
	return array('name'=>'Google Connection Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
}

function checkPhpVersion() {
	$desc = "Your PHP version is ".phpversion().". This is enough to use GAnalytics.";
	$status = 'ok';
	$solution = '';
	if(phpversion() < '5.0.0') {
		$desc = "Your PHP version is ".phpversion().". This is not enough to use GAnalytics.";
		$status = 'failure';
		$solution = 'Contact your web hoster and check if it possible to upgrade your php version to 5.0.0 or above.';
	}
	return array('name'=>'PHP Version Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
}

function checkCache() {
	$cacheDir =  JPATH_BASE.'/cache/com_ganalytics';
	$desc = "The directory ".$cacheDir." which is used by GAnalytics as cache directory is writable, this means you can enable caching in the GAnalytics component parameters.";
	$status = 'ok';
	$solution = '';
	JFolder::create($cacheDir, 0755);
	if ( !is_writable( $cacheDir ) ) {
		$desc = "The directory ".$cacheDir." which is used by GAnalytics as cache directory is not writable, this means you can't enable caching in the GAnalytics component parameters.";
		$status = 'failure';
		$solution = 'Set manually the write permission for the folder '.$cacheDir.' to writable.';
	}
	return array('name'=>'GAnalytics Cache Dir Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
}

function checkPROMode() {
	$desc = "Cool you use the PRO mode!!";
	$status = 'ok';
	$solution = '';
	if ( !GAnalyticsHelper::isPROMode() ) {
		$desc = "You are using the FREE version which doesn't have all the features the PRO version has. <a href=\"http://joomla.digital-peak.com/content/docu/doku.php/id,docu;ganalytics;pro/\" target=\"_blank\">Consider buying</a> the PRO version and get some cool graphical stuff and more.";
		$status = 'warning';
		$solution = '.';
	}
	return array('name'=>'GAnalytics Mode Check', 'description'=>$desc, 'status'=>$status, 'solution'=>$solution);
}