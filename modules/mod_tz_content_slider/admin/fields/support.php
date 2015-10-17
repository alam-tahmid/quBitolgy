<?php
/**
* @version 1.0.0
* @package Tz Module Widget
* @copyright (C) 2012 www.themezart.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*
**/

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldSupport extends JFormField{

    protected $type = 'Support';


    protected function getInput(){

        $title      = $this->element['title'] ? $this->element['title'] : FALSE;
        $msg        = $this->element['message'] ? $this->element['message'] : FALSE;
        $info        = $this->element['info'] ? $this->element['info'] : FALSE;
        $html       = '';


        if($title OR $msg)
        {
            if($info){
				$html .= '<div class="alert alert-info">';
			}else{
				$html .= '<div class="alert">';
            }    
				$html .= ($title) ? '<h4>' . JText::_($title) . '</h4>' : '';
                $html .= ($msg) ? '<p>' . JText::_($msg) . '</p>' : '';
            $html .= '</div>';
        }

        return $html;
    }

    protected function getLabel(){
        return ;
    }
}
