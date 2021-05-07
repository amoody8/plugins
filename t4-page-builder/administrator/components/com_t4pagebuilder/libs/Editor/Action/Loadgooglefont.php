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
class Loadgooglefont extends Base {
	public function run() {
		$params = JComponentHelper::getParams('com_t4pagebuilder');
		$data['value'] = $params->get('loadfonts');
		$data['name'] = 'loadgooglefont';
		$data['id'] = 'load_google_font';
		$data['pageid'] = JFactory::getApplication()->input->getInt('pageid','');
		return \JLayoutHelper::render ('layouts.editorSetting', $data,JPATH_ADMINISTRATOR ."/components/com_t4pagebuilder/libs");
	}

}

