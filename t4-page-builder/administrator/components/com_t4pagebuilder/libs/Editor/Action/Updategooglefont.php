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
namespace JPB\Editor\Action;
defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use Joomla\CMS\Layout\LayoutHelper as JLayoutHelper;
class Updategooglefont extends Base {
	public function run() {
		$data = json_decode(file_get_contents('php://input'), true);
		// Load the current component params.
		$params = JComponentHelper::getParams('com_t4pagebuilder');
		// Set new value of param(s)
		$params->set('loadfonts', $data['data']['dataFont']);
		$params->set('loadconfigicons', $data['data']['dataFontIcons']);

		// Save the parameters
		$componentid = JComponentHelper::getComponent('com_t4pagebuilder')->id;
		$table = \JTable::getInstance('extension');
		$table->load($componentid);
		$table->bind(array('params' => $params->toString()));

		// check for error
		if (!$table->check()) {
		    echo $table->getError();
		    return false;
		}
		// Save to database
		if (!$table->store()) {
		    echo $table->getError();
		    return false;
		}else{
			return ['ok' => '1'];
		}

	}

}

