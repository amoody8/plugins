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

class JFormFieldLoadfont extends JFormField
{
	protected $type = 'Loadfont' ;
	protected $layout = 'layouts.fonts' ;
	
	protected function getInput()
	{
		$data['name'] = $this->name;
		$data['value'] = $this->value;
		$data['id'] = $this->id;
		return \JLayoutHelper::render ($this->layout, $data,JPATH_ADMINISTRATOR ."/components/com_t4pagebuilder/libs");
	}
	
	
}