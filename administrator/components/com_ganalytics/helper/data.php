<?php
/**
 * @package		GAnalytics
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2012 - 2015 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

if(!class_exists('apiClient')) {
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ganalytics'.DS.'libraries'.DS.'gapc'.DS.'apiClient.php');
}
JLoader::import('components.com_ganalytics.libraries.gapc.contrib.apiAnalyticsService', JPATH_ADMINISTRATOR);
JLoader::import('components.com_ganalytics.libraries.gviz.gviz_api', JPATH_ADMINISTRATOR);

class GAnalyticsDataHelper {

	public static function getAccounts() {
		try {
			$client = self::getClient();
			$service = new apiAnalyticsService($client);

			$client->authenticate();

			$data = array();
			foreach ($service->management_accounts->listManagementAccounts()->getItems() as $account) {
				foreach ($service->management_webproperties->listManagementWebproperties($account->getId())->getItems() as $webItem) {
					$profiles = $service->management_profiles->listManagementProfiles($account->getId(), $webItem->getId())->getItems();
					if (empty($profiles)) {
						continue;
					}
					foreach ($profiles as $profile) {
						$table = JTable::getInstance('Profile', 'GAnalyticsTable');
						$table->id = 0;

						$table->accountID = $account->getId();
						$table->accountName = $account->getName();

						$table->profileID = $profile->getId();

						$table->webPropertyId = $webItem->getId();
						$table->profileName = $webItem->getWebsiteUrl();
						$table->startDate = $profile->getCreated();
						$table->token = $client->getAccessToken();

						$data[] = $table;
					}
				}
			}
			return $data;
		} catch(Exception $e) {
			if(!JFactory::getLanguage()->hasKey('COM_GANALYTICS')) {
				JFactory::getLanguage()->load('com_ganalytics', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ganalytics');
			}
			JError::raiseWarning( 500, JText::_('COM_GANALYTICS_IMPORT_VIEW_MODEL_FEED_ERROR').' '.$e->getMessage());
			return array();
		}
	}

	public static function getData($profile, array $dimensions, array $metrics, JDate $startDate = null, JDate $endDate = null, array $sort = null, $filter = null, $max = 1000, $offset = 1) {
		if(!$profile) {
			return array();
		}

		if($startDate == null) {
			$startDate = new JDate();
			$startDate->modify('-1 month');
		}
		if($endDate == null) {
			$endDate = new JDate();
			$endDate->modify('-1 day');
		}

		if($endDate < $startDate) {
			$endDate = $startDate;
		}

		$newDimensions = '';
		foreach ($dimensions as $dimension) {
			if (strpos($dimension, 'ga:') === 0) {
				$newDimensions .= $dimension.',';
			} else {
				$newDimensions .= 'ga:'.$dimension.',';
			}
		}
		$newDimensions = trim($newDimensions, ',');

		$newMetrics = '';
		foreach ($metrics as $metric) {
			if (strpos($metric, 'ga:') === 0) {
				$newMetrics .= $metric.',';
			} else {
				$newMetrics .= 'ga:'.$metric.',';
			}
		}
		$newMetrics = trim($newMetrics, ',');

		$newSort = null;
		if($sort !== null) {
			$newSort = implode(',', $sort);
		}

		try {
			if(GAnalyticsHelper::isPROMode()) {
				$data = GAnalyticsProUtil::getFromCache($profile, $newDimensions, $newMetrics, $startDate, $endDate, $newSort, $filter, $max, $offset);
			} else {
				$client = self::getClient();
				$client->refreshToken($profile->token);

				$service = new apiAnalyticsService($client);

				$options = array('dimensions' => $newDimensions, 'start-index' => $offset, 'max-results' => $max);
				if(!empty($filter)) {
					$options['filters'] = $filter;
				}
				if(!empty($newSort)) {
					$options['sort'] = $newSort;
				}

				$data = $service->data_ga->get('ga:'.$profile->profileID, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $newMetrics, $options);
			}

			if($data != null && $data->getRows() == null) {
				$data->setRows(array());
			}

			return $data;
		} catch(Exception $e) {
			if(!JFactory::getLanguage()->hasKey('COM_GANALYTICS')) {
				JFactory::getLanguage()->load('com_ganalytics', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ganalytics');
			}
			JError::raiseWarning( 500, JText::_('COM_GANALYTICS_IMPORT_VIEW_MODEL_FEED_ERROR').' '.$e->getMessage());
			return null;
		}
	}

	public static function convertToJsonResponse($profile, $data = null, $chartType = 'list') {
		$dataTable = new GvizDataTable(JRequest::getVar('tqx', ''));
		try {
			if($chartType != 'list' && !GAnalyticsHelper::isPROMode()) {
				$chartType = 'list';
			}

			if($data === null) {
				$message = array_shift(JFactory::getApplication()->getMessageQueue());
				if(!empty($message) && key_exists('message', $message)) {
					$message = $message['message'];
				} else {
					$message = print_r($message, true);
				}
				$dataTable->addError('invalid_request', JText::_('COM_GANALYTICS_JSONDFEED_VIEW_ERROR'), $message);
			} else {
				$dataTable->addWarning('other', $chartType);

				$headers = $data->getColumnHeaders();//print_R($headers);die;
				foreach ($headers as $header) {
					$type = 'string';
					if($header->dataType == 'INTEGER' || $header->dataType == 'FLOAT' || $header->dataType == 'TIME') {
						$type = 'number';
					}
					if(stripos($header->name, 'ga:date') !== false)
						$type = 'date';

					$dataTable->addColumn($header->name, GAnalyticsHelper::translate($header->name), $type);
				}
				$dateRange = JRequest::getVar('dateRange', 'day');
				//$dateFormat = $params->get('dateFormat', '%d.%m.%Y');

				$counter = -1;
				foreach($data->getRows() as $item) {
					$counter++;
					$rowId = -1;
					foreach($item as $index => $value) {
						$header = $headers[$index];
						$formatted = $value;
						$property = '';
						if(stripos($header->name, 'ga:date') !== false) {
							$value = mktime(0, 0, 0, substr($value, 4, 2), substr($value, 6, 2), substr($value, 0, 4));
							$formatted = strftime(GAnalyticsHelper::getComponentParameter('dateFormat', '%d.%m.%Y'), $value);
							if($dateRange == 'week' && strftime('%u', $value) > 1 && $counter > 0 && $counter < count($data->getRows())-1) {
								break;
							}
							if($dateRange == 'month' && strftime('%e', $value) > 1 && $counter > 0 && $counter < count($data->getRows())-1) {
								break;
							}
						} else if(stripos($header->name, 'source') !== false && $value != '(direct)' && $chartType == 'list') {
							$url = $value;
							foreach ($headers as $index => $tmpHeader) {
								if(stripos($tmpHeader->name, 'referralPath') !== false && $item[$index] != '(not set)') {
									$url .= $item[$index];
								}
							}
							$formatted = '<a href="http://'.$url.'" target="_blank">'.GAnalyticsHelper::trim($value).'</a>';
						} else if(stripos($header->name, 'country') !== false && $chartType == 'list') {
							$flag = GAnalyticsHelper::convertCountryNameToISO($value);
							if(!empty($flag))
								$property = "style: 'background-image:url(\"".JURI::root().'media/com_ganalytics/images/flags/'.strtolower($flag).".gif\"); background-repeat: no-repeat;background-position: 5px 4px;padding-left:30px;'";
						} else if(stripos($header->name, 'pagePath') !== false && $chartType == 'list') {
							$formatted = '<a href="'.JRoute::_('index.php?option=com_ganalytics&view=page&tmpl=component&gaid='.$profile->id.'&path='.base64_encode($value)).'">'.GAnalyticsHelper::trim($value).'</a>';
						} else {
							$value = addslashes($value);
							$formatted = GAnalyticsHelper::trim($value);
						}

						if($rowId == -1) {
							$rowId = $dataTable->newRow();
						}
						$dataTable->addCell($rowId, $header->name, JText::_($value), JText::_($formatted), $property);
					}
				}
			}
		} catch(Exception $e) {
			$dataTable->addError('invalid_request', JText::_('COM_GANALYTICS_JSONDFEED_VIEW_ERROR'), $e->getMessage());
		}
		return $dataTable->toJsonResponse();
	}

	public static function getClient() {
		$client = new apiClient();
		$client->setApplicationName('GAnalytics joomla extension');
		$client->setClientId(GAnalyticsHelper::getComponentParameter('client-id'));
		$client->setClientSecret(GAnalyticsHelper::getComponentParameter('client-secret'));
		$uri = JFactory::getURI();
		if(filter_var($uri->getHost(), FILTER_VALIDATE_IP)) {
			$uri->setHost('localhost');
		}
		$client->setRedirectUri($uri->toString(array('scheme', 'host', 'port', 'path')).'?option=com_ganalytics&view=import');
		$client->setUseObjects(true);

		$service = new apiAnalyticsService($client);

		return $client;
	}
}