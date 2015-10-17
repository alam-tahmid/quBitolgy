<?php
/********************************************************************
Product    : Plotalot
Date       : 16 February 2014
Copyright  : Les Arbres Design 2014
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
Description: Displays a list of charts on the front end
*********************************************************************/
defined('_JEXEC') or die('Restricted Access'); 

// load the helpers

	require_once JPATH_ADMINISTRATOR.'/components/com_plotalot/helpers/plotalot_helper.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_plotalot/helpers/db_helper.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_plotalot/helpers/plotalot.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_plotalot/helpers/view_helper.php';
	require_once JPATH_ADMINISTRATOR.'/components/com_plotalot/models/chart.php';

// get parameters from the active menu item

	$app = JFactory::getApplication('site');
	$menu_params =  $app->getParams();
	if ($menu_params == null)
		{
		echo 'Menu item not found';
		return;
		}
		
// load our css, if it exists

	LAP_view::load_styles();

// load the Google jsapi

	$document = JFactory::getDocument();
	$document->addScript("https://www.google.com/jsapi");

// display the heading and top text	

	echo "\n<h2>".$menu_params->get('page_hdr').'</h2>';
	echo "\n<div>".$menu_params->get('top_text').'</div>';

// get the model

	jimport('joomla.application.component.model');
	$model = new PlotalotModelChart;			

// draw the charts

	$chart_id_array = explode(",",$menu_params->get('chart_ids'));
	if (count($chart_id_array) == 0)
		{
		echo "\n<div>No charts specified</div>";
		return;
		}
	
	$plotalot = new Plotalot;
	
	foreach ($chart_id_array as $chart_id)
		{
		$plot_info = $model->getOne($chart_id);					// load the chart definition from the table
		if ($plot_info === false)
			{
			echo "\n<div>Chart $chart_id not defined</div>";
			continue;
			}
		if (!$plot_info->published)
			continue;											// skip if not published
		$chart = $plotalot->drawChart($plot_info);				// have Plotalot create the script or html
		if ($chart == '')
			{
			echo "\n<div>Chart $chart_id : ".$plotalot->error."</div>";
			continue;
			}
		if (strpos($chart,'script type'))						// is it a script?
			{
			echo "\n".'<div id="chart_'.$chart_id.'"></div>';	// for Google charts, define a <div> for the chart
			$document->addCustomTag($chart);					// .. and load the script
			}
		else
			echo "\n<div>".$chart.'</div>';						// for Plotalot tables and single items, just echo it
		}
		
// display the bottom text		

	echo "\n<div>".$menu_params->get('bottom_text').'</div>';

	return;

?>
