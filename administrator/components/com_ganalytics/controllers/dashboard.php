<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class GAnalyticsControllerDashboard extends JControllerLegacy {

	public function __construct($config = array()){
		parent::__construct($config);
	}

	public function save(){
		$structure = JRequest::getVar('structure', null);
		if($structure === null){
			return;
		}

		if (!GAnalyticsHelper::getActions()->get('core.edit')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}

		if ($this->getModel()->saveStructure(json_decode($structure, true))) {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_SAVE_SUCCESS'), 'status' => 'success'));
		} else {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_SAVE_ERROR'), 'status' => 'error'));
		}
		JFactory::getApplication()->close();
	}

	public function addgroup(){
		if (!GAnalyticsHelper::getActions()->get('core.create')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}

		$table = $this->getModel()->addgroup(JRequest::getCmd('name'));
		if ($table->id > 0) {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_CREATE_GROUP_SUCCESS'), 'status' => 'success', 'id' => $table->id, 'name' => $table->name));
		} else {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_CREATE_GROUP_ERROR'), 'status' => 'error'));
		}
		JFactory::getApplication()->close();
	}

	public function deletegroup(){
		if (!GAnalyticsHelper::getActions()->get('core.delete')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}

		$success = $this->getModel()->deletegroup(JRequest::getInt('id'));
		if ($success) {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_DELETE_GROUP_SUCCESS'), 'status' => 'success', 'id' => JRequest::getInt('id')));
		} else {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_DELETE_GROUP_ERROR'), 'status' => 'error'));
		}
		JFactory::getApplication()->close();
	}

	public function addcolumn(){
		if (!GAnalyticsHelper::getActions()->get('core.edit')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}

		$success = $this->getModel()->addcolumn(JRequest::getInt('id'), JRequest::getInt('column'));
		if ($success) {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_CREATE_GROUPCOLUMN_SUCCESS'), 'status' => 'success', 'id' => JRequest::getInt('id'), 'column' => JRequest::getInt('column')));
		} else {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_CREATE_GROUPCOLUMN_ERROR'), 'status' => 'error'));
		}
		JFactory::getApplication()->close();
	}

	public function deletecolumn(){
		if (!GAnalyticsHelper::getActions()->get('core.edit')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}

		$success = $this->getModel()->deletecolumn(JRequest::getInt('id'), JRequest::getInt('column'));
		if ($success) {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_DELETE_GROUPCOLUMN_SUCCESS'), 'status' => 'success', 'id' => JRequest::getInt('id'), 'column' => JRequest::getInt('column')));
		} else {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_DELETE_GROUPCOLUMN_ERROR'), 'status' => 'error'));
		}
		JFactory::getApplication()->close();
	}

	public function savewidget(){
		if (JRequest::getInt('id', 0) == 0 && !GAnalyticsHelper::getActions()->get('core.create')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}
		if (JRequest::getInt('id', 0) > 0 && !GAnalyticsHelper::getActions()->get('core.edit.state')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}

		$success = null;
		if(JRequest::getInt('id', 0) == 0){
			$success = $this->getModel()->addwidget();
		}else{
			$success = $this->getModel()->savewidget();
		}

		if ($success) {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_SAVE_WIDGET_SUCCESS'), 'status' => 'success'));
		} else {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_SAVE_WIDGET_ERROR'), 'status' => 'error'));
		}
		JFactory::getApplication()->close();
	}

	public function deletewidget(){
		if (!GAnalyticsHelper::getActions()->get('core.delete')){
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'), 'status' => 'error'));
			JFactory::getApplication()->close();
			return;
		}

		$id = JRequest::getInt('id', 0);

		if ($this->getModel()->deletewidget($id)) {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_DELETE_WIDGET_SUCCESS'), 'status' => 'success', 'id' => $id));
		} else {
			echo json_encode(array('message' => JText::_('COM_GANALYTICS_DASHBOARD_DELETE_WIDGET_ERROR'), 'status' => 'error', 'id' => $id));
		}
		JFactory::getApplication()->close();
	}

	public function reset(){
		if (!GAnalyticsHelper::getActions()->get('core.create')){
			$this->setRedirect('index.php?option=com_ganalytics', JText::_('COM_GANALYTICS_DASHBOARD_NO_PERMISSION'));
			return;
		}

		$this->getModel()->reset();
		$this->setRedirect('index.php?option=com_ganalytics');
	}

	public function getModel($name = 'Dashboard', $prefix = 'GAnalyticsModel'){
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}