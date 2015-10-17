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

class PlotalotViewTable extends JViewLegacy
{
function display($tpl = null)
{
global $chart_types;

	if ($this->chart_data->id == 0)					// creating a new record
		JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('COM_PLOTALOT_NEW_TABLE'), 'plotalot.png');
	else
		JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('COM_PLOTALOT_EDIT_TABLE').' '.$this->chart_data->id, 'plotalot.png' );

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

	$background = $params->get('background','FFFFFF');
	if ((strcasecmp($background,'transparent') != 0)
	and ($background[0] != '#'))
		$background = '#'.$background;

	if ($params->get('autocomplete',1))
		{
		$autocomplete = 'autocomplete="off"';
		if (empty($this->chart_data->db_user))		// some browsers don't respect autocomplete="off"
			$this->chart_data->db_user = '-';	    // the model will intercept this and remove it
		}
	else
		$autocomplete = '';

	$this->default_styles();
	
// make the Javascript that hides and enables the chart type dependent fields
// it is called by the 'domready' function and the 'onchange' function of the chart type list selector

	$js = self::javascript();
	$document = JFactory::getDocument();
	$document->addScriptDeclaration($js);
	$dom_ready = "\nwindow.addEvent('domready', function() {plotalot_fields(".$this->chart_data->chart_type.");});\n";
	$document->addScriptDeclaration($dom_ready);

// make the chart type list

	$plotutil = new Plotalot_Utility;
	$chart_type_list = LAP_view::make_list('chart_type', $this->chart_data->chart_type, $plotutil->table_types, 0, 'onchange="plotalot_fields(this.value)"');

	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" <?php echo $autocomplete; ?> >

	<input type="hidden" name="option" value="<?php echo LAP_COMPONENT; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->chart_data->id; ?>" />
	<input type="hidden" name="chart_type" value="<?php echo $this->chart_data->chart_type; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="plot_enable_0" value="1" />
	<?php echo JHTML::_('form.token'); ?>

	<table class="plot_table">
	<tr>
	<td width="50%" class="ptop">
		<fieldset class="adminform plotalot_form"><legend><?php echo JText::_('COM_PLOTALOT_CHART_TYPE_TABLE'); ?></legend>
		<table cellspacing="0" width="100%">
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_NAME', 'COM_PLOTALOT_TOOLTIP_CHART_NAME'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_short" name="chart_name" size="40" 
					maxlength="250" value="'.htmlspecialchars($this->chart_data->chart_name).'" />';
				echo ' '.JText::_('COM_PLOTALOT_TYPE').' ';
				echo $chart_type_list;
				echo '<span id="pl_col_heads">';
				echo ' ';
				echo LAP_view::make_checkbox('legend_type', $this->chart_data->legend_type,$this->make_prompt('COM_PLOTALOT_COL_HEADINGS', 'COM_PLOTALOT_TOOLTIP_TABLE_HEADINGS'));
				echo '</span>';
				?>
			</td>
		</tr>

		<tr id="gv_sizes">
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_SIZE'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_short" name="x_size" size="8" 
					value="'.$this->chart_data->x_size.'" />';
				echo ' x ';
				echo '<input type="text" class="p_short" name="y_size" size="8" 
					value="'.$this->chart_data->y_size.'" />';
				echo ' ';
				echo LAP_view::make_checkbox('chart_option', $this->chart_data->chart_option,JText::_('COM_PLOTALOT_ROW_NUMBERS'));
				?>
			</td>
		</tr>

		<tr id="pl_title">
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_TITLE', 'COM_PLOTALOT_TOOLTIP_TITLE'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long" name="chart_title" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->chart_title).'" />';
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
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_MAX_ROWS', 'COM_PLOTALOT_TOOLTIP_TABLE_MAX_ROWS'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_short" name="y_labels" size="10" 
				maxlength="10" value="'.$this->chart_data->y_labels.'" />';
				?>
			</td>
		</tr>
		
		<tr id="gv_extra_params">
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_EXTRA_PARAMS', 'COM_PLOTALOT_TOOLTIP_EXTRA_PARAMS'); ?>
			</td>
			<td>
				<?php 
				echo '<textarea name="extra_parms" rows="1" style="width:97% !important;">'.htmlspecialchars($this->chart_data->extra_parms).'</textarea>';
				?>
			</td>
		</tr>

		</table>
		</fieldset>
	</td>
	<td width="50%" class="ptop">
		<fieldset class="adminform plotalot_form"><legend><?php echo JText::_('COM_PLOTALOT_CLASSES'); ?></legend>
		<table width="100%" id="pl_styles">

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_TABLE_STYLE'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long" name="style_pl_table" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['pl_table']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_TITLE_ROW_STYLE'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long" name="style_pl_title" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['pl_title']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_HEADING_ROW_STYLE'); ?>
			</td>
			<td>
				<?php
				echo ' <input type="text" class="p_long" name="style_pl_head" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['pl_head']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_TABLE_ODD_STYLE', 'COM_PLOTALOT_TOOLTIP_TABLE_ODD'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long" name="style_pl_odd" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['pl_odd']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_TABLE_EVEN_STYLE', 'COM_PLOTALOT_TOOLTIP_TABLE_EVEN'); ?>
			</td>
			<td>
				<?php
				echo ' <input type="text" class="p_long" name="style_pl_even" 
				maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['pl_even']).'" />';
				?>
			</td>
		</tr>

		</table>

		<table width="100%" id="gv_styles">

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_HEADING_ROW_STYLE'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long" name="style_gv_head" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_head']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_TABLE_ODD_STYLE'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long" name="style_gv_odd" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_odd']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_TABLE_ROW'); ?>
			</td>
			<td>
				<?php
				echo ' <input type="text" class="p_long" name="style_gv_row" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_row']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_SELECTED_ROW'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long" name="style_gv_selected" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_selected']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_HOVER_ROW'); ?>
			</td>
			<td>
				<?php
				echo ' <input type="text" class="p_long" name="style_gv_hover" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_hover']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_HEADER_CELL'); ?>
			</td>
			<td>
				<?php
				echo ' <input type="text" class="p_long" name="style_gv_hcell" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_hcell']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_TABLE_CELL'); ?>
			</td>
			<td>
				<?php
				echo ' <input type="text" class="p_long" name="style_gv_tcell" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_tcell']).'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo JText::_('COM_PLOTALOT_NUMBER_CELL'); ?>
			</td>
			<td>
				<?php
				echo ' <input type="text" class="p_long" name="style_gv_numcell" 
					maxlength="255" value="'.htmlspecialchars($this->chart_data->style_array['gv_numcell']).'" />';
				?>
			</td>
		</tr>

		</table>

		</fieldset>
	</td>
	</tr>
	</table>
	
	<div><fieldset class="adminform plotalot_form">
	<table cellspacing="0" width="100%">
	<tr>
		<td class="pright" style="vertical-align: top; width:8%;">
			<?php echo $this->make_prompt('COM_PLOTALOT_QUERY', 'COM_PLOTALOT_TOOLTIP_TABLE_QUERY'); ?>
		</td>
		<td>
			<?php
			if (isset($this->chart_data->plot_array[0]['query']))
				$query = $this->chart_data->plot_array[0]['query'];
			else
				$query = '';
			echo '<textarea name="query[0]" rows="8" style="width:97% !important;">'.htmlspecialchars($query).'</textarea>';
			?>
		</td>
	</tr>
	</table>
	</fieldset></div>

	
	<div class="clr"></div>

	</form>
	<?php
		
// if new, don't try to draw the chart
	
	if ($this->chart_data->id == 0)
		return;
		
// create the table	script or html

	$plotalot = new Plotalot;
	if ($this->chart_data->db_user == '-')
		$this->chart_data->db_user = '';
	$chart_text = $plotalot->drawChart($this->chart_data);

// Show any errors

	echo '<div id="error_msg" class="plotalot_error"></div>';		// place for any Javascript errors

	if ($plotalot->error != '')
		echo '<div class="plotalot_error">'.JText::_('COM_PLOTALOT_ERROR').': '.$plotalot->error.'</div>';
	
	if ($plotalot->warning != '')
		echo '<div class="plotalot_error">'.JText::_('COM_PLOTALOT_WARNING').': '.$plotalot->warning.'</div>';

// add an error handler to catch any Javascript errors

	$error_handler = "window.onerror = function(message, url, linenumber) {
	    setTimeout(function() {document.getElementById('error_msg').innerHTML = '".JText::_('COM_PLOTALOT_JAVASCRIPT_ERROR')."'+': '+message;}, 200); };";
	    
	$document = JFactory::getDocument();
	$document->addScriptDeclaration($error_handler);
	
// for Plotalot tables, draw the image html

	if ($this->chart_data->chart_type == CHART_TYPE_PL_TABLE)
		{
		echo '<div>';
		$chart_id = $this->chart_data->id;
		echo '<span style="float:left; background-color:'.$background.'">'.$chart_text.'</span>';
		echo '</div>';
		// echo '<div style="background-color:'.$background.'"></div>';
		return;
		}	

// for Google tables, load the scripts

	if ($this->chart_data->chart_type == CHART_TYPE_GV_TABLE)
		{
		$document = JFactory::getDocument();
		$document->addScript("https://www.google.com/jsapi");
		$document->addCustomTag($chart_text);
		$chart_id = $this->chart_data->id;
		echo '<div>';
		$chart_id = $this->chart_data->id;
		echo '<span id="chart_'.$chart_id.'" style="float:left; background-color:'.$background.'"></span>';
		echo '</div>';
		}

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

//-------------------------------------------------------------------------------
// Make sure all entries exist in the styles array
//
function default_styles()
{
	if (!isset($this->chart_data->style_array['pl_table']))
		$this->chart_data->style_array['pl_table'] = '';
	if (!isset($this->chart_data->style_array['pl_title']))
		$this->chart_data->style_array['pl_title'] = '';
	if (!isset($this->chart_data->style_array['pl_head']))
		$this->chart_data->style_array['pl_head'] = '';
	if (!isset($this->chart_data->style_array['pl_odd']))
		$this->chart_data->style_array['pl_odd'] = '';
	if (!isset($this->chart_data->style_array['pl_even']))
		$this->chart_data->style_array['pl_even'] = '';
	if (!isset($this->chart_data->style_array['gv_head']))
		$this->chart_data->style_array['gv_head'] = '';
	if (!isset($this->chart_data->style_array['gv_odd']))
		$this->chart_data->style_array['gv_odd'] = '';
	if (!isset($this->chart_data->style_array['gv_row']))
		$this->chart_data->style_array['gv_row'] = '';
	if (!isset($this->chart_data->style_array['gv_selected']))
		$this->chart_data->style_array['gv_selected'] = '';
	if (!isset($this->chart_data->style_array['gv_hover']))
		$this->chart_data->style_array['gv_hover'] = '';
	if (!isset($this->chart_data->style_array['gv_hcell']))
		$this->chart_data->style_array['gv_hcell'] = '';
	if (!isset($this->chart_data->style_array['gv_tcell']))
		$this->chart_data->style_array['gv_tcell'] = '';
	if (!isset($this->chart_data->style_array['gv_numcell']))
		$this->chart_data->style_array['gv_numcell'] = '';
}

//-----------------------------------------------------------------------------------------------
// make the Javascript for the chart type selector
//
static function javascript()
{
	$js = "
function plotalot_fields(chart_type)
{	
	switch(parseInt(chart_type))
	{
	case ".CHART_TYPE_GV_TABLE.": 
		document.getElementById('gv_sizes').style.display = 'table-row'; 
		document.getElementById('gv_extra_params').style.display = 'table-row'; 
		document.getElementById('gv_styles').style.display = 'table'; 
		document.getElementById('pl_col_heads').style.display = 'none'; 
		document.getElementById('pl_styles').style.display = 'none'; 
		document.getElementById('pl_title').style.display = 'none'; 
		break;
	default: 
		document.getElementById('gv_sizes').style.display = 'none'; 
		document.getElementById('gv_extra_params').style.display = 'none'; 
		document.getElementById('gv_styles').style.display = 'none'; 
		document.getElementById('pl_col_heads').style.display = 'inline'; 
		document.getElementById('pl_styles').style.display = 'table'; 
		document.getElementById('pl_title').style.display = 'table-row'; 
	}
}
";
	return $js;
}

}