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

class T4pagebuilderViewRevisions extends JViewLegacy
{
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
    	$id = $input->getInt('id');
		$this->items = $this->get('revisions');
		$this->pageId = $id;
		return parent::display($tpl);

	}
}

?>