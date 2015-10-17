<?php
/*------------------------------------------------------------------------
# TZ Content Slider
# ------------------------------------------------------------------------
# Author    ThemeZart http://www.ThemeZart.com
# Copyright (C) 2010 - 2012 ThemeZart.com. All Rights Reserved.
# @license - GNU/GPL V2 for PHP files. CSS / JS are Copyrighted Commercial
# Websites: http://www.ThemeZart.com
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);

$k2route = JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php';
$k2utilities = JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php';
if (file_exists($k2route))
	require_once($k2route);
	
if (file_exists($k2utilities))
	require_once($k2utilities);
	
abstract class modTzContentSliderK2Helper {

	static function getList($params,$count){
	
			$mainframe = JFactory::getApplication();
			$catids								= $params->get('k2catids', array());
			$ordering							= $params->get('ordering', 'a.ordering');
			$ordering_direction					= $params->get('ordering_direction', 'ASC');
			$user_id							= $params->get('user_id');
			$show_featured						= $params->get('show_featured');

			$user 		= JFactory::getUser();
			$aid 		= $user->get('aid');
			$db 		= JFactory::getDBO();

			$jnow 		= JFactory::getDate();
			if ( version_compare( JVERSION, '3.0', '<' ) == 1) {  
				$now = $jnow->toMySQL();
			}else{
				$now = $jnow->toSql();
			}
			$nullDate 	= $db->getNullDate();

			$query = "SELECT a.*, c.name as categoryname,c.id as categoryid, c.alias as categoryalias, c.params as categoryparams".
			" FROM #__k2_items as a".
			" LEFT JOIN #__k2_categories c ON c.id = a.catid";
			if ( version_compare( JVERSION, '3.0', '<' ) == 1) { 
				$query .= " WHERE a.published = 1 AND a.access IN(".implode(',', $user->authorisedLevels()).") AND a.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->authorisedLevels()).")  AND c.trash = 0";
			}else{
				$query .= " WHERE a.published = 1 AND a.access IN(".implode(',', $user->getAuthorisedViewLevels()).") AND a.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")  AND c.trash = 0";
			}
			// User filter
			$userId = JFactory::getUser()->get('id');
			switch ($params->get('user_id'))
			{
				case 'by_me':
					$query .= ' AND (a.created_by = ' . (int) $userId . ' OR a.modified_by = ' . (int) $userId . ')';
					break;
				case 'not_me':
					$query .= ' AND (a.created_by <> ' . (int) $userId . ' AND a.modified_by <> ' . (int) $userId . ')';
					break;

				case '0':
					break;

				default:
					$query .= ' AND (a.created_by = ' . (int) $userId . ' OR a.modified_by = ' . (int) $userId . ')';
					break;				
			}

			//Added Category
			if (!is_null($catids)) {
				if (is_array($catids)) {
					JArrayHelper::toInteger($catids);
					$query .= " AND a.catid IN(".implode(',', $catids).")";
				} else {
					$query .= " AND a.catid=".(int)$catids;
				}
			}		
			
			//  Featured items filter
			if ($show_featured == '0')
			$query .= " AND a.featured != 1";

			if ($show_featured == '1')
			$query .= " AND a.featured = 1";

			// ensure should be published
			$query .= " AND ( a.publish_up = ".$db->Quote($nullDate)." OR a.publish_up <= ".$db->Quote($now)." )";
			$query .= " AND ( a.publish_down = ".$db->Quote($nullDate)." OR a.publish_down >= ".$db->Quote($now)." )";
			
			if (K2_JVERSION != '15')
			{
				if ($mainframe->getLanguageFilter())
				{
					$languageTag = JFactory::getLanguage()->getTag();
					$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
				}
			}
			
			//Ordering
			$orderby = $ordering . ' ' . $ordering_direction; //ordering

			$query .= " ORDER BY ".$orderby;
			$db->setQuery($query, 0, $count);
			$items = $db->loadObjectList();
			
			$model = K2Model::getInstance('Item', 'K2Model');
			if (count($items)) {
				foreach ($items as $item) {
				
					if (! empty($item->created_by_alias)) {
						$item->author = $item->created_by_alias;
					} else {
						$author = JFactory::getUser($item->created_by);
						$item->author = $author->name;
					}
					
					$item->created 		= $item->created;
					$item->hits 		= $item->hits;
					$item->category 	= $item->categoryname;
					$item->cat_link 	= urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($item->catid.':'.urlencode($item->categoryalias))));
					$item->image 		= modTzContentSliderK2Helper::getImage($item->id, $item->introtext);
					$item->title 		= htmlspecialchars($item->title);
					$item->introtext 	= $item->introtext;
					$item->link 		= urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->catid.':'.urlencode($item->categoryalias))));
					$item->comment		= '<a class="tzcontentslider-comments" href="' . $item->link . '#itemCommentsAnchor">' . JText::_('COMMENTS_TEXT') . ' (' . $model->countItemComments($item->id) . ')</a>';
					$item->rating 		= $model->getVotesPercentage($item->id);
					if ($params->get('article_extra_fields')) {
						$item->extra_fields = $model->getItemExtraFields($item->extra_fields, $item);
					}

					$rows[] = $item;
				}
				return $rows;
			}
	}
	
	//retrive k2 image
	public static function getImage($id, $text) {	
		if (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $id) . '_XL.jpg')) {
			return 'media/k2/items/cache/' . md5("Image" . $id) . '_XL.jpg';
		} else {
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $text, $matches);	
			if (isset($matches[1])) {
				return $matches[1];
			}		
		}	
	}
	
}
