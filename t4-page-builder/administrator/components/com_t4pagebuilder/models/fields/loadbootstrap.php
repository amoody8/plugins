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

class JFormFieldLoadbootstrap extends JFormField
{
	protected $type = 'loadbootstrap' ;
	protected $layout = 'layouts.btlayout' ;
	
	protected function getInput()
	{
		JFactory::getDocument()->addStyleSheet(JUri::base() .  "components/com_t4pagebuilder/assets/css/ja-pagebuilder.css");
		
		return \JLayoutHelper::render ($this->layout, '',JPATH_ADMINISTRATOR ."/components/com_t4pagebuilder/libs");
	}
	
	protected function getLabel()
	{
		return '<label id="jform_loadbootstrap-lbl" class="control-label" for="jform_loadbootstrap">'.JText::_('COM_T4PAGEBUILDER_BOOTSTRAP_ASSIGN').'</label>';
	}
	
}