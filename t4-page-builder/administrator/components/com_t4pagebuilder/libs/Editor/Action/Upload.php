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

class Upload extends Base {

	public function run () {
		$data = $this->helper->getData();

		jimport('joomla.filesystem.file');
		$imgTypes = array('png', 'jpg', 'jpeg', 'gif', 'ico', 'bmp', 'svg', 'pict');
		
		// validate content		
		if (!preg_match('/data:image\/([^;]*);base64,(.*)$/', $data['content'], $match)) {
			// not valid image data
			return ['error' => 'not-valid-image-data'];
		}

		$type = strtolower($match[1]);
		$content = base64_decode($match[2]);
		if (!in_array($type, $imgTypes)) {
			// not support image type
			return ['error' => 'not-support-image-type [' . $type . ']'];
		}
		// make file name safe
		$ext = \JFile::getExt($data['name']);
		if (!in_array(strtolower($ext), $imgTypes)) $ext = $type;
		$name = preg_replace('/[\.\s]/', '-', \JFile::stripExt($data['name'])) . '.' . $ext;

		// image max size
		$max_size = 10;
		if (strlen($content) > 1024*1024*$max_size) {
			// image oversize
			return ['error' => 'oversize: ' . strlen($content)];
		}
		// save image
		$path = 'media/t4/images/' . $name;
		$fullpath = JPATH_ROOT . '/' . $path;

		\JFile::write(\JPath::check($fullpath), $content);
		/*
		if (!is_dir(dirname($fullpath))) {
			@mkdir(dirname($fullpath), 0755, true);
		}
		file_put_contents($fullpath, $content);
		*/
		$output = [];
		$output['path'] = $path;
		$output['data'] = $data;
		$output['ok'] = 1;

		return $output;
	}

}