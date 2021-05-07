<?php

/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:		  https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

class JFormFieldPagekey extends JFormField
{
	protected $type = 'Pagekey' ;
	protected $layout = 'layouts.Pagekey' ;
	
	protected function getInput()
	{

		$updated = $this->value ? true : false;
		if(defined('T4B_DEVMODE')){
			$updated = false;
		}
		$readonly = "";
		if($updated){
			$readonly = 'readonly="readonly"';
		}
		$hint = $this->element['hint'] ? 'placeholder="'.htmlspecialchars((string)$this->element['hint']).'"' : '';
		$hidden = $this->element['hideLabel'] ? 'hidden' : 'text';
		$html = '';
		$html	.= '<input id="'.$this->id.'" class="t4b-page-key '.$this->element['class'].'" type="text" name="jform['.$this->element['name'].']" data-attrname="'.$this->element['name'].'" value="'.$this->value.'" '. $hint .' '.$readonly.' />';
		// if(!$updated){
		// 	$html	.= '<button type="button" id="update-page-key" class="btn btn-fonts" >Update</button>';
		// }
		return $html;
	}
	
	
}