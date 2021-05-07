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
namespace JPB;
defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;

class Factory {

	public static function getEditorHelper ($params = null) {
		static $instance = null;
		if (!$instance) {
			$instance = new Editor\Helper ($params);
		}
		return $instance;
	}

	public static function getHtml__ () {
		static $instance = null;
		if (!$instance) {
			$input = JFactory::getApplication()->input;
			$class = '\JPB\Html\\' . ($input->get('t4doc') ? ucfirst($input->get('t4doc')) : 'Site');

			//$preview = $input->get('t4preview');
			//$previewCheck = $input->get('t4preview-check');
			$instance = new $class();
			/*			
			if ($preview) {
				if ($previewCheck !== null) {
					$instance = new Html\PreviewUpdate ();
				} else {
					$instance = new Html\Preview ();
				}
			} else {
				$instance = new Html\Site ();
			}
			*/

			// define ('T4_TEMPLATE_PATH', $tmpl_path);
			// define ('T4_TEMPLATE_URI', 'templates/' . basename(T4_TEMPLATE_PATH));
		}
		return $instance;
	}

	// Create alias class for original call in $filepath, then overload the class
	public static function makeAlias($filepath, $originClassName, $aliasClassName) {
		if (!is_file($filepath)) return false;
		$code = file_get_contents($filepath);
		$code = str_replace('class ' . $originClassName, 'class ' . $aliasClassName, $code);
		eval('?>'. $code);
		return true;
	}	
	public static function getJVersion(){
		$jv = false;
		if(version_compare(JVERSION, '4', 'ge')){
			$jv = true;
		}
		return $jv;
	}
}
