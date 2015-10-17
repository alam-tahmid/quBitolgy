<?php
/********************************************************************
Product    : Plotalot
Date       : 15 April 2014
Copyright  : Les Arbres Design 2010-2014
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/

defined('_JEXEC') or die('Restricted Access');

require_once JPATH_COMPONENT.'/helpers/view_helper.php';

class PlotalotViewEdit_Css extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAP_COMPONENT_NAME.': '.JText::_('COM_PLOTALOT_EDIT_CSS'), 'plotalot.png');
	JToolBarHelper::apply('apply_css');
	JToolBarHelper::save('save_css');
	JToolBarHelper::cancel('cancel','JTOOLBAR_CLOSE');
	
	$css_path = JPATH_COMPONENT_SITE.'/assets/plotalot.css';
	
	if (!file_exists($css_path)) 
		{ 
		$app = &JFactory::getApplication();
		$app->redirect(LAP_COMPONENT_LINK.'&task=config',
			JText::_('COM_PLOTALOT_CSS_MISSING').' ('.$css_path.')', 'error');
		return;
		}
		
	if (!is_readable($css_path)) 
		{ 
		$app = &JFactory::getApplication();
		$app->redirect(LAP_COMPONENT_LINK.'&task=config',
			JText::_('COM_PLOTALOT_CSS_NOT_READABLE').' ('.$css_path.')', 'error'); 
		return;
		}

	if (!is_writable($css_path)) 
		{ 
		$app = &JFactory::getApplication();
		$app->redirect(LAP_COMPONENT_LINK.'&task=config',
			JText::_('COM_PLOTALOT_CSS_NOT_WRITEABLE').' ('.$css_path.')', 'error'); 
		return;
		}
		
	$css_contents = @file_get_contents($css_path);
	
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="<?php echo LAP_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	
	<?php 
	echo '<table><tr><td>';
	echo '<textarea name="css_contents" rows="25" cols="125">'.$css_contents .'</textarea>';
	echo '</td><td valign="top">';
	echo LAP_view::make_info('www.w3schools.com/css','http://www.w3schools.com/css/default.asp');
	echo '</td></tr></table>';
	?>
	</form>
	<?php 
}

}