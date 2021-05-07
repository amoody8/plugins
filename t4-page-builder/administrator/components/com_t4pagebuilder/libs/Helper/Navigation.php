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

class Navigation {

	public static function menuChanged () {
		// menu items is updated/changed, re-sync with generated menu html
		// find all share blocks and try to update it's menu if found
		$blocks = \JPB\Helper\Block::loadAllShareBlocks();
		$menu = JFactory::getApplication()->getMenu('site');

		foreach ($blocks as $name => $block) {
			$doc = self::loadDoc($block);
			$xpath = new \DOMXpath($doc);

			// find nav
			$dmenus = $xpath->query("//ul[@menutype]");
			if (!$dmenus->length) continue;
			
			foreach ($dmenus as $dmenu) {
				$menutype = (string) $dmenu->getAttribute('menutype');
				$items = $menu->getItems('menutype', $menutype);
				// update
				$ids = [];
				foreach ($items as $item) {
					// find menu item
					$id = $item->id;
					$ids[] = $id;
					$ditem = $xpath->query(".//li[@itemid='$id']", $dmenu)->item(0);
					$link = $item->link . (preg_match('/\?/', $item->link) ? '&' : '?') . 'Itemid=' . $id;
					if (!$ditem) {
						// add new item, find parent
						$pid = $item->parent_id;
						$dparent = $xpath->query(".//li[@itemid='$pid']", $dmenu)->item(0);

						// add to root nav
						$dropdownitem = false;
						if (!$dparent) {
							// $dul = $xpath->query(".//ul", $dmenu)->item(0);
							// if (!$dul) continue;
							$dul = $dmenu;
						} else {
							$dropdownitem = true;
							// get dropdown
							$ddropdown = $xpath->query(".//div", $dparent)->item(0);

							if (!$ddropdown) {
								// create new one
								$ddropdown = self::addChild($doc, $dparent, 'div', '', ['class' => 'dropdown-menu']);
								self::addAttrs($dparent, ['class' => 'dropdown']);
								
								$dparentlink = $xpath->query('.//a', $dparent)->item(0);
								if ($dparentlink) self::addAttrs($dparentlink, ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown']);
							}

							// find ul
							$dul = $xpath->query(".//ul[contains(@class, 'nav')]", $ddropdown)->item(0);
							if (!$dul) {
								$dul = self::addChild($doc, $ddropdown, 'ul', '', ['class' => 'nav']);
							}
						}

						// append to ul
						$dli = self::addChild($doc, $dul, 'li', '', ['class' => 'nav-item' . ($dropdownitem ? ' dropdown-item' : ''), 'itemid' => $id]);
						$dlilink = self::addChild($doc, $dli, 'a', $item->title, ['class' => 'nav-link', 'title' => $item->title, 'href' => $link]);
					} else {
						// update item
						$ditemlink = $xpath->query('.//a', $ditem)->item(0);
						if ($ditemlink) {
							$ditemlink->textContent = $item->title;
							self::addAttrs($ditemlink, ['title' => $item->title, 'href' => $link]);
						}
					}
				}

				// find item not in dom, remote it
				$ditems = $xpath->query('.//li[@itemid]', $dmenu);
				// need delete
				$need_deletes = [];
				foreach ($ditems as $ditem) {
					$id = (int)$ditem->getAttribute('itemid');
					if (!in_array($id, $ids)) $need_deletes[] = $ditem;
				}

				foreach (array_reverse($need_deletes) as $ditem) $ditem->parentNode->removeChild($ditem);

			}

			// echo $doc->saveHTML();
			\JPB\Helper\Block::saveShareBlock($name, self::getHtml($doc));
		}

	}

	private static function &addChild (&$doc, &$parent, $tagName, $content = '', $attributes) {
		$element = $doc->createElement($tagName, $content);
		foreach ($attributes as $name => $value) {
			$element->setAttribute($name, $value);
		}
		$element = $parent->appendChild($element);
		return $element;
	}

	private static function addAttrs (&$el, $attrs) {
		foreach ($attrs as $name => $value) {
			if ($name == 'class') {
				$cval = $el->getAttribute($name);
				$value = implode(' ', array_unique(explode(' ', trim($cval . ' ' . $value))));
			}
			$el->setAttribute($name, $value);
		}
	}

	private static function loadDoc ($html) {
		$doc = new \DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML("<html><body>$html</body></html>");
		return $doc;
	}

	private static function getHtml ($doc) {
		$html = $doc->saveHTML();
		if (preg_match ("#<body>(.*)</body>#mus", $html, $match)) return $match[1];
		return $html;
	}
}