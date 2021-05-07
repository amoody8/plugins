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

class Layout {

	public static function j3($file) {
		$j = 'j' . explode('.', JVERSION)[0];
		$jfile = preg_replace('/(\.[^\.]+)$/', '.' . $j . '\1', $file);
		return ($j == 'j3' && is_file($jfile) && $jfile != $file) ? $jfile : false;
	}

	public static function addIncludePath (&$path) {
		//JPATH_THEMES
		$basetheme = \JPB\Factory::getBaseTheme();
		if (!$basetheme) return false;

		$template = JFactory::getApplication()->getTemplate();
		$template_path = JPATH_THEMES . '/' . $template;

		for($i = count($path)-1; $i >= 0; $i--) {			
			$p = $path[$i];
			if (strpos($p, $template_path) === 0) {
				// add base path theme
				$base_path = T4PATH_THEMES . '/' . $basetheme . substr($p, strlen($template_path));
				array_splice($path, $i+1, 0, $base_path);
			}
		}

		return true;
	}

	public static function renderSection($data) {
		// render row content
		$content = self::renderRow($data);

		// render section html
		$id = !empty($data['name']) ? ' id="' . preg_replace('/\s/', '-', strtolower($data['name'])) . '"' : '';
		$html = "<section{$id}>\n{$content}\n</section>";

		return $html;
	}

	public static function renderRow($data) {
		if (empty($data['contents'])) return '';

		// render cols
		$contents = [];
		$cols = !empty($data['cols']) && (int)$data['cols'] && (int)$data['cols'] <= count($data['contents']) ? (int)$data['cols'] : count($data['contents']);

		if (!$cols) return '';

		for($i=0; $i < $cols; $i++) {
			$content = self::renderContent($data['contents'][$i]);
			if ($cols > 1) {
				// render cols
				$content = "<div class=\"col\">\n$content\n</div>";
			}
			$contents[] = $content;
		}

		$html = implode("\n", $contents);

		if ($cols > 1) {
			$html = "<div class=\"row\">\n$html\n</div>";
		}
		return $html;
	}

	public static function renderContent ($data) {

		switch ($data['type']) {
			case 'row':
				return self::renderRow($data);
			case 'component':
				return self::renderComponent($data);
			case 'module': 
				$data['jdoc'] = 'module';
				break;
			case 'positions': 
				$data['jdoc'] = 'modules';
				break;
			case 'block': 
				return self::renderBlock($data);
			case 'spacer':
				return self::renderSpacer($data);
		}
		return self::renderJdoc($data);
	}

	public static function renderJdoc($data) {

		if (empty($data['jdoc'])) return '';
		$html = "<jdoc:include type=\"{$data['jdoc']}\" name=\"{$data['name']}\" ";
		if (!empty($data['style'])) $html .= 'style="' . $data['style'] . '" ';
		if (!empty($data['title'])) $html .= 'title="' . $data['title'] . '" ';
		if (!empty($data['block'])) $html .= 'block="' . $data['block'] . '" ';
		$html .= '/>';
		return $html;
	}

	public static function renderComponent($data) {
		return '<jdoc:include type="message" /><jdoc:include type="component" />';
	}

	public static function renderSpacer($data) {		
		return '<meta name="spacer"/>';
	}

	public static function renderBlock($data) {

		$template = JFactory::getApplication()->getTemplate();
		$paths = [];
		$paths[] = JPATH_THEMES . '/' . $template;
		$paths[] = T4PATH_THEMES . '/' . \JPB\Factory::getBaseTheme();
		$file = '/block/' . $data['name'] . '/default.html';
		$layoutfile = null;
		foreach ($paths as $path) {
			if (is_file($layoutfile = $path . $file)) {
				return file_get_contents($layoutfile);
			}
		}

		return '';
	}
}