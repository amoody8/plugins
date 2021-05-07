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

class T4pagebuilderViewPage extends JViewLegacy
{
		

	public function display($tpl = null)
	{

		$input = JFactory::getApplication()->input;
		$action = $input->getCmd(JPB_PARAM);
		if($action == 'head'){
			\JPB\Helper\Editor::renderPreviewHead();
		}else{
			\JPB\Helper\Editor::handleAction();
		}
	}
}
?>