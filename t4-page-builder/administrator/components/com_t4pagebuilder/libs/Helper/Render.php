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

class Render {
	static function loadItems ($content, &$items) {
		// find T4 item from shotcode
		if (preg_match_all('/<p data-name="t4b" data-content="t4b:(\d+):start[^>]+>/', $content, $matches)) {
			foreach($matches[1] as $iid) {
				if (!isset($items[$iid])) {
					$item = \JPB\Helper\Item::load($iid);
					$items[$iid] = $item;
				}
			}
		}
	}

	public static function renderHead() {
		$base = \JUri::base(true);
		$t4base = $base . JPB_MEDIA_BUILDER;
		$doc = JFactory::getDocument();
		$buffer = $doc->getBuffer();

		// find items used
		$items = [];
		foreach ($buffer as $type => $groups) {
			foreach ($groups as $group) {
				foreach ($group as $content) {
					self::loadItems ($content, $items);
				}
			}
		}
		if (count($items)) {
			
			// add core
			// add core css to first position if not T4 template
			$doc->_styleSheets = array($t4base . 'css/ja_pagebuilder.css' => array()) + $doc->_styleSheets;
			// add jquery & bootstrap js to top
			$doc->_scripts = array($t4base . 'vendors/jquery/jquery.min.js' => array(), $t4base . 'vendors/bootstrap4/js/bootstrap.min.js' => array()) + $doc->_scripts;

			//check jquery file existing!!!
			$jquery = JPATH_ROOT . '/media/vendor/jquery/js/jquery.js';
			if(!is_file($jquery)){
				$doc->addScript($t4base . 'vendors/jquery/jquery.min.js');
			}
			// $doc->addStylesheet($t4base . 'css/site.css');
			$doc->addScript($t4base . 'vendors/popper/dist/umd/popper.min.js');
			$doc->addScript($t4base . 'vendors/bootstrap4/js/bootstrap.min.js');

			foreach ($items as $item) {
				$content = (array)$item;
				if (!$content || !is_array($content)) continue;
				if (isset($content['bundle_css'])) {
					$doc->addStyleDeclaration($content['bundle_css']);
				}
				if (isset($content['css'])) {
					$doc->addStyleDeclaration($content['css']);
				}
				// assets
				if (isset($content['assets'])) {
					$assets = $content['assets'];
					if (isset($assets['css'])) {
						foreach ($assets['css'] as $link) {
							$url = preg_match('/^http/', $link) ? $link : $t4base . $link;
							$doc->addStylesheet($url);
						}
					}

					if (isset($assets['js'])) {
						foreach ($assets['js'] as $link) {
							$url = preg_match('/^http/', $link) ? $link : $t4base . $link;
							$doc->addScript ($url);
						}
					}
				}
			}
		}
	}

	public static function cleanRespond () {
		// Remove T4 Signal
		$app = JFactory::getApplication();
		$buffer = $app->getBody();


		// clean other
		$buffer = preg_replace('/<\/?T4:[^>]+>/i', '', $buffer);
		// add T4 class
		$buffer = preg_replace('/<t4[^>]*>/i', '<div class="t4">', $buffer);
		$buffer = preg_replace('/<\/t4>/i', '</div>', $buffer);
		// clean
		$buffer = preg_replace('/<\/?t4[^>]*>/i', '', $buffer);


		// remove readmore mark
		$buffer = preg_replace('/<meta name="jpb" content="\d*:end-intro">\s*<meta name="jpb" content="\d*:start-full">/', '', $buffer);

		// add T4 class
		$buffer = preg_replace('/<meta name="jpb" content="\d+:start[^>]*>/i', '<div class="t4">', $buffer, 1);
		// $buffer = preg_replace('/<meta name="jpb" content="\d+:end[^>]*>/i', '</div>', $buffer);
		// replace last match
		$arr = preg_split('/<meta name="jpb" content="\d+:end[^>]*>/i', $buffer);
		if (count($arr) > 1) {
			$buffer = implode("\n", array_slice($arr, 0, -1)) . "\n</div>\n" . end($arr);
		}

		// clean
		$buffer = preg_replace('/<meta name="jpb" [^>]*>/i', '', $buffer);

		// remove empty container tag
	    $pattern = '/<(div(?=[^>]*class=\"[^>]*col)|section|nav|header|footer)[^>]*>([\s\n\r]*)<\/\1>/imsU';
	    while (preg_match($pattern, $buffer)) {
	    	$buffer = preg_replace($pattern, '', $buffer);
	    }

	    // sef update
	    $buffer = \JPB\Helper\Html::sefUpdate($buffer);

		$app->setBody($buffer);
	}

}
