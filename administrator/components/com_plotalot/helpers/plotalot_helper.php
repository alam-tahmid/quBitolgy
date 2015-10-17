<?php
/********************************************************************
Product		: Plotalot
Date		: 6 October 2014
Copyright	: Les Arbres Design 2010-2014
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define("LAP_COMPONENT", "com_plotalot");
define("LAP_COMPONENT_LINK", "index.php?option=com_plotalot");
define("LAP_COMPONENT_NAME", "Plotalot");
define("LAP_ADMIN_ASSETS_URL", JURI::root(true).'/administrator/components/com_plotalot/assets/');

// create the new class names used by Joomla 3 and above, if they don't already exist.
// (you can't define a class inside a method of a class, but you can include a file that does so)

if (!class_exists('JControllerLegacy'))
	{
	jimport('joomla.application.component.controller');
	class JControllerLegacy extends JController { };
	}
if (!class_exists('JModelLegacy'))
	{
	jimport('joomla.application.component.model');
	class JModelLegacy extends JModel { };
	}
if (!class_exists('JViewLegacy'))
	{
	jimport('joomla.application.component.view');
	class JViewLegacy extends JView { };
	}
	
class Plotalot_Utility
{
//-------------------------------------------------------------------------------
// Constructor
//
function Plotalot_Utility()
{
	$this->chart_types = array(
		CHART_TYPE_ANY         => JText::_('COM_PLOTALOT_CHART_TYPE_ANY'), 
		CHART_TYPE_PL_TABLE    => JText::_('COM_PLOTALOT_CHART_TYPE_PL_TABLE'),
		CHART_TYPE_GV_TABLE    => JText::_('COM_PLOTALOT_CHART_TYPE_GV_TABLE'),
		CHART_TYPE_SINGLE_ITEM => JText::_('COM_PLOTALOT_CHART_TYPE_SINGLE_ITEM'),
		CHART_TYPE_LINE        => JText::_('COM_PLOTALOT_CHART_TYPE_LINE'),
		CHART_TYPE_AREA        => JText::_('COM_PLOTALOT_CHART_TYPE_AREA'),
		CHART_TYPE_SCATTER     => JText::_('COM_PLOTALOT_CHART_TYPE_SCATTER'),
		CHART_TYPE_GAUGE       => JText::_('COM_PLOTALOT_CHART_TYPE_GAUGE'),
		CHART_TYPE_TIMELINE    => JText::_('COM_PLOTALOT_CHART_TYPE_TIMELINE'),
		CHART_TYPE_BUBBLE      => JText::_('COM_PLOTALOT_CHART_TYPE_BUBBLE'),
		"OPTGROUP_START_1"     => JText::_('COM_PLOTALOT_CHART_TYPE_BAR'), 
		CHART_TYPE_BAR_H_STACK => JText::_('COM_PLOTALOT_CHART_TYPE_BAR_H_STACK'),
		CHART_TYPE_BAR_H_GROUP => JText::_('COM_PLOTALOT_CHART_TYPE_BAR_H_GROUP'),
		CHART_TYPE_BAR_V_STACK => JText::_('COM_PLOTALOT_CHART_TYPE_BAR_V_STACK'),
		CHART_TYPE_BAR_V_GROUP => JText::_('COM_PLOTALOT_CHART_TYPE_BAR_V_GROUP'),
		"OPTGROUP_END_1"       => '',
		"OPTGROUP_START_2"     => JText::_('COM_PLOTALOT_CHART_TYPE_PIE'), 
		CHART_TYPE_PIE_2D      => JText::_('COM_PLOTALOT_CHART_TYPE_PIE_2D'),
		CHART_TYPE_PIE_3D      => JText::_('COM_PLOTALOT_CHART_TYPE_PIE_3D'),
		CHART_TYPE_PIE_2D_V    => JText::_('COM_PLOTALOT_CHART_TYPE_PIE_2D_V'),
		CHART_TYPE_PIE_3D_V    => JText::_('COM_PLOTALOT_CHART_TYPE_PIE_3D_V'),
		"OPTGROUP_END_2"       => '',
		"OPTGROUP_START_3"     => JText::_('COM_PLOTALOT_CHART_TYPE_COMBO'), 
		CHART_TYPE_COMBO_STACK => JText::_('COM_PLOTALOT_CHART_TYPE_COMBO_STACK'),
		CHART_TYPE_COMBO_GROUP => JText::_('COM_PLOTALOT_CHART_TYPE_COMBO_GROUP'),
		"OPTGROUP_END_3"       => '',
		);
		
	$this->chart_categories = array(
		CHART_TYPE_ANY         => JText::_('COM_PLOTALOT_CHART_TYPE_ANY'),
		CHART_TYPE_SINGLE_ITEM => JText::_('COM_PLOTALOT_CHART_TYPE_SINGLE_ITEM'),
		CHART_CATEGORY_TABLE   => JText::_('COM_PLOTALOT_CHART_TYPE_TABLE'),
		CHART_TYPE_LINE        => JText::_('COM_PLOTALOT_CHART_TYPE_LINE'),
		CHART_TYPE_AREA        => JText::_('COM_PLOTALOT_CHART_TYPE_AREA'),
		CHART_TYPE_SCATTER     => JText::_('COM_PLOTALOT_CHART_TYPE_SCATTER'),
		CHART_TYPE_GAUGE       => JText::_('COM_PLOTALOT_CHART_TYPE_GAUGE'),
		CHART_TYPE_TIMELINE    => JText::_('COM_PLOTALOT_CHART_TYPE_TIMELINE'),
		CHART_TYPE_BUBBLE      => JText::_('COM_PLOTALOT_CHART_TYPE_BUBBLE'),
		CHART_CATEGORY_BAR     => JText::_('COM_PLOTALOT_CHART_TYPE_BAR'),
		CHART_CATEGORY_PIE     => JText::_('COM_PLOTALOT_CHART_TYPE_PIE'),
		CHART_CATEGORY_COMBO   => JText::_('COM_PLOTALOT_CHART_TYPE_COMBO'),
		CHART_CATEGORY_SAMPLE  => JText::_('COM_PLOTALOT_SAMPLE'),
		);

	$this->table_types = array(
		CHART_TYPE_PL_TABLE    => JText::_('COM_PLOTALOT_CHART_TYPE_PL_TABLE'),
		CHART_TYPE_GV_TABLE    => JText::_('COM_PLOTALOT_CHART_TYPE_GV_TABLE'),
		);
		
	$this->xDataFormats = array(
		FORMAT_NONE      =>  JText::_('JNONE'), 
		FORMAT_NUM_UK_0  =>  "99,999",
		FORMAT_NUM_UK_1  =>  "99,999.9",
		FORMAT_NUM_UK_2  =>  "99,999.99",
		FORMAT_DATE_DMY  =>  JText::_('COM_PLOTALOT_FORMAT_DATE_DMY'), 
		FORMAT_DATE_MDY  =>  JText::_('COM_PLOTALOT_FORMAT_DATE_MDY'),
		FORMAT_DATE_DMONY=>  JText::_('COM_PLOTALOT_FORMAT_DATE_DMONY'),
		FORMAT_DATE_DM   =>  JText::_('COM_PLOTALOT_FORMAT_DATE_DM'), 
		FORMAT_DATE_DMON =>  JText::_('COM_PLOTALOT_FORMAT_DATE_DMON'), 
		FORMAT_DATE_MD   =>  JText::_('COM_PLOTALOT_FORMAT_DATE_MD'),
		FORMAT_DATE_MY   =>  JText::_('COM_PLOTALOT_FORMAT_DATE_MY'),
		FORMAT_DATE_MONY =>  JText::_('COM_PLOTALOT_FORMAT_DATE_MONY'),
		FORMAT_DATE_Y    =>  JText::_('COM_PLOTALOT_FORMAT_DATE_Y'),
		FORMAT_DATE_M    =>  JText::_('COM_PLOTALOT_FORMAT_DATE_M'),
		FORMAT_DATE_MON  =>  JText::_('COM_PLOTALOT_FORMAT_DATE_MON'),
		FORMAT_DATE_MONTH => JText::_('COM_PLOTALOT_FORMAT_DATE_MONTH'),
		FORMAT_DATE_D    =>  JText::_('COM_PLOTALOT_FORMAT_DATE_D'), 
		FORMAT_DATE_DAY  =>  JText::_('COM_PLOTALOT_FORMAT_DATE_DAY'),
		FORMAT_TIME_HHMM =>  JText::_('COM_PLOTALOT_FORMAT_TIME_HHMM'),
		FORMAT_TIME_HHMMSS => JText::_('COM_PLOTALOT_FORMAT_TIME_HHMMSS'),
		FORMAT_TIME_HH   =>  JText::_('COM_PLOTALOT_FORMAT_TIME_HH'),
		FORMAT_TIME_MM   =>  JText::_('COM_PLOTALOT_FORMAT_TIME_MM'),
		FORMAT_PERCENT_0 =>  JText::_('COM_PLOTALOT_FORMAT_PERCENT_0'),
		FORMAT_PERCENT_1 =>  JText::_('COM_PLOTALOT_FORMAT_PERCENT_1'),
		FORMAT_PERCENT_2 =>  JText::_('COM_PLOTALOT_FORMAT_PERCENT_2'));
		
	$this->yDataFormats = array(
		FORMAT_NONE      =>  JText::_('JNONE'), 
		FORMAT_NUM_UK_0  =>  "99,999",
		FORMAT_NUM_UK_1  =>  "99,999.9",
		FORMAT_NUM_UK_2  =>  "99,999.99",
		FORMAT_NUM_FR_0  =>  "99 999",
		FORMAT_NUM_FR_1  =>  "99 999,9",
		FORMAT_NUM_FR_2  =>  "99 999,99",
		FORMAT_PERCENT_0 =>  JText::_('COM_PLOTALOT_FORMAT_PERCENT_0'),
		FORMAT_PERCENT_1 =>  JText::_('COM_PLOTALOT_FORMAT_PERCENT_1'),
		FORMAT_PERCENT_2 =>  JText::_('COM_PLOTALOT_FORMAT_PERCENT_2'));

	$this->lineStylesPie = array(
		  PLOT_STYLE_NORMAL  => JText::_('COM_PLOTALOT_NORMAL'), 
		  PIE_LIGHT_GRADIENT => JText::_('COM_PLOTALOT_PIE_LIGHT_GRADIENT'), 
		  PIE_DARK_GRADIENT  => JText::_('COM_PLOTALOT_PIE_DARK_GRADIENT'), 
		  PIE_MULTI_COLOUR   => JText::_('COM_PLOTALOT_PIE_MULTI_COLOUR'));
		
	$this->lineStylesLine = array(
		  PLOT_STYLE_NORMAL => JText::_('COM_PLOTALOT_NORMAL'), 
		  LINE_THIN_SOLID   => JText::_('COM_PLOTALOT_LINE_THIN_SOLID'),
		  LINE_THICK_SOLID  => JText::_('COM_PLOTALOT_LINE_THICK_SOLID'));

	$this->legendTypes = array(
		LEGEND_NONE 	=> JText::_('JNONE'),
		LEGEND_LEFT 	=> JText::_('COM_PLOTALOT_LEGEND_LEFT'),
		LEGEND_RIGHT 	=> JText::_('COM_PLOTALOT_LEGEND_RIGHT'),
		LEGEND_TOP 		=> JText::_('COM_PLOTALOT_LEGEND_TOP'),
		LEGEND_BOTTOM 	=> JText::_('COM_PLOTALOT_LEGEND_BOTTOM'));

	$this->pieTextTypes = array(
		PIE_TEXT_NONE 	 => JText::_('COM_PLOTALOT_PIE_TEXT_NONE'),
		PIE_TEXT_PERCENT => JText::_('COM_PLOTALOT_PIE_TEXT_PERCENT'),
		PIE_TEXT_VALUE 	 => JText::_('COM_PLOTALOT_PIE_TEXT_VALUE'),
		PIE_TEXT_LABEL   => JText::_('COM_PLOTALOT_PIE_TEXT_LABEL'));

	$this->comboPlotTypes = array(
		COMBO_PLOT_TYPE_LINE_NORMAL  => JText::_('COM_PLOTALOT_LINE'),
		COMBO_PLOT_TYPE_LINE_THIN    => JText::_('COM_PLOTALOT_LINE').' ('.JText::_('COM_PLOTALOT_LINE_THIN_SOLID').')',
		COMBO_PLOT_TYPE_LINE_THICK   => JText::_('COM_PLOTALOT_LINE').' ('.JText::_('COM_PLOTALOT_LINE_THICK_SOLID').')',
		COMBO_PLOT_TYPE_AREA         => JText::_('COM_PLOTALOT_AREA'),
		COMBO_PLOT_TYPE_BARS         => JText::_('COM_PLOTALOT_BARS'),
//		COMBO_PLOT_TYPE_CANDLESTICKS => JText::_('COM_PLOTALOT_CANDLESTICKS'),	// not supported for now
		COMBO_PLOT_TYPE_STEPPEDAREA  => JText::_('COM_PLOTALOT_STEPPEDAREA'));
}

//-------------------------------------------------------------------------------
// Return a chart type name
//
function chartTypeName($chart_type)
{
	if (array_key_exists($chart_type, $this->chart_types))
		return $this->chart_types[$chart_type];
	else
		return JText::_('COM_PLOTALOT_ERROR_CHART_TYPE');
}

//-------------------------------------------------------------------------------
// Return a chart category, if there is one, or the chart type if not
//
static function chartCategory($chart_type)
{
	switch ($chart_type)
		{
		case CHART_TYPE_BAR_H_STACK:
		case CHART_TYPE_BAR_H_GROUP:
		case CHART_TYPE_BAR_V_STACK:
		case CHART_TYPE_BAR_V_GROUP:
			return CHART_CATEGORY_BAR;
		case CHART_TYPE_PIE_2D:
		case CHART_TYPE_PIE_3D:
		case CHART_TYPE_PIE_2D_V:
		case CHART_TYPE_PIE_3D_V:
			return CHART_CATEGORY_PIE;
		case CHART_TYPE_PL_TABLE:
		case CHART_TYPE_GV_TABLE:
			return CHART_CATEGORY_TABLE;
		case CHART_TYPE_COMBO_STACK:
		case CHART_TYPE_COMBO_GROUP:
			return CHART_CATEGORY_COMBO;
		default:
			return $chart_type;
		}
}

//-------------------------------------------------------------------------------
// Return true if supplied argument is a positive integer, else false
//
static function is_posint($arg, $allow_blank=true)
{
	if ($arg == '')
		{
		if ($allow_blank)
			return true;
		else
			return false;
		}
	if (!is_numeric($arg))
		return false;
	if ((intval($arg) == $arg) and ($arg >= 0))
		return true;
	else
		return false;
}


}

?>


