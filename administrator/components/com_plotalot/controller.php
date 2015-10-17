<?php
/********************************************************************
Product    : Plotalot
Date       : 16 April 2014
Copyright  : Les Arbres Design 2010-2014
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class PlotalotController extends JControllerLegacy
{
function __construct()
{
	parent::__construct();						// automatically maps public functions
	$this->registerTask('apply', 'save');
	$this->registerTask('apply2', 'save');		// the second "Apply" button
	$this->registerTask('save2copy', 'save');
	$this->registerTask('save_css', 'apply_css');
}

function display($cachable = false, $urlparams = false)
{
	$model = $this->getModel('chart');
	$view = $this->getView('chartlist', 'html');
	$check_table = $model->check_table();
	$view->check_table = $check_table;
	if ($check_table)							// only do this if table structure ok
		{
		$chart_list = $model->getList();
		$pagination = $model->getPagination();
		$view->items = $chart_list;
		$view->pagination = $pagination;
		}
	$count = self::cache();
	$view->cache_count = $count;
	$view->display();
}

function new_chart()
{
	$jinput = JFactory::getApplication()->input;
	$jinput->set('hidemainmenu', 1);
	$model = $this->getModel('chart');
	$data = $model->initData(CHART_TYPE_LINE);

	$view = $this->getView('chart','html');
	$view->chart_data = $data;
	$view->display();
}

function new_table()
{
	$jinput = JFactory::getApplication()->input;
	$jinput->set('hidemainmenu', 1);
	$model = $this->getModel('chart');
	$data = $model->initData(CHART_TYPE_GV_TABLE);

	$view = $this->getView('table','html');
	$view->chart_data = $data;
	$view->display();
}

function new_item()
{
	$jinput = JFactory::getApplication()->input;
	$jinput->set('hidemainmenu', 1);
	$model = $this->getModel('chart');
	$data = $model->initData(CHART_TYPE_SINGLE_ITEM);

	$view = $this->getView('item','html');
	$view->chart_data = $data;
	$view->display();
}

function edit()
{
	$jinput = JFactory::getApplication()->input;
	$jinput->set('hidemainmenu', 1);
	$model = $this->getModel('chart');
	$cid = $jinput->get('cid',  array(), 'ARRAY');
	$id = (int) $cid[0];
	$data = $model->getOne($id);
	if ($data === false)
		{												// an error has been enqueued
		$this->setRedirect(LAP_COMPONENT_LINK);			// redirect back to list
		return;
		}

// if the chart type is not supported, don't go any further

	$plotutil = new Plotalot_Utility;
	if (!array_key_exists($model->_data->chart_type, $plotutil->chart_types))
		{
		$msg = JText::_('COM_PLOTALOT_CHART').' '.$model->_data->id.': '.JText::_('COM_PLOTALOT_ERROR_CHART_TYPE');
		$this->setRedirect(LAP_COMPONENT_LINK, $msg, 'error');
		return;
		}

	switch ($data->chart_type)
		{
		case CHART_TYPE_PL_TABLE:
		case CHART_TYPE_GV_TABLE:
			$view = $this->getView('table','html');
			break;
		case CHART_TYPE_SINGLE_ITEM:
			$view = $this->getView('item','html');
			break;
		default:
			$view = $this->getView('chart','html');
		}

	$view->chart_data = $data;
	$view->display();
}

function save()
{
	$jinput = JFactory::getApplication()->input;
	$task = $jinput->get('task', '', 'STRING');					// 'save' or 'apply'
	$model = $this->getModel('chart');
	$model->getPostData();
	if ($task == 'save2copy')					// for Save Copy ..
		{
		$model->_data->id = 0;					// .. create a new chart
		$task = 'save';							// and return to the chart list
		}
	$stored = $model->store();					// does validation and may enqueue error messages

	if ($stored and ($task == 'save'))
		{
		$msg = JText::_('COM_PLOTALOT_CHART_SAVED');
		$this->setRedirect(LAP_COMPONENT_LINK, $msg);
		return;
		}

// 'apply' or failed 'save' (db error or validation failure)

	switch ($model->_data->chart_type)
		{
		case CHART_TYPE_PL_TABLE:
		case CHART_TYPE_GV_TABLE:
			$view = $this->getView('table','html');
			break;
		case CHART_TYPE_SINGLE_ITEM:
			$view = $this->getView('item','html');
			break;
		default:
			$view = $this->getView('chart','html');
		}

	$view->stored = $stored;
	$view->chart_data = $model->_data;
	$view->display();
}

function remove()
{
	$model = $this->getModel('chart');
	if ($model->delete())
		$msg = JText::_('COM_PLOTALOT_DELETED');
	$this->setRedirect(LAP_COMPONENT_LINK, $msg);
}

function publish()
{
	$model = $this->getModel('chart');
	$model->publish(1);
	$this->setRedirect(LAP_COMPONENT_LINK, $msg);
}

function unpublish()
{
	$model = $this->getModel('chart');
	$model->publish(0);
	$this->setRedirect(LAP_COMPONENT_LINK, $msg);
}

function help()
{
	$view = $this->getView('help','html');
	$view->display();
}

function edit_css()
{
	$view = $this->getView('edit_css', 'html');
	$view->display();
}

function apply_css()								// save changes to front end css
{
	$jinput = JFactory::getApplication()->input;
	$task = $jinput->get('task', '', 'STRING');					// 'save' or 'apply'
	$css_contents = $_POST['css_contents'];
	if (strlen($css_contents) == 0)
		$this->setRedirect(LAP_COMPONENT_LINK."&task=display");
	$css_path = JPATH_COMPONENT_SITE.'/assets/plotalot.css';
	$length_written = file_put_contents ($css_path, $css_contents);
	if ($length_written == 0)
		$msg = JText::_('COM_PLOTALOT_NOT_SAVED');
	else
		$msg = JText::_('COM_PLOTALOT_SAVED');
	if ($task == 'apply_css')
		$this->setRedirect(LAP_COMPONENT_LINK."&task=edit_css",$msg);
	else
		$this->setRedirect(LAP_COMPONENT_LINK."&task=display",$msg);
}   

function trace_on()
{
	LAP_trace::init_trace();
	$this->setRedirect(LAP_COMPONENT_LINK.'&task=help');
}

function trace_off()
{
	LAP_trace::delete_trace_file();
	$this->setRedirect(LAP_COMPONENT_LINK.'&task=help');
}

function clear_cache()
{
	$count = self::cache(true);
	$msg = $count.' '.JText::_('COM_PLOTALOT_DELETED');
	$this->setRedirect(LAP_COMPONENT_LINK."&task=display",$msg);
}

//-------------------------------------------------------------------
// Count how many files are cached by the Plotalot plugin
// if $delete is true, delete them
// returns the number of files
//
function cache($delete=false)	
{
	$app = JFactory::getApplication();
	$tmp_path = $app->getCfg('tmp_path');
	$mask = $tmp_path.'/plotalot_*.txt';
	$files = glob($mask);				// get an array of matched files
	$count = count($files);
	if ($delete)
		@array_map("unlink", $files);
	return $count;
}

} // class
