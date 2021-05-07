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
namespace JPB\Helper;
defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;

class Block {
	const LOCATION = JPATH_ROOT . JPB_MEDIA . 'public-html/';
	const TYPE_USER = 'Saved-Blocks';
	const TYPE_SHARE = 'Shared-Blocks';

	public static function save ($name, $content, $type) {
		$path = self::LOCATION . $type . '/';
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}
		$block_file = $path . $name . '.html';
		// void jdoc
		$content = Html::voidJdoc($content);
		return file_put_contents($block_file, $content);
	}

	public static function saveShareBlock ($name, $content) {
		return self::save ($name, $content, self::TYPE_SHARE);
	}

	public static function saveUserBlock ($name, $content) {
		return self::save ($name, $content, self::TYPE_USER);
	}

	public static function load ($name, $type) {
		$block_file = self::LOCATION . $type . '/' . $name . '.html';
		$block_content = is_file($block_file) ? file_get_contents($block_file) : '';

		return $block_content;	
	}

	public static function loadShareBlock ($name) {
		return self::load ($name, self::TYPE_SHARE);
	}

	public static function loadUserBlock ($name) {
		return self::load ($name, self::TYPE_USER);
	}

	public static function loadAll ($type) {
		$share_blocks = [];

		$dir_share_html = array_filter(glob(self::LOCATION . $type . '/*.html'));
		foreach ($dir_share_html as $share_html) {
			$name = basename($share_html,".html");
			$block_content = file_get_contents($share_html);

			if (!$block_content ||  !$name) continue;

			$share_blocks[$name] = $block_content;
		}

		return $share_blocks;
	}


	public static function loadAllShareBlocks () {
		return self::loadAll(self::TYPE_SHARE);
	}

	public static function loadAllUserBlocks () {
		return self::loadAll(self::TYPE_USER);
	}
	public static function removeBlock($name,$type)
	{
		$block_file = self::LOCATION . $type . '/' . $name . '.html';
		$block_content = is_file($block_file) ? unlink($block_file) : '';
		if(is_file($block_file)){
			self::removeBlock($name,$type);
		}
		return true;
	}

}