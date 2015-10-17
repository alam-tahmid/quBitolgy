<?php
/********************************************************************
Product    : Plotalot
Date       : 13 March 2015
Copyright  : Les Arbres Design 2010-2015
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');
jimport('joomla.html.pagination');

class PlotalotModelChart extends LAP_model
{
var $_data;
var $_pagination = null;

//-------------------------------------------------------------------------------
// initialise data for a new row
//
function initData($chart_type)
{
	$this->_data = new stdClass();
	$this->_data->id = 0;
	$this->_data->published = 1;
	$this->_data->chart_type = $chart_type;
	$this->_data->chart_option = 0;
	$this->_data->chart_name = '';
	$this->_data->sample_id = 0;
	$this->_data->state = 0;
	$this->_data->chart_title = '';
	$this->_data->chart_title_colour = '';
	$this->_data->chart_title_font_size = '';
	$this->_data->styles = '';
	$this->_data->x_size = 512;
	$this->_data->y_size = 256;
	$this->_data->legend_type = 0;
	$this->_data->back_colour = 'FFFFFF';
	$this->_data->db_host = '';
	$this->_data->db_name = '';
	$this->_data->db_user = '';
	$this->_data->db_pass = '';
	$this->_data->num_plots = 1;
	$this->_data->show_grid = true;
	$this->_data->show_raw_data = false;
	$this->_data->show_script = false;
	$this->_data->x_title = '';
	$this->_data->x_start = '';
	$this->_data->x_end = '';
	$this->_data->x_format = FORMAT_NUM_UK_0;
	$this->_data->x_labels = -1;
	$this->_data->y_title = '';
	$this->_data->y_start = '';
	$this->_data->y_end = '';
	$this->_data->y_format = FORMAT_NUM_UK_0;
	$this->_data->y_labels = -1;
	$this->_data->extra_parms = '';
	$this->_data->extra_columns = '';
	$this->_data->plots = '';
	$this->_data->date_created = date('Y-m-d');
	$this->_data->date_updated = date('Y-m-d');
	$this->_data->plot_array = array();
	$this->_data->plot_array[0]['enable'] = 1;

	if (($this->_data->chart_type == CHART_TYPE_PL_TABLE) or ($this->_data->chart_type == CHART_TYPE_GV_TABLE))
		{
		$this->_data->y_labels = 100;			// max rows
		$this->_data->legend_type = true;		// default column headings to true
		$this->_data->style_array = array();
		}

	return $this->_data;
}

//-------------------------------------------------------------------------------
// get the post data and load the plot_ post data fields into an array
//
function getPostData()
{
	$this->_data = new stdClass();
	$jinput = JFactory::getApplication()->input;
	$this->_data->id = $jinput->get('id', '', 'INT');
	$this->_data->published = $jinput->get('published',1, 'INT');
	$this->_data->chart_type = $jinput->get('chart_type',0, 'INT');
	$this->_data->chart_option = 0;
	if (Plotalot_Utility::chartCategory($this->_data->chart_type) == CHART_CATEGORY_TABLE)
		$this->_data->chart_option = $jinput->get('chart_option',0, 'INT');
	if (Plotalot_Utility::chartCategory($this->_data->chart_type) == CHART_CATEGORY_PIE)
		$this->_data->chart_option = $jinput->get('pie_chart_option',0, 'INT');
	if (Plotalot_Utility::chartCategory($this->_data->chart_type) == CHART_CATEGORY_BAR)
		$this->_data->chart_option = $jinput->get('bar_chart_option',0, 'INT');
	if (Plotalot_Utility::chartCategory($this->_data->chart_type) == CHART_CATEGORY_COMBO)
		$this->_data->chart_option = $jinput->get('bar_chart_option',0, 'INT');
	$this->_data->chart_name = $jinput->get('chart_name', '', 'STRING');
	$this->_data->chart_title = $jinput->get('chart_title', '', 'STRING');
	$this->_data->chart_title_colour = $jinput->get('chart_title_colour', '', 'STRING');
	$this->_data->chart_title_font_size = $jinput->get('chart_title_font_size', '', 'STRING');
	$this->_data->styles = $jinput->get('styles', '', 'STRING');
	$this->_data->x_size = $jinput->get('x_size','512', 'STRING');
	$this->_data->y_size = $jinput->get('y_size','256', 'STRING');
	$this->_data->legend_type = $jinput->get('legend_type',0, 'INT');
	$this->_data->back_colour = $jinput->get('back_colour', '', 'STRING');
	$this->_data->db_host = $jinput->get('db_host', '', 'STRING');
	$this->_data->db_name = $jinput->get('db_name', '', 'STRING');
	$this->_data->db_user = $jinput->get('db_user', '', 'STRING');
	if ($this->_data->db_user == '-')
		$this->_data->db_user = '';
	$this->_data->db_pass = $jinput->get('db_pass', '', 'STRING');
	$this->_data->num_plots = $jinput->get('num_plots','1', 'STRING');
	$this->_data->show_grid = $jinput->get('show_grid',0, 'INT');
	$this->_data->show_raw_data = $jinput->get('show_raw_data',0, 'INT');
	$this->_data->show_script = $jinput->get('show_script',0, 'INT');
	$this->_data->x_title = $jinput->get('x_title', '', 'STRING');
	$this->_data->x_start = $jinput->get('x_start', '', 'STRING');
	$this->_data->x_end = $jinput->get('x_end', '', 'STRING');
	$this->_data->x_format = $jinput->get('x_format','0', 'INT');
	$this->_data->x_labels = $jinput->get('x_labels','-1', 'STRING');
	$this->_data->y_title = $jinput->get('y_title', '', 'STRING');
	$this->_data->y_start = $jinput->get('y_start', '', 'STRING');
	$this->_data->y_end = $jinput->get('y_end', '', 'STRING');
	$this->_data->y_format = $jinput->get('y_format',0, 'INT');
	$this->_data->y_labels = $jinput->get('y_labels','-1', 'STRING');
	$this->_data->extra_parms = $jinput->get('extra_parms','', 'STRING');
	$this->_data->extra_columns = $jinput->get('extra_columns','', 'STRING');

	$legend     = $jinput->get("legend",     array(), 'ARRAY');
	$colour     = $jinput->get("colour",     array(), 'ARRAY');
	$pie_style  = $jinput->get("pie_style",  array(), 'ARRAY');
	$line_style = $jinput->get("line_style", array(), 'ARRAY');
	$plot_type  = $jinput->get("plot_type",  array(), 'ARRAY');
	$query      = $jinput->get("query",      array(), 'ARRAY');
	$enable     = $jinput->get("enable",     array(), 'ARRAY');
	$this->_data->plot_array = array();
	for ($i = 0; $i < $this->_data->num_plots; $i++)
		{
		if (!isset($legend[$i]))
			$legend[$i] = '';
		$this->_data->plot_array[$i]['legend'] = $legend[$i];
		
		if (!isset($colour[$i]))
			$colour[$i] = '';
		$this->_data->plot_array[$i]['colour'] = $colour[$i];
		
		if (Plotalot_Utility::chartCategory($this->_data->chart_type) == CHART_CATEGORY_PIE)
			{
			if (!isset($pie_style[$i]))
				$pie_style[$i] = '0';
			$this->_data->plot_array[$i]['style']  = $pie_style[$i];
			}
		if (($this->_data->chart_type == CHART_TYPE_LINE) or ($this->_data->chart_type == CHART_TYPE_AREA))
			{
			if (!isset($line_style[$i]))
				$line_style[$i] = '0';
			$this->_data->plot_array[$i]['style']  = $line_style[$i];
			}
		if (Plotalot_Utility::chartCategory($this->_data->chart_type) == CHART_CATEGORY_COMBO)
			{
			if (!isset($plot_type[$i]))
				$plot_type[$i] = '0';
			$this->_data->plot_array[$i]['style']  = $plot_type[$i];
			}
		if (!isset($query[$i]))
			$query[$i] = '';
		// replace any C2A0 unicode non-breaking space characters with ordinary spaces
		$this->_data->plot_array[$i]['query']  = str_replace("\xC2"."\xA0", ' ', $query[$i]);
		
		if (!isset($enable[$i]))
			$enable[$i] = '';
		$this->_data->plot_array[$i]['enable'] = $enable[$i];
		}
	$this->_data->sort_plots = $jinput->get('sort_plots',0);
	if ($this->_data->sort_plots)
		usort($this->_data->plot_array,array('self', 'usort_compare'));

	$this->_data->plots = serialize($this->_data->plot_array);

	if (($this->_data->chart_type == CHART_TYPE_PL_TABLE) or ($this->_data->chart_type == CHART_TYPE_GV_TABLE))
		{
		$this->_data->style_array = array();
		$this->_data->style_array['pl_table']    = $jinput->get('style_pl_table', '', 'STRING');
		$this->_data->style_array['pl_title']    = $jinput->get('style_pl_title', '', 'STRING');
		$this->_data->style_array['pl_head']     = $jinput->get('style_pl_head', '', 'STRING');
		$this->_data->style_array['pl_odd']      = $jinput->get('style_pl_odd', '', 'STRING');
		$this->_data->style_array['pl_even']     = $jinput->get('style_pl_even', '', 'STRING');
		$this->_data->style_array['gv_head']     = $jinput->get('style_gv_head', '', 'STRING');
		$this->_data->style_array['gv_odd']      = $jinput->get('style_gv_odd', '', 'STRING');
		$this->_data->style_array['gv_row']      = $jinput->get('style_gv_row', '', 'STRING');
		$this->_data->style_array['gv_selected'] = $jinput->get('style_gv_selected', '', 'STRING');
		$this->_data->style_array['gv_hover']    = $jinput->get('style_gv_hover', '', 'STRING');
		$this->_data->style_array['gv_hcell']    = $jinput->get('style_gv_hcell', '', 'STRING');
		$this->_data->style_array['gv_tcell']    = $jinput->get('style_gv_tcell', '', 'STRING');
		$this->_data->style_array['gv_numcell']  = $jinput->get('style_gv_numcell', '', 'STRING');
		$this->_data->styles = serialize($this->_data->style_array);
		}
	else
		$this->_data->styles = '';
}

//-------------------------------------------------------------------------------
// custom comparison function for the usort() call above
//
function usort_compare($a, $b)
{
	return strcasecmp($a['legend'], $b['legend']);
}

//-------------------------------------------------------------------------------
// validate the data
//
function check()
{
	if ($this->_data->chart_name == '')
		{
		$this->_app->enqueueMessage(JText::_('COM_PLOTALOT_CHART_NAME_BLANK'), 'error');
		return false;
		}
		
// force x_labels and y_labels to be valid

	if (!Plotalot_Utility::is_posint($this->_data->x_labels,false))
		$this->_data->x_labels = -1;
	if (!Plotalot_Utility::is_posint($this->_data->y_labels,false))
		$this->_data->y_labels = -1;

// force a comma at the front of extra parameters

	if ((!empty($this->_data->extra_parms)) and (substr($this->_data->extra_parms,0,1) != ','))
		$this->_data->extra_parms = ','.$this->_data->extra_parms;

// no more validation for Plotalot tables and single items

	if ($this->_data->chart_type <= CHART_TYPE_SINGLE_ITEM)
		return true;
		
// x-size and y-size can be numbers or both blank to take the size of the containing div		

	if (!Plotalot_Utility::is_posint($this->_data->x_size,true))
		{
		$this->_app->enqueueMessage(JText::_('COM_PLOTALOT_INVALID').' '.JText::_('COM_PLOTALOT_SIZE'), 'error');
		return false;
		}

	if (!Plotalot_Utility::is_posint($this->_data->y_size,true))
		{
		$this->_app->enqueueMessage(JText::_('COM_PLOTALOT_INVALID').' '.JText::_('COM_PLOTALOT_SIZE'), 'error');
		return false;
		}
	if ( (($this->_data->x_size == '') and ($this->_data->y_size != ''))
	or   (($this->_data->x_size != '') and ($this->_data->y_size == '')) )
		{
		$this->_app->enqueueMessage(JText::_('COM_PLOTALOT_INVALID').' '.JText::_('COM_PLOTALOT_SIZE'), 'error');
		return false;
		}

// validate number of plots

	if ($this->_data->num_plots < 1)
		{
		$this->_data->num_plots = 1;
		$this->_app->enqueueMessage(JText::_('COM_PLOTALOT_MIN_PLOTS'), 'error');
		return false;
		}
		
	if ($this->_data->num_plots > CHART_MAX_PLOTS)
		{
		$this->_data->num_plots = CHART_MAX_PLOTS;
		$this->_app->enqueueMessage(JText::_('COM_PLOTALOT_MAX_PLOTS').' '.CHART_MAX_PLOTS, 'error');
		return false;
		}
		
// for bar charts, pie charts, and gauges, x_start, x_end, x_labels and x_format are not used

	$chart_category = Plotalot_Utility::chartCategory($this->_data->chart_type);

	if (($chart_category == CHART_CATEGORY_BAR) 
	or  ($chart_category == CHART_CATEGORY_PIE)
	or  ($this->_data->chart_type == CHART_TYPE_GAUGE))
		{
		$this->_data->x_start = '';
		$this->_data->x_end = '';
		$this->_data->x_format = FORMAT_NONE;
		$this->_data->x_labels = -1;
		}

	return true;
}

//-------------------------------------------------------------------------------
// Store a record
//
function store()
{	
	if (!$this->check())				// Validate user input
		return false;					// error message has been enqueued
		
	if ($this->_data->id == 0)
		$query = "INSERT INTO `#__plotalot`	(
			`published`, `chart_type`, `chart_option`, `chart_name`, `chart_title`, `chart_title_colour`, `chart_title_font_size`,
			`styles`, `x_size`, `y_size`, `legend_type`, `back_colour`, `db_host`, `db_name`, `db_user`, `db_pass`,
			`num_plots`, `show_grid`, `show_raw_data`, `show_script`, `x_title`, `x_start`, `x_end`, `x_format`, `x_labels`,
			`y_title`, `y_start`, `y_end`, `y_format`, `y_labels`, `extra_parms`, `extra_columns`, `plots`, `date_created`, `date_updated`) VALUES (".
				'1,'.
				$this->_data->chart_type.','.
				$this->_data->chart_option.','.
				$this->_db->Quote($this->_data->chart_name).','.
				$this->_db->Quote($this->_data->chart_title).','.
				$this->_db->Quote($this->_data->chart_title_colour).','.
				$this->_db->Quote($this->_data->chart_title_font_size).','.
				$this->_db->Quote($this->_data->styles).','.
				$this->_db->Quote($this->_data->x_size).','.
				$this->_db->Quote($this->_data->y_size).','.
				$this->_data->legend_type.','.
				$this->_db->Quote($this->_data->back_colour).','.
				$this->_db->Quote($this->_data->db_host).','.
				$this->_db->Quote($this->_data->db_name).','.
				$this->_db->Quote($this->_data->db_user).','.
				$this->_db->Quote($this->_data->db_pass).','.
				$this->_data->num_plots.','.
				$this->_data->show_grid.','.
				$this->_data->show_raw_data.','.
				$this->_data->show_script.','.
				$this->_db->Quote($this->_data->x_title).','.
				$this->_db->Quote($this->_data->x_start).','.
				$this->_db->Quote($this->_data->x_end).','.
				$this->_data->x_format.','.
				$this->_data->x_labels.','.
				$this->_db->Quote($this->_data->y_title).','.
				$this->_db->Quote($this->_data->y_start).','.
				$this->_db->Quote($this->_data->y_end).','.
				$this->_data->y_format.','.
				$this->_data->y_labels.','.
				$this->_db->Quote($this->_data->extra_parms).','.
				$this->_db->Quote($this->_data->extra_columns).','.
				$this->_db->Quote($this->_data->plots).','.
				$this->_db->Quote(date('Y-m-d')).','.
				$this->_db->Quote(date('Y-m-d')).')';
	else
		$query = "UPDATE `#__plotalot` SET 
				`chart_type` = ".$this->_data->chart_type.",
				`chart_option` = ".$this->_data->chart_option.",
				`chart_name` = ".$this->_db->Quote($this->_data->chart_name).",
				`state` = 0,
				`chart_title` = ".$this->_db->Quote($this->_data->chart_title).",
				`chart_title_colour` = ".$this->_db->Quote($this->_data->chart_title_colour).",
				`chart_title_font_size` = ".$this->_db->Quote($this->_data->chart_title_font_size).",
				`styles` = ".$this->_db->Quote($this->_data->styles).",
				`x_size` = ".$this->_db->Quote($this->_data->x_size).",
				`y_size` = ".$this->_db->Quote($this->_data->y_size).",
				`legend_type` = ".$this->_data->legend_type.",
				`back_colour` = ".$this->_db->Quote($this->_data->back_colour).",
				`db_host` = ".$this->_db->Quote($this->_data->db_host).",
				`db_name` = ".$this->_db->Quote($this->_data->db_name).",
				`db_user` = ".$this->_db->Quote($this->_data->db_user).",
				`db_pass` = ".$this->_db->Quote($this->_data->db_pass).",
				`num_plots` = ".$this->_data->num_plots.",
				`show_grid` = ".$this->_data->show_grid.",
				`show_raw_data` = ".$this->_data->show_raw_data.",
				`show_script` = ".$this->_data->show_script.",
				`x_title` = ".$this->_db->Quote($this->_data->x_title).",
				`x_start` = ".$this->_db->Quote($this->_data->x_start).",
				`x_end` = ".$this->_db->Quote($this->_data->x_end).",
				`x_format` = ".$this->_data->x_format.",
				`x_labels` = ".$this->_data->x_labels.",
				`y_title` = ".$this->_db->Quote($this->_data->y_title).",
				`y_start` = ".$this->_db->Quote($this->_data->y_start).",
				`y_end` = ".$this->_db->Quote($this->_data->y_end).",
				`y_format` = ".$this->_data->y_format.",
				`y_labels` = ".$this->_data->y_labels.",
				`extra_parms` = ".$this->_db->Quote($this->_data->extra_parms).",
				`extra_columns` = ".$this->_db->Quote($this->_data->extra_columns).",
				`plots` = ".$this->_db->Quote($this->_data->plots).",
				`date_updated` = ".$this->_db->Quote(date('Y-m-d'))."
					WHERE `id` = ".$this->_data->id;

	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');	// this is only called in the back end
		return false;
		}

	if ($this->_data->id == 0)							// if this was an insert ...
		$this->_data->id = $this->_db->insertId();		// .. get the id of the new row
	return true;
}

//-------------------------------------------------------------------------------
// Get one chart record
//
function &getOne($id)
{
	$this->_data = $this->ladb_loadObject("SELECT * FROM #__plotalot WHERE id = $id");

	if ($this->_data)
		{
		$this->_data->plot_array = unserialize($this->_data->plots);		// create the plot array
		if (Plotalot_Utility::chartCategory($this->_data->chart_type) == CHART_CATEGORY_TABLE)
			$this->_data->style_array = unserialize($this->_data->styles);	// create the styles array
		return $this->_data;
		}
	else
		{
		if ($this->_app->isAdmin())
			$this->_app->enqueueMessage(JText::_('COM_PLOTALOT_CHART_NOT_FOUND'), 'error');
		$this->_data = false;
		return $this->_data;
		}
}

//-------------------------------------------------------------------------------
// return the pagination object
// - should be called after getList()
//
function &getPagination()
{
	if ($this->_pagination == Null)
		$this->_pagination = new JPagination(0,0,0);
	return $this->_pagination;
}

//-------------------------------------------------------------------------------
// Load a list of charts
//
function &getList()
{
// get the filter states, order states, and pagination variables

	$app = JFactory::getApplication();
	$filter_state = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_state','filter_state','','word');
	$filter_chart_type = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_chart_type','filter_chart_type',CHART_TYPE_ANY,'int');
	$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
	$limitstart = $app->getUserStateFromRequest(LAP_COMPONENT.'.limitstart', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$filter_order = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_order', 'filter_order', 'chart_name');
	$filter_order_Dir = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'asc');

// build the query

	$query_count = "Select count(*) ";
	$query_cols  = "Select * ";
	$query_from  = "From #__plotalot ";

// where

	$query_where = "Where 1";

	switch ($filter_state)
		{
		case 'P':
			$query_where .= " And published = '1'";
			break;
		case 'U':
			$query_where .= " And published = '0'";
			break;
		default:
		}

	switch ($filter_chart_type)
		{
		case CHART_TYPE_ANY:
			break;
		case CHART_CATEGORY_TABLE:
			$query_where .= " And chart_type in (".CHART_TYPE_PL_TABLE.", ".CHART_TYPE_GV_TABLE.")";
			break;
		case CHART_CATEGORY_PIE:
			$query_where .= " And chart_type in (".CHART_TYPE_PIE_2D.", ".CHART_TYPE_PIE_3D.", ".CHART_TYPE_PIE_2D_V.", ".CHART_TYPE_PIE_3D_V.")";
			break;
		case CHART_CATEGORY_BAR:
			$query_where .= " And chart_type in (".CHART_TYPE_BAR_H_STACK.", ".CHART_TYPE_BAR_H_GROUP.", ".CHART_TYPE_BAR_V_STACK.", ".CHART_TYPE_BAR_V_GROUP.")";
			break;
		case CHART_CATEGORY_COMBO:
			$query_where .= " And chart_type in (".CHART_TYPE_COMBO_STACK.", ".CHART_TYPE_COMBO_GROUP.")";
			break;
		case CHART_CATEGORY_SAMPLE:
			$query_where .= " And sample_id > 0";
			break;
		default:
			$query_where .= " And chart_type = ".$filter_chart_type;
		}

// order by			

	switch ($filter_order)							// validate column name
		{
		case 'id':
		case 'chart_name':
		case 'chart_type':
		case 'db_name':
		case 'date_created':
		case 'date_updated':
			break;
		default:
			$filter_order = 'chart_name';
		}

	if (strcasecmp($filter_order_Dir,'ASC') != 0)	// validate 'asc' or 'desc'
		$filter_order_Dir = 'DESC';

	$query_order = " Order by ".$filter_order.' '.$filter_order_Dir;


// get the total row count

	$count_query = $query_count.$query_from.$query_where;
	$total = $this->ladb_loadResult($count_query);
	if ($total === false)
		{
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
		return $total;
		}

// setup the pagination object

	$this->_pagination = new JPagination($total, $limitstart, $limit);

//now get the data, within the limits required		

	$main_query = $query_cols.$query_from.$query_where.$query_order;
	$this->_data = $this->ladb_loadObjectList($main_query, $this->_pagination->limitstart, $limit);
	if ($this->_data === false)
		{
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->_data;
		}
		
	return $this->_data;
}

//-------------------------------------------------------------------------------
// Delete one or more items
//
function delete()
{
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get( 'cid', array(0), 'post', 'array' );

	foreach($cids as $cid)
		{
		$result = $this->ladb_execute("DELETE FROM `#__plotalot` WHERE `id` = ".$cid);
		if ($result === false)
			{
			$this->_app->enqueueMessage($this->ladb_error_text, 'error');
			return false;
			}
		}
	return true;
}

//-------------------------------------------------------------------------------
// $p is 0 if unpublishing, 1 if publishing
//
function publish($p)					
{
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get( 'cid', array(0), 'post', 'array' );

	foreach($cids as $cid)
		{
		$result = $this->ladb_execute("UPDATE `#__plotalot` SET `published` = $p WHERE `id` = ".$cid);
		$this->_db->query();
		if ($result === false)
			{
			$this->_app->enqueueMessage($this->ladb_error_text, 'error');
			return false;
			}
		}
	return true;
}

//-------------------------------------------------------------------------------
// Check our table structure is what we expect for this version
//
function check_table()
{
	$tables = $this->_db->getTableList();
	$dbprefix = $this->_app->getCfg('dbprefix');
	$table_name = str_replace('#__',$dbprefix,'#__plotalot');
	if (!self::in_arrayi($table_name,$tables))
		return false;									// table does not exist

	$fields = $this->_db->getTableColumns('#__plotalot');

	if (empty($fields))
		return false;
		
	if (array_key_exists('chart_option',$fields))		// 'chart_option' was added in Plotalot version 3.00
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Case insensitive in_array()
//
static function in_arrayi($needle, $haystack)
{
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}


}