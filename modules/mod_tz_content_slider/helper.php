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
require_once JPATH_SITE.'/components/com_content/helpers/route.php';

jimport('joomla.application.component.model');
jimport( 'joomla.plugin.helper');
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models');

abstract class modTzContentSliderJHelper
{
	static function getList($params,$count){
		
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();

		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$appParams = JFactory::getApplication()->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);

		$catids								= $params->get('catids', array());
		$ordering							= $params->get('ordering', 'a.ordering');
		$ordering_direction					= $params->get('ordering_direction', 'ASC');
		$user_id							= $params->get('user_id');
		$show_featured						= $params->get('show_featured');
		
		//sp comments
		$plgParams							= "";
		if (JPluginHelper::isEnabled('content', 'spcomments')) {
			$plgname = JPluginHelper::getPlugin('content', 'spcomments');
			$plgParams = new JParameter($plgname->params);
		}
		//sp comments
		
		$model->setState('list.limit', (int) $count);
		
		$model->setState('filter.published', 1);

		// User filter
		$userId = JFactory::getUser()->get('id');
		switch ($user_id)
		{
			case 'by_me':
				$model->setState('filter.author_id', (int) $userId);
				break;
			case 'not_me':
				$model->setState('filter.author_id', $userId);
				$model->setState('filter.author_id.include', false);
				break;

			case '0':
				break;

			default:
				$model->setState('filter.author_id', (int) $user_id);
				break;
		}		

		//  Featured switch
		switch ($show_featured)
		{
			case '1':
				$model->setState('filter.featured', 'only');
				break;
			case '0':
				$model->setState('filter.featured', 'hide');
				break;
			default:
				$model->setState('filter.featured', 'show');
				break;
		}
		
		/*
		$model->setState('list.select', 'a.fulltext, a.id, a.title, a.alias, a.title_alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,a.images,' .
			' a.modified, a.modified_by,a.publish_up, a.publish_down, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' .
			' a.hits, a.featured,' .		
			' LENGTH(a.fulltext) AS readmore');
		*/	
		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $catids);

		// Filter by language
		$model->setState('filter.language',$app->getLanguageFilter());

		// Set ordering
		$model->setState('list.ordering', $ordering);
		$model->setState('list.direction', $ordering_direction);

		//	Retrieve Content
		$items = $model->getItems();
		
		foreach ($items as &$item) {
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug 		= $item->catid.':'.$item->category_alias;
			$author 			= &JFactory::getUser($item->created_by);

			$item->author 		= ($item->created_by_alias) ? $item->created_by_alias : $author->name;
			$item->created 		= $item->created;
			$item->hits 		= $item->hits;
			$item->category 	= $item->category_title;
			$item->cat_link 	= JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
			$item->image 		= modTzContentSliderJHelper::getImage($item->introtext,$item->images);
			$item->title 		= htmlspecialchars($item->title);
			$item->introtext 	= JHtml::_('content.prepare', $item->introtext);
			$item->link 		= JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			$item->comment 		= modTzContentSliderJHelper::getComment($item->link, $item->catid, $plgParams);
			$item->rating 		= ($item->rating) ? number_format(intval($item->rating)/intval($item->rating_count), 2)*20 : 0;		

		}	
		return $items;
		
	}
	
	function getImage($text, $image_src="") {
		$image_src = json_decode($image_src);		
		if (JVERSION>=2.5 && @$image_src->image_intro) {
			return $image_src->image_intro;
		} elseif (JVERSION>=2.5 && @$image_src->image_fulltext) {
			return $image_src->image_fulltext;
		} else {
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $text, $matches);	
			if (isset($matches[1])) {
				return $matches[1];
			}			
		}
	}
	
	//function to retrive comment from sp comments plugin
	function getComment($url, $catid, $params) {
		if (JPluginHelper::isEnabled('content', 'spcomments')) {
			if (in_array($catid, $params->get('catids'))) {	
				//identifier
				$post_id			=substr(JURI::base(), 0, -1)."/".strstr($url, 'index.php');
				$identifier			= md5($post_id);
				//params
				$commenting_engine 	= $params->get('commenting_engine');
				$disqus_subdomain	= $params->get('disqus_subdomain');
				$disqus_devmode		= $params->get('disqus_devmode');
				$disqus_lang		= $params->get('disqus_lang');
				$intensedebate_acc	= $params->get('intensedebate_acc');
				$fb_appID			= $params->get('fb_appID');
				$fb_lang			= $params->get('fb_lang');
				
				if ($commenting_engine=="disqus") {//if disquss
					$link = '<a class="tzcontentslider-comments" href="' . $url . '#disqus_thread" data-disqus-identifier="' . $identifier . '"></a>';
				} else if ($commenting_engine=="intensedebate") {//intenseDebate
					$link = '<span class="containerCountComment">
							<script type="text/javascript">
							//<![CDATA[
									var idcomments_acct = "' . $intensedebate_acc . '";
									var idcomments_post_id = "' . $identifier . '";
									var idcomments_post_url = encodeURIComponent("' . $post_id . '");
							//]]>
							</script>
							<script type="text/javascript" src="http://www.intensedebate.com/js/genericLinkWrapperV2.js"></script>
					</span>';
				} else {//facebook
					$link = "<a class=\"tzcontentslider-comments\" href='$url'>Comments (<fb:comments-count href='$url'></fb:comments-count>)</a>";
				}
				return $link;
			}	
		}
	}
}