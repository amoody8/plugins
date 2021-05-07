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

class Editor {
	var $app;
	var $params;


	public static function getAction ($type) {
		$type = trim(ucfirst($type));
		if (!$type) return null;
		$className = '\\JPB\\Editor\\Action\\' . $type;
		if (class_exists($className)) {
			return new $className();
		}
		return null;
	}

	public static function authorite() {
		$input = JFactory::getApplication()->input;
		$key = $input->get('t4_key');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('count(*)')
			->from('#__session')
			->where('md5(concat(session_id, userid)) = ' . $db->quote($key));
		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function handleAction () {
		$app = JFactory::getApplication();
		$action = $app->input->getCmd(JPB_PARAM);
		if ($action) {
			$actionHelper = self::getAction ($action);
			if ($actionHelper) {
				self::doExit($actionHelper->run());
				/*
				if (!self::authorite()) {
					self::doExit(['error' => "No permission!"]);
				}

				self::doExit($actionHelper->run());
				*/
			} else {
				//self::doExit(['error' => "Action not found [$action]!"]);
				self::doExit(['error' => "Action not found [$action]!"]);
			}
		}
		return null;
	}

	public static function doExit ($result) {
		if (!is_array($result)) $result = ['data' => $result];
		// return as json
		header('Content-type: application/json');
		echo json_encode($result);
		exit();
	}

	public static function renderPreviewHead() {
		$app = JFactory::getApplication();
		if ($app->input->getCmd(JPB_PARAM . 'head')) {
			$doc = $app->getDocument();
			$stylesheet = array();
			if($app->input->getCmd('templateStyle')){
				$pageid = $app->input->getCmd('pageid','');
				if(!empty($pageid)){
					$url = \JUri::root() . "index.php?option=com_t4pagebuilder&view=page&id=".$pageid . "&";
				}else{
					$url = \JUri::root() ."index.php?";
				}
				$checkHead = get_headers($url . "templateStyle=".$app->input->getCmd('templateStyle'));
				$string = $checkHead[0];
				if(strpos($string,"200")){
					// Create a DOM object from a URL
					$html = file_get_contents( $url . "templateStyle=".$app->input->getCmd('templateStyle'));
					$re = '/href="([^"]*)([^"]*)"\s+rel="stylesheet/m';
					preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);
					if(is_array($matches) && count($matches) > 0){
						foreach ($matches as $match) {
							$stylesheet[$match[1]] = [
									"type" 		=> "text/css",
									"options" 	=> []
								];
						}
					}

				}
			}
			$stylesheet = self::loadstyles($stylesheet);
			$loadstyle = (isset($stylesheet)) ? array_merge($stylesheet,$doc->_styleSheets): $doc->_styleSheets;
			header('Content-type: application/json');
			echo json_encode($loadstyle); 
			exit;
		}
	}
	public static function loadstyles($stylesheet){
		$arr = array(
			\JUri::root()."/media/t4pagebuilder/builder/vendors/bootstrap4/css/bootstrap.min.css",
			\JUri::root()."/media/t4pagebuilder/builder/vendors/animate/animate.css",
			\JUri::root()."/media/t4pagebuilder/builder/css/ja_pagebuilder.css",
			\JUri::root()."/media/t4pagebuilder/builder/css/elements.css",

		);
		$font_config = self::_loadFontIcons();
		if(is_array($font_config) && !empty($font_config)){
			$arr = array_merge($font_config, $arr);
		}
		foreach ($arr as $data) {
			if(!isset($stylesheet[$data])){
				$stylesheet[$data] = [
					"type" 		=> "text/css",
					"options" 	=> []
				];
			}
		}
		return $stylesheet;
	}
	public static function _loadFontIcons(){
		$arr = array();

		$t4b_params = \JComponentHelper::getParams( 'com_t4pagebuilder' );
		$loadconfigicons = $t4b_params->get( 'loadconfigicons');
		$model = \JModelLegacy::getInstance('Page', 'T4pagebuilderModel', array('ignore_request' => true));
		if(!$loadconfigicons) return $arr;
		$fData = json_decode($loadconfigicons,true);
		$aws = $fData['awesome_icons'];
		$material = $fData['material_icons'];
		if($aws['awesome_icons'] == false && $material['material_icons'] == false ) return $arr;
		if( $aws['awesome_icons'] && $aws['url_type'] == 'cdn'){
			if(!$model->getIsT4()){
				$arr[] = \JUri::root(true).'/media/t4pagebuilder/builder/css/awesome_5.11.2.min.css';
			}
		}elseif($aws['awesome_icons'] && $aws['url_type'] == 'url'){
			if($aws['custom_url']){
				$arr[] = $aws['custom_url'];
			}
		}
		if($material['material_icons'] && $material['url_type'] == 'cdn'){
			$arr[] ='https://fonts.googleapis.com/icon?family=Material+Icons';
		}elseif($material['material_icons'] && $material['url_type'] == 'url'){
			if($material['custom_url']){
				$arr[] = $material['custom_url'];
			}
		}
		return $arr;
	}
}