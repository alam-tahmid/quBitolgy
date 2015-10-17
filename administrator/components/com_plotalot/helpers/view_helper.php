<?php
/********************************************************************
Product    : Plotalot
Date       : 16 April 2014
Copyright  : Les Arbres Design 2010-2014
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

if (class_exists("LAP_view"))
	return;

class LAP_view
{

//-------------------------------------------------------------------------------
// Make a checkbox
// $name          : Field name
// $current_value : Current value (boolean)
// $label         : optional label to be added left of checkbox
// $extra         : Javascript or styling to be added to <input> tag
//
static function make_checkbox($name,$current_value,$label='',$extra='')
{
	$html = '';
	if ($label != '')
//		$html = "\n".'<label for="'.$name.'">'.$label.'</label>';	// doesn't work in Joomla 1.6 admin template
		$html = $label.' ';
	if ($current_value)
		$checked = 'checked="checked" ';
	else
		$checked = '';
	$html .= '<input type="checkbox" name="'.$name.'" value="1" id="'.$name.'" '.$checked.' '.$extra.'/>'."\n";
	return $html;
}

//-------------------------------------------------------------------------------
// Make a select list
// $name          : Field name
// $current_value : Current value
// $list          : Array of ID => value items
// $first         : ID of first item to be placed in the list
// $extra         : Javascript or styling to be added to <select> tag
//
static function make_list($name, $current_value, &$items, $first = 0, $extra='')
{
	$html = "\n".'<select name="'.$name.'" id="'.$name.'" class="inputbox" size="1" '.$extra.'>';
	if ($items == null)
		return '';
	foreach ($items as $key => $value)
		{
		if (strncmp($key,"OPTGROUP_START",14) == 0)
			{
			$html .= "\n".'<optgroup label="'.$value.'">';
			continue;
			}
		if (strncmp($key,"OPTGROUP_END",12) == 0)
			{
			$html .= "\n".'</optgroup>';
			continue;
			}
		if ($key < $first)					// skip unwanted entries
			continue;
		$selected = '';
		if ($current_value == $key)
			$selected = ' selected="selected"';
		$html .= "\n".'<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
	$html .= '</select>'."\n";
	return $html;
}

//-------------------------------------------------------------------------------
// Make an info button
//
static function make_info($title, $link='', $extra='')
{
	if ($link == '')
		{
		$icon_name = 'info-16.png';
		$html = '';
		}
	else
		{
		$icon_name = 'link-16.png';
		$html = '<a href="'.$link.'" target="_blank">';
		}

	$icon = '<img src="'.LAP_ADMIN_ASSETS_URL.$icon_name.'" style="vertical-align:text-bottom;" alt="" />';
	$html .= '<span class="hasTip" title="'.htmlspecialchars($title, ENT_COMPAT, 'UTF-8').'" '.$extra.' >'.$icon.'</span>';
		
	if ($link != '')
		$html .= '</a>';
		
	return $html;
}

//---------------------------------------------------------------------------------------------
// Load our front end style sheet, if it exists
//
static function load_styles()
{
	if (!file_exists(JPATH_ROOT.'/components/com_plotalot/assets/plotalot.css'))
		return;

	$document = JFactory::getDocument();
	$document->addStyleSheet(JURI::root(true).'/components/com_plotalot/assets/plotalot.css');	
}



}

