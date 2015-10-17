<?php
/********************************************************************
Product    : Plotalot
Date       : 4 October 2014
Copyright  : Les Arbres Design 2010-2014
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');
require_once JPATH_COMPONENT.'/helpers/view_helper.php';

class PlotalotViewChart extends JViewLegacy
{
function display($tpl = null)
{

// the controller has already populated the data into $this->chart_data

	if ($this->chart_data->id == 0)					// creating a new record
		JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('COM_PLOTALOT_NEW_CHART'), 'plotalot.png');
	else
		JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('COM_PLOTALOT_EDIT_CHART').' '.$this->chart_data->id, 'plotalot.png' );

	JToolBarHelper::apply();
	JToolBarHelper::save();
	if ($this->chart_data->id > 0)
		JToolBarHelper::save2copy();
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
	JToolBarHelper::preferences(LAP_COMPONENT,350,450);
	JToolBarHelper::custom('help', 'help.png', 'help_f2.png', 'JHELP', false);

// load our front end css, if it exists

	LAP_view::load_styles();
	
// are we coming back with a validation failure?	

	if ((isset($this->stored)) and ($this->stored === false))
		$validation_failed = true;
	else
		$validation_failed = false;
	
// if we came back here as a result of the extra save button, scroll down so we can see the chart
// - unless it was a validation failure, in which case we should stay at the top

	$document = JFactory::getDocument();
	$jinput = JFactory::getApplication()->input;
	$task = $jinput->get('task', '', 'STRING');					// 'save' or 'apply'
	if (($task == 'apply2') and (!$validation_failed))
		{
		$js = "\n window.addEvent('domready', function() {document.getElementById('chart_area').scrollIntoView();});";
		$document->addScriptDeclaration($js);
		}

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

		
// load the Javascript that hides and enables the chart type dependent fields
// it is called by the 'domready' function and the 'onchange' function of the chart type list selector

	$document->addScript(JURI::base(true).'/components/com_plotalot/assets/chart_edit.js?2');
	$dom_ready = "\nwindow.addEvent('domready', function() {plotalot_fields(".$this->chart_data->chart_type.");});\n";
	$document->addScriptDeclaration($dom_ready);

// make objects

	$plotalot = new Plotalot;
	$plotutil = new Plotalot_Utility;

// make the chart type list

	$chart_type_list = LAP_view::make_list('chart_type', $this->chart_data->chart_type, $plotutil->chart_types, CHART_TYPE_LINE, 'onchange="plotalot_fields(this.value)"');

// build the form

	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" <?php echo $autocomplete; ?> >

	<input type="hidden" name="option" value="<?php echo LAP_COMPONENT; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->chart_data->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />

	<table class="plot_table">
	<tr>
	<td width="50%" class="ptop">
		<fieldset class="adminform plotalot_form"><legend><?php echo JText::_('COM_PLOTALOT_CHART'); ?></legend>
		<table class="plot_table">
		<tr>
			<td class="pright">
				<?php
				echo $this->make_prompt('COM_PLOTALOT_NAME', 'COM_PLOTALOT_TOOLTIP_CHART_NAME');
				?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_long" name="chart_name" maxlength="250" value="'.htmlspecialchars($this->chart_data->chart_name).'" />';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php 
				echo JText::_('COM_PLOTALOT_TYPE');
				?>
			</td>
			<td>
				<?php 
				echo $chart_type_list;
				echo '<span class="pjh_all pjh_legend_type" >';
				echo ' '.JText::_('COM_PLOTALOT_LEGEND');
				echo LAP_view::make_list('legend_type', $this->chart_data->legend_type, $plotutil->legendTypes);
				echo '</span>';
				
				echo '<span class="pjh_all pjh_chart_option_pie" >';
				echo ' '.JText::_('COM_PLOTALOT_PIE_TEXT_TYPE');
				echo LAP_view::make_list('pie_chart_option', $this->chart_data->chart_option, $plotutil->pieTextTypes);
				echo '</span>';
				
				echo '<span class="pjh_all pjh_chart_option_bar" >';
				echo ' '.LAP_view::make_checkbox('bar_chart_option', $this->chart_data->chart_option,JText::_('COM_PLOTALOT_ORDERED'));
				echo '</span>';
				?>
			</td>
		</tr>
		
		<tr>
			<td class="pright">
				<?php 
				echo JText::_('COM_PLOTALOT_SIZE');
				?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_short" name="x_size" size="8" 
					value="'.$this->chart_data->x_size.'" />';
				echo ' x ';
				echo '<input type="text" class="p_short" name="y_size" size="8" 
					value="'.$this->chart_data->y_size.'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php 
				echo $this->make_prompt('COM_PLOTALOT_TITLE', 'COM_PLOTALOT_TOOLTIP_TITLE');
				?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_short pjd_all pjd_chart_title" name="chart_title" size="60" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->chart_title).'" />';
				echo ' '.$this->make_prompt('COM_PLOTALOT_COLOUR', 'COM_PLOTALOT_TOOLTIP_COLOUR').' ';
				echo '<input type="text" class="p_short pjd_all pjd_chart_title color {required:false}" name="chart_title_colour" size="6" value="'.$this->chart_data->chart_title_colour.'" />';
				?>
			</td>
		</tr>

		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_BACKGROUND', 'COM_PLOTALOT_TOOLTIP_COLOUR'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_short color {required:false}" name="back_colour" size="6" value="'.$this->chart_data->back_colour.'" />';
				echo '<span class="pjh_all pjh_show_grid" >';
				echo ' '.LAP_view::make_checkbox('show_grid', $this->chart_data->show_grid, $this->make_prompt('COM_PLOTALOT_GRID', 'COM_PLOTALOT_TOOLTIP_GRID'));
				echo '</span>';
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
			<td class="pright ptop">
				<?php echo $this->make_prompt('COM_PLOTALOT_EXTRA_PARAMS', 'COM_PLOTALOT_TOOLTIP_EXTRA_PARAMS'); ?>
			</td>
			<td>
				<?php 
				echo '<textarea class="p_long" name="extra_parms" rows="1">'.htmlspecialchars($this->chart_data->extra_parms).'</textarea>';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright ptop">
				<?php echo $this->make_prompt('COM_PLOTALOT_EXTRA_COLUMNS', 'COM_PLOTALOT_TOOLTIP_EXTRA_COLUMNS'); ?>
			</td>
			<td>
				<?php 
				echo '<textarea class="p_long" name="extra_columns" rows="1">'.htmlspecialchars($this->chart_data->extra_columns).'</textarea>';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_NUM_PLOTS', 'COM_PLOTALOT_TOOLTIP_NUM_PLOTS'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_short pjd_all pjd_num_plots" size="5" name="num_plots" value = "'.$this->chart_data->num_plots.'" />';
				echo ' '.LAP_view::make_checkbox('sort_plots', false,
					$this->make_prompt('COM_PLOTALOT_SORT_PLOTS', 'COM_PLOTALOT_TOOLTIP_SORT_PLOTS'));
				echo ' '.LAP_view::make_checkbox('show_raw_data', $this->chart_data->show_raw_data,
					$this->make_prompt('COM_PLOTALOT_SHOW_RAW', 'COM_PLOTALOT_TOOLTIP_SHOW_RAW'));
				echo ' '.LAP_view::make_checkbox('show_script', $this->chart_data->show_script,
					$this->make_prompt('COM_PLOTALOT_SHOW_SCRIPT', 'COM_PLOTALOT_TOOLTIP_SHOW_SCRIPT'));
				?>
			</td>
		</tr>
		</table>
		</fieldset>
	</td>
	<td width="50%" class="ptop">
		<fieldset class="adminform plotalot_form"><legend><?php echo JText::_('COM_PLOTALOT_AXES'); ?></legend>
		<table class="plot_table">
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_X_TITLE', 'COM_PLOTALOT_TOOLTIP_X_TITLE'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_long pjd_all pjd_xy_titles" name="x_title" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->x_title).'" />';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_X_START', 'COM_PLOTALOT_TOOLTIP_X_START'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_long pjd_all pjd_x_params" name="x_start" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->x_start).'" />';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_X_END', 'COM_PLOTALOT_TOOLTIP_X_END'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_long pjd_all pjd_x_params" name="x_end" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->x_end).'" />';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_X_NUM_LABELS', 'COM_PLOTALOT_TOOLTIP_X_NUM_LABELS'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_short pjd_all pjd_x_params" size="5" name="x_labels" value = "'.$this->chart_data->x_labels.'" />';
				echo '<span class="pjh_all pjh_x_format" >';
				echo ' '.$this->make_prompt('COM_PLOTALOT_X_LABEL_FORMAT', 'COM_PLOTALOT_TOOLTIP_X_FORMAT').' ';
				echo LAP_view::make_list('x_format', $this->chart_data->x_format, $plotutil->xDataFormats,FORMAT_NONE);
				echo '</span>';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_Y_TITLE', 'COM_PLOTALOT_TOOLTIP_Y_TITLE'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_long pjd_all pjd_xy_titles" name="y_title" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->y_title).'" />';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_Y_START', 'COM_PLOTALOT_TOOLTIP_Y_START'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_long pjd_all pjd_y_params" name="y_start" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->y_start).'" />';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_Y_END', 'COM_PLOTALOT_TOOLTIP_Y_END'); ?>
			</td>
			<td>
				<?php 
				echo '<input type="text" class="p_long pjd_all pjd_y_params" name="y_end" maxlength="2000" value="';
				echo htmlspecialchars($this->chart_data->y_end).'" />';
				?>
			</td>
		</tr>
		<tr>
			<td class="pright">
				<?php echo $this->make_prompt('COM_PLOTALOT_Y_NUM_LABELS', 'COM_PLOTALOT_TOOLTIP_Y_NUM_LABELS'); ?>
			</td>
			<td>
				<?php
				echo '<input type="text" class="p_short pjd_all pjd_y_labels" size="5" name="y_labels" value = "'.$this->chart_data->y_labels.'" />';
				echo '<span class="pjh_all pjh_y_format" >';
				echo ' '.$this->make_prompt('COM_PLOTALOT_Y_LABEL_FORMAT', 'COM_PLOTALOT_TOOLTIP_Y_FORMAT').' ';
				echo LAP_view::make_list('y_format', $this->chart_data->y_format, $plotutil->yDataFormats,FORMAT_NONE);
				echo '</span>';
				?>
			</td>
		</tr>
		</table>
		</fieldset>
	</td>
	</tr>
	</table>

	<?php
	
// the plots array

	for ($i = 0; $i < $this->chart_data->num_plots; $i++)
		{
		if (!isset($this->chart_data->plot_array[$i]['legend']))
			$this->chart_data->plot_array[$i]['legend'] = '';
			
		if (!isset($this->chart_data->plot_array[$i]['colour']))
			$this->chart_data->plot_array[$i]['colour'] = '';
			
		if (!isset($this->chart_data->plot_array[$i]['style']))
			$this->chart_data->plot_array[$i]['style'] = 0;
		$this->chart_data->plot_array[$i]['style'] = $plotalot->checkPlotStyle($this->chart_data->chart_type, $this->chart_data->plot_array[$i]['style']);

		if (!isset($this->chart_data->plot_array[$i]['query']))
			$this->chart_data->plot_array[$i]['query'] = '';

		if (!isset($this->chart_data->plot_array[$i]['enable']))		// if not present ..
			$this->chart_data->plot_array[$i]['enable'] = false;		// .. plot is disabled
			
		if (($this->chart_data->plot_array[$i]['legend'] == '') and ($this->chart_data->plot_array[$i]['query'] == ''))
			$this->chart_data->plot_array[$i]['enable'] = true;			// make new plots enabled
		
		echo "\n\n".'<div><fieldset class="adminform plotalot_form"><legend>'.JText::_('COM_PLOTALOT_PLOT').' '.($i+1).'</legend>';
		
		echo '<table width="100%"><tr>';
		echo '<td style="width:20%; vertical-align:top;">';

		echo "\n".'<table>';
		echo "\n".'<tr><td>'.$this->make_prompt('COM_PLOTALOT_NAME', 'COM_PLOTALOT_TOOLTIP_PLOT_NAME').'</td>';
		echo '<td><input type="text" class="p_short" size="25" name="legend['.$i.']" value = "'.htmlspecialchars($this->chart_data->plot_array[$i]['legend']).'" /></td>';
		echo '</tr>';
		
		echo "\n".'<tr><td>'.$this->make_prompt('COM_PLOTALOT_COLOUR', 'COM_PLOTALOT_TOOLTIP_COLOUR').'</td>';
		echo '<td><input type="text" class="p_short color {required:false}" name="colour['.$i.']" size="6" value="'.$this->chart_data->plot_array[$i]['colour'].'" /></td>';
		echo '</tr>';

		echo "\n".'<tr class="pjh_all pjh_plot_style_pie"><td>'.$this->make_prompt('COM_PLOTALOT_STYLE', 'COM_PLOTALOT_TOOLTIP_PLOT_STYLE').'</td>';
		echo '<td>'.LAP_view::make_list("pie_style[$i]", $this->chart_data->plot_array[$i]['style'], $plotutil->lineStylesPie).'</td>';
		echo '</tr>';
			
		echo "\n".'<tr class="pjh_all pjh_plot_style_line"><td>'.$this->make_prompt('COM_PLOTALOT_STYLE', 'COM_PLOTALOT_TOOLTIP_PLOT_STYLE').'</td>';
		echo '<td>'.LAP_view::make_list("line_style[$i]", $this->chart_data->plot_array[$i]['style'], $plotutil->lineStylesLine).'</td>';
		echo '</tr>';
			
		echo "\n".'<tr class="pjh_all pjh_plot_type"><td>'.JText::_('COM_PLOTALOT_TYPE').'</td>';
		echo '<td>'.LAP_view::make_list("plot_type[$i]", $this->chart_data->plot_array[$i]['style'], $plotutil->comboPlotTypes).'</td>';
		echo '</tr>';
			
		echo "\n".'<tr><td>'.$this->make_prompt('COM_PLOTALOT_ENABLE', 'COM_PLOTALOT_TOOLTIP_PLOT_ENABLE').'</td>';
		echo '<td>'.LAP_view::make_checkbox("enable[$i]", $this->chart_data->plot_array[$i]['enable']).'</td>';
		echo '</tr>';
		echo "\n".'</table>';
		
		echo '</td>';	// width:20%
		
		echo "\n<td>";
		echo LAP_view::make_info(JText::_('COM_PLOTALOT_TOOLTIP_PLOT_QUERY'),'','style="float:left";');
		echo '<textarea class="p_long" name="query['.$i.']" rows="5" style="white-space:normal !important; word-wrap:normal !important;">'.
			htmlspecialchars($this->chart_data->plot_array[$i]['query']).'</textarea>';
		echo '</td>';
		
		echo '</tr></table>';
		echo '</fieldset></div>';
		}
	?>

	</form>
	<?php
	
// if new, don't try to draw the chart
	
	if ($this->chart_data->id == 0)
		return;
		
// if validation failed in the model, don't try to draw the chart
	
	if ($validation_failed)
		return;

// create the chart	script	

	if ($this->chart_data->db_user == '-')
		$this->chart_data->db_user = '';
	$chart_script = $plotalot->drawChart($this->chart_data);
	
// Show any errors

	echo '<div id="error_msg" class="plotalot_error" style="margin-bottom:5px;"></div>';		// place for any Javascript errors

	if ($plotalot->error != '')
		echo '<div class="plotalot_error">'.JText::_('COM_PLOTALOT_ERROR').': '.$plotalot->error.'</div>';
	
	if ($plotalot->warning != '')
		echo '<div class="plotalot_error">'.JText::_('COM_PLOTALOT_WARNING').': '.$plotalot->warning.'</div>';

// add an error handler to catch any Javascript errors

	$error_handler = "\n window.onerror = function(message, url, linenumber) {
	    setTimeout(function() {document.getElementById('error_msg').innerHTML = '".JText::_('COM_PLOTALOT_JAVASCRIPT_ERROR')."'+': '+message;}, 200); };";
	    
	$document->addScriptDeclaration($error_handler);
	
// put the chart script into the document header
	
	$document->addScript("https://www.google.com/jsapi");
	$document->addCustomTag($chart_script);

// the place for the Google javascript to draw in, and an extra save button
// if the chart is responsive, make the chart container an arbitrary 640 x 480	
// if not, let the chart define the size of the container

	if (empty($this->chart_data->x_size))
		$styles = 'width:500px; height:300px; ';
	else
		$styles = 'height:'.$this->chart_data->y_size.'px; width:'.$this->chart_data->x_size.'px; ';

	echo "\n".'<div>';
	echo "\n".'<div id="chart_area" style="'.$styles.'float:left; border:1px solid lightgray;">';
	$chart_id = $this->chart_data->id;
	echo '<span id="chart_'.$chart_id.'" style="'.$styles.'float:left; background-color:'.$background.'"></span>';
	echo '</div>';
	echo self::save_button();
	echo '</div>';
	
// if show raw data was selected, show each plot as a table of up to 20 rows

	if ($this->chart_data->show_raw_data)
		{
		echo '<div style="clear:left;"></div>';
		echo '<h3>'.JText::_('COM_PLOTALOT_SHOW_RAW').'</h3>';
		$this->chart_data->chart_type = CHART_TYPE_PL_TABLE;
		$this->chart_data->chart_css_style = 'border="1" cellspacing="0" cellpadding="2"';
		$this->chart_data->y_labels = 20;
		for ($p = 0; $p < $this->chart_data->num_plots; $p++)
			{
			echo '<div style="float:left; margin:5px;">';
			if (empty($this->chart_data->plot_array[$p]['enable']))
				continue;
			if (!$this->chart_data->plot_array[$p]['enable'])			// plot is disabled
				continue;
			if ($this->chart_data->plot_array[$p]['query'] == '')		// no query
				continue;
			$this->chart_data->plot_array[0]['query'] = $this->chart_data->plot_array[$p]['query']; // table query is always plot zero
			$this->chart_data->chart_title = JText::_('COM_PLOTALOT_RAW_DATA').', '.JText::_('COM_PLOTALOT_PLOT').' '.($p + 1)." (".$this->chart_data->plot_array[$p]['legend'].")";
			$chart_html = $plotalot->drawChart($this->chart_data, true);	// draw table but do not overwrite chart trace
			echo $chart_html;
			echo '';
			}
		}
		
// if show script was selected, show it

	if ($this->chart_data->show_script)
		{
		$viewable_script = str_replace("\n<script type=\"text/javascript\">\n",'',$chart_script);
		$viewable_script = str_replace('</script>','',$viewable_script);
		echo '<div style="clear:left;"></div>';
		echo '<h3>'.JText::_('COM_PLOTALOT_SHOW_SCRIPT').'</h3>';
		echo '<pre>';
		echo $viewable_script;
		echo '</pre>';
		}
}

//------------------------------------------------------------------------------
// draw an extra save button
//
static function save_button()
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$onclick = 'onclick="Joomla.submitbutton('."'apply2'".')"';
		$html = '<span class="extra_save_button"><a href="#" '.$onclick.'>
				<span class="icon-32-apply"></span>'.JText::_('JAPPLY').'</a></span>';
		}
	else
		{
		$onclick = 'onclick="Joomla.submitbutton('."'apply2'".')"';
		$html = '<span style="float:right"><button href="#" '.$onclick.'" class="btn btn-small btn-success" style="width:148px;font-size:12px;";>
				<i class="icon-apply icon-white"></i>'.JText::_('JAPPLY').'</button></span>';
		}
	return $html;
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

} // class