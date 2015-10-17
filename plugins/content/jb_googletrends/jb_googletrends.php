<?php
/**
 * @package     Joomla.site
 * @subpackage  Plugin.content.jb_googletrends
 *
 * @copyright   (C) 2014 Joomlabuzz.com
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @author      Dave Horsfall
 */

defined('_JEXEC') or die;

class PlgContentJb_GoogleTrends extends JPlugin
{
	/**
	 * Plugin that loads Google Trends gadgets within content.
	 *
         * @access  public
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 * @return  mixed    True if there is no error. Void otherwise.
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed.
		if ($context == 'com_finder.indexer')
		{
			return true;
		}

		// Simple performance check to determine whether bot should process further
		if (strpos($article->text, 'googletrends') === false)
		{
			return true;
		}

		// Expression to search for (positions)
		$regex = '/{googletrends\s(.*?)}/i';

		// Find all instances of plugin and put in $matches for 'googletrends'
		// $matches[0] is full pattern match, $matches[1] is the position
		preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);

		if ($matches)
		{
			foreach ($matches as $match)
			{
                                // Setup attributes registry.
                                $attributes = new JRegistry;

                                // Extract the terms from the code.
                                preg_match('/terms="([^"]+)/', $match[1], $terms);
                                if (!empty($terms[1]))
                                {
                                        $attributes->set('terms', $terms[1]);
                                }
                
                                // Extract the other parameters from the code.                                
				$matcheslist = explode(' ', $match[1]);
                                
                                foreach ($matcheslist as $data)
                                {
                                        $input = explode('=', $data);

                                        if (empty($terms[1]) && $input[0] == 'terms')   $attributes->set('terms', $input[1]);
                                        if ($input[0] == 'chart')   $attributes->set('chart', $input[1]);
                                        if ($input[0] == 'width')   $attributes->set('width', $input[1]);
                                }

				$output = $this->_load($attributes);

				// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				$article->text = preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
			}
		}
	}

	/**
	 * Loads and renders the Google Trends gadget.
	 *
         * @access  protected
	 * @param   string     $params  The parameters.
	 * @return  string     The rendered gadget output.
	 */
	protected function _load($attributes)
	{
                if ($attributes->get('terms') == '')
                {
                        return "no terms";
                }

                // Add page assets.
                JHtml::_('jquery.framework');
                $doc = JFactory::getDocument();
                
                // Define parameters.
                $hl = JFactory::getLanguage()->getTag();
                $cid = $this->_getCid($attributes->get('chart', $this->params->get('chart')));
                $w = (int) $attributes->get('width', $this->params->get('width', 500));
                $h = (int) $this->_getHeight($cid, $w);

                // Add style to control the iframe.
                $doc->addStyleDeclaration("
                .jb-googletrends iframe {
                        max-width: 100%;
                }
                "); 
                
                // Resize chart appropriately.
                $doc->addScriptDeclaration("
jQuery(document).ready(function(){
  jQuery(window).on('resize', function () {
    jQuery('.jb-googletrends iframe').each(function(index) {
      if(jQuery(this).length) {
        var src = jQuery(this).attr('src');
        var width = Math.min(jQuery(this).parent().width(), jQuery(this).attr('width'));
        var update = src.replace(/(w=).*?(&)/,'$1' + width + '$2');
        jQuery(this).attr('src', update);   
        return false;
      }
      return true;
    });
  }).resize();
});
                "); 

                ob_start();
                ?>
<div class="jb-googletrends">
<script type="text/javascript" src="//www.google.com/trends/embed.js?hl=<?php echo $hl; ?>&q=<?php echo $attributes->get('terms'); ?>&cmpt=q&tz&tz&content=1&cid=<?php echo $cid; ?>&export=5&w=<?php echo $w; ?>&h=<?php echo $h; ?>"></script>
</div>
                <?php
                $html = ob_get_contents();
                ob_end_clean();
                return $html;           
        }
        
	/**
	 * Returns the Google Trends chart code.
	 *
         * @access  protected
	 * @param   string     $chart  The readable chart string.
	 * @return  string     The Google Trends chart code.
	 */
	protected function _getCid($chart)
	{
                switch ($chart)
                {
                        // Time series graph
                        case 'timeseries';
                                return 'TIMESERIES_GRAPH_0';
                        break;
                        // Regional interest chart (region wise map)
                        case 'regionalmap';
                                return 'GEO_MAP_0_0';
                        break;
                        // Regional interest chart (city wise map)
                        case 'citymap';
                                return 'GEO_MAP_0_1';
                        break;
                        // Regional interest chart (region wise table)
                        case 'regionaltable';
                                return 'GEO_TABLE_0_0';
                        break;
                        // Regional interest chart (city wise table)
                        case 'citytable';
                                return 'GEO_TABLE_0_1';
                        break;
                        // Top 10 queries
                        case 'topqueries';
                                return 'TOP_QUERIES_0_0';
                        break;
                        // Top 5 related searches
                        case 'relatedsearches';
                                return 'TOP_ENTITIES_0_0';
                        break;
                }
        }
        
	/**
	 * Calculated the Google Trends gadget height.
	 *
         * @access  protected
	 * @param   string     $cid  The Google Trends chart code.
	 * @param   integer    $w    The chart width.
	 * @return  string     The chart height.
	 */
	protected function _getHeight($cid, $w)
	{
                switch ($cid)
                {
                        // Time series graph
                        case 'TIMESERIES_GRAPH_0';
                                $h = 350;
                        break;
                        case 'GEO_MAP_0_0'; // Regional interest chart (region wise map)
                        case 'GEO_MAP_0_1'; // Regional interest chart (city wise map)
                                $h = 530;
                        break;
                        case 'GEO_TABLE_0_0'; // Regional interest chart (region wise table)
                        case 'GEO_TABLE_0_1'; // Regional interest chart (city wise table)
                               $h = 350;
                        break;                        
                        case 'TOP_QUERIES_0_0'; // Top 10 queries
                        case 'TOP_ENTITIES_0_0'; // Top 5 related searches
                                $h = 440;
                        break;
                }
                
                return $h;
        }
}
