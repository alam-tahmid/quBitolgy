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

class PlotalotViewItem extends JViewLegacy
{
function display($tpl = null)
{
global $chart_types;

	if ($this->chart_data->id == 0)					// creating a new record
		JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('COM_PLOTALOT_NEW_ITEM'), 'plotalot.png');
	else
		JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('COM_PLOTALOT_EDIT_ITEM').' '.$this->chart_data->id, 'plotalot.png' );

	JToolBarHelper::apply();
	JToolBarHelper::save();
	if ($this->chart_data->id > 0)
		JToolBarHelper::save2copy();
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
	JToolBarHelper::preferences(LAP_COMPONENT,350,450);
	JToolBarHelper::custom('help', 'help.png', 'help_f2.png', 'JHELP', false);
		
// load our front end css, if it exists

	LAP_view::load_styles();

// get component parameters
	
	$params = JComponentHelper::getParams(LAP_COMPONENT);

	$this->tooltips = $params->get('tooltips',1);

	if ($params->get('autocomplete',1))
		{
		$autocomplete = 'autocomplete="off"';
		if (empty($this->chart_data->db_user))		// some browsers don't respect autocomplete="off"
			$this->chart_data->db_user = '-';	    // the model will intercept this and remove it
		}
	else
		$autocomplete = '';

	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" <?php echo $autocomplete; ?> >

	<input type="hidden" name="option" value="<?php echo LAP_COMPONENT; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->chart_data->id; ?>" />
	<input type="hidden" name="chart_type" value="<?php echo $this->chart_data->chart_type; ?>" />
	<input type="hidden" name="y_size" value="1" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="plot_enable_0" value="1" />
	<?php echo JHTML::_('form.token'); ?>

	<table class="plot_table">
	<tr>
		<td class="pright">
			<?php echo $this->make_prompt('COM_PLOTALOT_NAME', 'COM_PLOTALOT_TOOLTIP_CHART_NAME'); ?>
		</td>
		<td>
			<?php 
			echo '<input type="text" class="p_short" name="chart_name" id="chart_name" size="80" 
			maxlength="250" value="'.htmlspecialchars($this->chart_data->chart_name).'" />';
			?>
		</td>
	</tr>

	<tr>
		<td class="pright">
			<?php echo $this->make_prompt('COM_PLOTALOT_DATABASE', 'COM_PLOTALOT_TOOLTIP_DB_NAME'); ?>
		</td>
		<td>
				<?php
				echo '<input type="text" class="p_short" name="db_name" size="18" maxlength="250" value="'.
				htmlspecialchars($this->chart_data->db_name).'" />';
				echo ' '.$this->make_prompt('COM_PLOTALOT_HOST', 'COM_PLOTALOT_TOOLTIP_DB_HOST');
				echo '<input type="text" class="p_short" name="db_host" size="10" maxlength="250" value="'.
				htmlspecialchars($this->chart_data->db_host).'" />';
				echo ' '.$this->make_prompt('COM_PLOTALOT_USER', 'COM_PLOTALOT_TOOLTIP_DB_USER');
				echo '<input type="text" class="p_short" name="db_user" size="10" maxlength="250" value="'.
				htmlspecialchars($this->chart_data->db_user).'" '.$autocomplete.' />';
				echo ' '.$this->make_prompt('COM_PLOTALOT_PASSWORD', 'COM_PLOTALOT_TOOLTIP_DB_USER');
				echo '<input type="password" class="p_short" name="db_pass" size="9" maxlength="250" value="'.$this->chart_data->db_pass.'" '.$autocomplete.' />';
				?>
		</td>
	</tr>

	<tr>
		<td class="pright" style="vertical-align: top;">
			<?php echo $this->make_prompt('COM_PLOTALOT_QUERY', 'COM_PLOTALOT_TOOLTIP_TABLE_QUERY'); ?>
		</td>
		<td>
			<?php
			if (isset($this->chart_data->plot_array[0]['query']))
				$query = $this->chart_data->plot_array[0]['query'];
			else
				$query = '';
			echo '<textarea class="p_short" name="query[0]" rows="6" cols="100">'.htmlspecialchars($query).'</textarea>';
			?>
		</td>
	</tr>
	</table>

	</form>
	<?php
		
// if new, don't try to draw the chart
	
	if ($this->chart_data->id == 0)
		return;
	
// create the item text

	$plotalot = new Plotalot;
	if ($this->chart_data->db_user == '-')
		$this->chart_data->db_user = '';
	$chart_html = $plotalot->drawChart($this->chart_data);

// Show any errors

	if ($plotalot->error != '')
		echo '<div class="plotalot_error">'.JText::_('COM_PLOTALOT_ERROR').': '.$plotalot->error.'</div>';
	if ($plotalot->warning != '')
		echo '<div class="plotalot_error">'.JText::_('COM_PLOTALOT_WARNING').': '.$plotalot->warning.'</div>';

// Output the text

	echo '<div style="float:left; margin-left:35px; padding:5px; border:2px solid black; background-color: white;">'.
		$chart_html.'</div>';
}

//-------------------------------------------------------------------------------
// Make a prompt with a tooltip
//
function make_prompt($prompt, $tooltip, $extra='')
{
	if (!$this->tooltips)
		return JText::_($prompt);
		
	$prompt_html = JText::_($prompt);

	return '<span class="hasTip" title="'.htmlspecialchars(JText::_($tooltip), ENT_COMPAT, 'UTF-8').'" '.$extra.'>'.$prompt_html.'</span>';
}


}