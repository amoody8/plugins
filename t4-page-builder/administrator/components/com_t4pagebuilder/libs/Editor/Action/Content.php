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

class Content extends Base {
	public function run() {
		$data = $this->helper->getData();
		if (!$data || !is_array($data) || !isset($data['type'])) {
			return '';
		}

		$doc = JFactory::getDocument();
		$oldStyleSheets = $doc->_styleSheets;
		$content = null;
		switch ($data['type']) {

			case 'module': 
				if (!isset($data['modid']) || !isset($data['modname']) || !isset($data['modtitle']) || !$data['modid']) {
					return array('content' => "Empty");
				}
				$data['title'] = $data['modtitle'];
				$data['id'] = $data['modid'];
				
				$content = $doc->getBuffer($data['type'], $data['modname'], $data);
				break;
			case 'position':
				if (!isset($data['position']) || !$data['position']) {
					return array('content' => "Empty");
				}
				$position = $data['position'];
				$content = $doc->getBuffer('modules', $position, $data);
				break;
		}

		// detect new css files
		$newStyleSheets = array_slice($doc->_styleSheets, count($oldStyleSheets));
		// preprocess to ignore some loaded
		$urls = array();
		foreach($newStyleSheets as $url => $css) {
			if (preg_match('/(font-awesome|bootstrap)/', $url)) continue;
			$urls[] = $url;
		}

		$result = array();
		$result['content'] = $content;
		$result['styleSheets'] = $urls;

		return $result;
	}

}
