<?php
/**
 * K2 JComments
 * 
 * @version 1.3.3
 * @author Sergey M. Litvinov (smart@joomlatune.ru)
 * @copyright (C) 2010-2014 by Sergey M. Litvinov (http://www.joomlatune.ru)
 * @license GNU General Public License version 2 or later; see license.txt
 *
 **/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgK2JComments extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		JPlugin::loadLanguage('plg_k2_jcomments', JPATH_ADMINISTRATOR);
	}

	public function onK2CommentsBlock(&$item, &$params, $limitstart)
	{
		$result = '';

		if (!JRequest::getInt('print')) {
	        	$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
		 	if (is_file($commentsAPI)) {
	 			require_once($commentsAPI);
 				$result = JComments::show($item->id, 'com_k2', $item->title);
			}
		}
		return $result;
	}

	public function onK2CommentsCounter(&$item, &$params, $limitstart)
	{
		$result = '';

	       	$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
	 	if (is_file($commentsAPI)) {
	 		require_once($commentsAPI);
			$count = JComments::getCommentsCount($item->id, 'com_k2');

	 		if ($count == 0) {
	 			$link = $item->link.'#addcomments';
	 			$text = JText::_('PLG_K2_JCOMMENTS_LINK_ADD_COMMENT');
			} else {
	 			$link = $item->link.'#comments';
	 			$text = JText::sprintf('PLG_K2_JCOMMENTS_LINK_READ_COMMENTS', $count);
	 		}

	 		$anchor_css = $this->params->get('anchor_css');
	 		$class = empty($anchor_css) ? '' : ' class="' . $anchor_css . '"';

	 		$result = '<a href="' . $link . '"' . $class . ' title="' . $text . '">' . $text . '</a>';
		}
		return $result;
	}

	public function onAfterK2Save(&$item, $isNew)
	{
	        if ($this->params->get('autosubscribe')) {
			if (!empty($item->id) && !empty($item->created_by) && $isNew) {
		        	$commentsAPI = JPATH_SITE . '/components/com_jcomments/jcomments.php';
			 	if (is_file($commentsAPI)) {
			 		require_once($commentsAPI);
					require_once(JPATH_SITE . '/components/com_jcomments/jcomments.subscription.php');
					$manager = JCommentsSubscriptionManager::getInstance();
					$manager->subscribe($item->id, 'com_k2', $item->created_by);
				}
			}
		}
		return true;		
	}
}