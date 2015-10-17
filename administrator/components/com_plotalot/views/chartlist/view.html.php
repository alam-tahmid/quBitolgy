<?php
/********************************************************************
Product    : Plotalot
Date       : 1 March 2015
Copyright  : Les Arbres Design 2010-2015
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');
require_once JPATH_COMPONENT.'/helpers/view_helper.php';

class PlotalotViewChartList extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAP_COMPONENT_NAME, 'plotalot.png');
	JToolBarHelper::custom('new_chart', 'chart.png', 'chart_f2.png', 'COM_PLOTALOT_NEW_CHART', false);
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		JToolBarHelper::custom('new_table', 'table.png', 'table_f2.png', 'COM_PLOTALOT_NEW_TABLE', false);
		JToolBarHelper::custom('new_item',  'item.png',  'item_f2.png',  'COM_PLOTALOT_NEW_ITEM',  false);
		JToolBarHelper::custom('edit_css',  'css.png',   'css_f2.png',   'COM_PLOTALOT_CSS',       false);
		if ($this->cache_count > 0)
			JToolBarHelper::custom('clear_cache', 'remove.png', 'remove_f2.png', 'JGLOBAL_SUBMENU_CLEAR_CACHE', false);
		}
	else
		{
		JToolBarHelper::custom('new_table', 'list.png',    'list_f2.png',    'COM_PLOTALOT_NEW_TABLE', false);
		JToolBarHelper::custom('new_item',  'pencil-2.png','pencil-2_f2.png','COM_PLOTALOT_NEW_ITEM',  false);
		JToolBarHelper::custom('edit_css',  'edit.png',    'edit_f2.png',    'COM_PLOTALOT_CSS',       false);
		if ($this->cache_count > 0)
			JToolBarHelper::custom('clear_cache', 'file-remove.png', 'file-remove_f2.png', 'JGLOBAL_SUBMENU_CLEAR_CACHE', false);
		}
	JToolBarHelper::unpublishList();
	JToolBarHelper::publishList();
	JToolBarHelper::deleteList();
	JToolBarHelper::preferences(LAP_COMPONENT,350,450);
	JToolBarHelper::custom('help', 'help.png', 'help_f2.png', 'JHELP', false);

// if the table structure is incorrect, don't go any further

	if (!$this->check_table)
		{
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_PLOTALOT_BAD_TABLE_STRUCTURE'), 'error');
		return;
		}
		
// if the mysql_ functions are not supported, don't go any further

	if (!function_exists('mysql_connect'))
		{
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_PLOTALOT_MYSQL_NOT_SUPPORTED'), 'error');
		return;
		}

// get the current filter	

	$app = JFactory::getApplication();
	$filter_state = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_state','filter_state','','word');
	$filter_chart_type = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_chart_type','filter_chart_type',CHART_TYPE_ANY,'int');

// Create the state filter html

	$lists['state']	= JHtml::_('grid.state', $filter_state );

	$plotutil = new Plotalot_Utility;
	$lists['chart_type'] = LAP_view::make_list('filter_chart_type', $filter_chart_type, $plotutil->chart_categories, CHART_TYPE_ANY, 'onchange="submitform( );"');					

// get the order states				

	$filter_order = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_order', 'filter_order', 'chart_name');
	$filter_order_Dir = $app->getUserStateFromRequest(LAP_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'asc');
	$lists['order_Dir'] = $filter_order_Dir;
	$lists['order'] = $filter_order;

// get the current Joomla database name

	$app = JFactory::getApplication();
	$website_database = $app->getCfg('db');

	$numrows = count($this->items);
	$check_all = 'onclick="Joomla.checkAll(this);"';

	$plotutil = new Plotalot_Utility;

	?>
	<form action="index.php" method="get" name="adminForm" id="adminForm">

	<input type="hidden" name="option" value="<?php echo LAP_COMPONENT ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />

	<div style="text-align:right">
		<?php
		echo $lists['chart_type'];
		echo '&nbsp;';
		echo $lists['state'];
		?>
	</div>
	
	<div id="editcell">
		<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="5"><?php echo JHtml::_('grid.sort',  'COM_PLOTALOT_ID', 'id', $lists['order_Dir'], $lists['order']); ?></th>
				<th width="20"><input type="checkbox" name="toggle" value="" <?php echo $check_all; ?> /></th>			
				<th width="5%" class="title" nowrap="nowrap" colspan="2"><?php echo JText::_('JPUBLISHED'); ?></th>
				<th><?php echo JHtml::_('grid.sort',  'COM_PLOTALOT_NAME', 'chart_name', $lists['order_Dir'], $lists['order']); ?></th>
				<th><?php echo JHtml::_('grid.sort',  'COM_PLOTALOT_TYPE', 'chart_type', $lists['order_Dir'], $lists['order']); ?></th>
				<th><?php echo JText::_('COM_PLOTALOT_PLOTS'); ?></th>
				<th><?php echo JHtml::_('grid.sort',  'COM_PLOTALOT_DATABASE', 'db_name', $lists['order_Dir'], $lists['order']); ?></th>
				<th><?php echo JHtml::_('grid.sort',  'COM_PLOTALOT_CREATED', 'date_created', $lists['order_Dir'], $lists['order']); ?></th>
				<th><?php echo JHtml::_('grid.sort',  'COM_PLOTALOT_UPDATED', 'date_updated', $lists['order_Dir'], $lists['order']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<?php
		if ($this->items == null)
			{
			echo '</table></div></form>';
			return;
			}
			
		$k = 0;
		$n = count($this->items);
		for ($i=0; $i < $n; $i++)
			{
			$row = &$this->items[$i];
			$checked 	= JHtml::_('grid.id',   $i, $row->id);
			$link 		= JRoute::_(LAP_COMPONENT_LINK.'&task=edit&cid[]='.$row->id);
			
			$icon = '';
			if ($row->sample_id > 0)
				{
				$icon = 'yellow_spot_16.png';		// old 2.xx samples
				$state = JText::_('COM_PLOTALOT_OLD_SAMPLE');
				}
			if ($row->sample_id > 5)
				{
				$icon = 'blue_spot_16.png';			// 3.xx samples
				$state = JText::_('COM_PLOTALOT_SAMPLE');
				}
			if (($row->sample_id == 0) and ($row->state == 1))
				{
				$icon = 'red_bullet_16.png';		// upgraded from 2.xx and not yet saved in 4.xx
				$state = JText::_('COM_PLOTALOT_STATE_UPGRADED');
				}
			if ($icon == '')
				$status_image = '';
			else
				$status_image = '<span title="'.$state.'" class="hasTip"><img src="'.LAP_ADMIN_ASSETS_URL.$icon.'" alt="" /></span>';
			
			$published 	= JHtml::_('grid.published', $row, $i);
			
			switch ($row->chart_type)
				{
				case CHART_TYPE_PL_TABLE:
					$icon = 'c_table.gif'; break;
				case CHART_TYPE_GV_TABLE:
					$icon = 'c_gv_table.gif'; break;
				case CHART_TYPE_SINGLE_ITEM:
					$icon = 'c_item.gif'; break;
				case CHART_TYPE_LINE:
					$icon = 'c_line.gif'; break;
				case CHART_TYPE_AREA:
					$icon = 'c_area.gif'; break;
				case CHART_TYPE_GAUGE:
					$icon = 'c_gauge.gif'; break;
				case CHART_TYPE_SCATTER:
					$icon = 'c_scatter.gif'; break;
				case CHART_TYPE_BAR_H_STACK:
				case CHART_TYPE_BAR_H_GROUP:
					$icon = 'c_bar_h.gif'; break;
				case CHART_TYPE_BAR_V_STACK:
				case CHART_TYPE_BAR_V_GROUP:
					$icon = 'c_bar_v.gif'; break;
				case CHART_TYPE_PIE_2D:
				case CHART_TYPE_PIE_2D_V:
					$icon = 'c_pie_2d.gif'; break;
				case CHART_TYPE_PIE_3D:
				case CHART_TYPE_PIE_3D_V:
					$icon = 'c_pie_3d.gif'; break;
				case CHART_TYPE_TIMELINE:
					$icon = 'c_timeline.gif'; break;
				case CHART_TYPE_BUBBLE:
					$icon = 'c_bubble.gif'; break;
				case CHART_TYPE_COMBO_STACK:
				case CHART_TYPE_COMBO_GROUP:
					$icon = 'c_combo.gif'; break;
				default:
					$icon = 'c_unknown.gif';
				}
			$image_html = '<img src="'.LAP_ADMIN_ASSETS_URL.$icon.'" alt="" />';
			
			echo '<tr class="row'.$k.'">';
			echo '<td>'.$row->id.'</td>';
			echo '<td>'.$checked.'</td>';
			echo '<td align="center">'.$published.'</td>';
			echo '<td>'.$status_image.'</td>';
			echo '<td><a href="'.$link.'">'.$row->chart_name.'</a></td>';
			echo '<td>'.$image_html.' '.$plotutil->chartTypeName($row->chart_type).'</td>';
			echo '<td style="text-align: center;">'.$row->num_plots.'</td>';
			if ($row->db_name == '')
				echo '<td>'.JText::_('JSITE').' ('.$website_database.')</td>';
			else
				echo '<td>'.$row->db_name.'</td>';
			echo '<td>'.$row->date_created.'</td>';
			echo '<td>'.$row->date_updated.'</td>';
			echo '</tr>';
			$k = 1 - $k;
			}
		?>
		</table>
	</div>

	</form>
	<?php
}
}