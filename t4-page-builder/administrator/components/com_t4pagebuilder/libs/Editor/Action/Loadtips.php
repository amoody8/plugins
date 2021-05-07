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

class Loadtips extends Base {
	public function run() {
		$tips = json_decode(file_get_contents(JPATH_ROOT . JPB_MEDIA_BUILDER . 'tips.json'));
		return $tips;
	}

}

