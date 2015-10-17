<?php
/**
* @version 1.0.0
* @package Tz Module Widget
* @copyright (C) 2012 www.themezart.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*
**/

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldSpacer extends JFormField{

    protected $type = 'Spacer';

    protected function getInput(){
        return '';
    }

    protected function getLabel(){
        $html   = array();
        $class  = (string) $this->element['class'];
        $label  = '';

        // Get the label text from the XML element, defaulting to the element name.
        $text = $this->element['label'] ? (string) $this->element['label'] : '';
        $text = $this->translateLabel ? JText::_($text) : $text;


        // Add the label text and closing tag.
        if($text != NULL){
            $label .= '<div class="spacer'.(($text != '') ? ' hasText hasTip' : '').'" title="'. JText::_($this->description) .'"><span>' . JText::_($text) . '</span></div>';
        }

        $html[] = $label;

        return implode('', $html);
    }
}

