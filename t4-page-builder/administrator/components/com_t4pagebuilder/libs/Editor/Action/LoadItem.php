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

class LoadItem extends Base {
	public function run() {
		$input = JFactory::getApplication()->input;
		$this->itemId = $input->getInt('id');
		$row = \JPB\Helper\Item::load($this->itemId);
		$data = $row ? $row->working_content : null;
		$data = \JPB\Helper\Table::decodeData($data);

		// always parse from html
		unset($data['components']);
		unset($data['styles']);
		if (empty($data['css'])) $data['css'] = isset($row->css) ? $row->css : '*{}';

		// replace tag jdoc:include to jdocinclude
		// $data['html'] = preg_replace('/<jdoc:include([^\/]*)\/>/', '<jdoc:include$1></jdoc:include>', isset($data['html']) ? $data['html'] : '');
		// void jdoc

		$data['html'] = !empty($data['html']) ? \JPB\Helper\Html::unvoidJdoc($data['html']) : (($row->asset_name == "pagetext") ? \JPB\Helper\Html::unvoidJdoc($row->page_html) : "");
		//$data['components'] = json_decode($data['components'], true);
		if (!$data) $data = [];
		return $data;
	}

}

