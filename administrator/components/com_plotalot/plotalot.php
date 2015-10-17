<?php
/********************************************************************
Product    : Plotalot
Date       : 16 February 2014
Copyright  : Les Arbres Design 2010-2014
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

// load the helpers

require_once JPATH_COMPONENT.'/helpers/plotalot_helper.php';
require_once JPATH_COMPONENT.'/helpers/db_helper.php';
require_once JPATH_COMPONENT.'/helpers/plotalot.php';
require_once JPATH_COMPONENT.'/helpers/trace_helper.php';

// load our css and javascript

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/com_plotalot/assets/com_plotalot.css');
$document->addScript(JURI::base(true).'/components/com_plotalot/assets/jscolor.js');

JHtml::_('behavior.framework');	// load MooTools
JHTML::_('behavior.tooltip');

// create an instance of the controller and tell it to execute $task

require_once( JPATH_COMPONENT.'/controller.php' );
$controller	= new PlotalotController( );

$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', '', 'STRING');

$controller->execute($task);

$controller->redirect();