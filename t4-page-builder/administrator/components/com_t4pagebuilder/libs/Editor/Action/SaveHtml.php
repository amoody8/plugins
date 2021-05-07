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
jimport('joomla.filesystem.file');

class SaveHtml extends Base {
	var $REVISION_CHANGES = 10;
	var $REVISION_MAX = 50;
	var $siteid = 0;
	var $local_revision = false;
	var $rev_types = [];
	var $update_queue = [];

	public function run () {
		$itemId = JFactory::getApplication()->input->getInt('id');
		$html = JFactory::getApplication()->input->getRaw('html');

		$item = \JPB\Helper\Item::load($itemId);
		if (!$item) return ["error" => "Item [$itemId] not found!"];
		
		$data = \JPB\Helper\Table::decodeData($item->working_content);
		$data['html'] = $html;
		\JPB\Helper\Item::save($data, $itemId);

		return ["ok" => 1];
	}

}