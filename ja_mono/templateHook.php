<?php
/**
 * ------------------------------------------------------------------------
 * JA Mono Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * T3 Blank Helper class
 *
 * @package		T3 Blank
 */

jimport('joomla.event.event');

class JA_MonoHook extends JEvent
{
	protected $jsa;
	protected $build_routes;
	protected $uris;
	
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$input = JFactory::getApplication()->input;
		$this->jsa = $input->getBool('jsa');
		$input->unset('jsa');
		$this->build_routes = array();
		$this->uris = array();

		$app = JFactory::getApplication();
		$router = $app->getRouter();
		// Attach build rules for language SEF.
		$router->attachBuildRule(array($this, 'beforeBuildRule'), JRouter::PROCESS_BEFORE);
	}
	
	public function onT3Init() // no params
	{
	}

	public function onT3TplInit($t3app)
	{
		// ignore ajax if view=form
		$input = JFactory::getApplication()->input;
		define('AJAX_ENABLED', $t3app->getParam('addon_ajax_enabled', 1) && $input->getCmd('view') != 'form' && $input->getCmd('tmpl') != 'component');
		define ('AJAX_REQUEST', isset($_SERVER['HTTP_REFERER']) && !preg_match('/view=form/', $_SERVER['HTTP_REFERER']) && $this->jsa && AJAX_ENABLED);
	}

	public function onT3LoadLayout(&$path, $layout)
	{
		if (!AJAX_REQUEST) return;
		if (!is_file($path)) return;
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		// get the layout, detect ajax blocks and return only ajax block files
		$tpl = T3::getApp();
		$tmp_path = JPATH_ROOT . '/' . $tpl->getParam('t3-assets', 't3-assets') . '/tpls/';
		if (!is_dir($tmp_path)) JFolder::create($tmp_path);
		$layout_content = file_get_contents($path);
		$tmp_path = $tmp_path . $layout . '.php';
		// check modified date
		if (is_file($tmp_path) && filemtime($tmp_path) > filemtime($path)) {
			$path = $tmp_path;
			return;
		}
		// update file
		$regex = '/<\?(php)?\s*\$this->loadBlock.*\'AJAX-BLOCK\'.*\?>/iU';
		if (preg_match_all($regex, $layout_content, $matches)) {
			$tmp_content = implode("\n", $matches[0]);
			JFile::write($tmp_path, $tmp_content);
			$path = $tmp_path;
		}
	}

	public function onT3Spotlight(&$info, $name, $position)
	{
		
	}
	
	public function onT3Megamenu(&$menutype, &$config, &$levels)
	{
		
	}

	public function onT3BodyClass(&$class)
	{
		//$class[] = 'onbodyclass';
	}

	public function onT3BeforeCompileHead() // no params
	{
		
	}
	
	public function onT3BeforeRender() // no params
	{
		
	}
	
	public function onT3AfterRender() // no params
	{
		foreach ($this->uris as $uri) {
			$this->build_routes[$uri['uri']->getPath()] = $uri['Itemid'];
		}
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return;
		$buffer = $app->getBody();
		$regex  = '#(\<a\s[^>]*)href="([^"]*)#m';
		$buffer = preg_replace_callback($regex, array($this, 'updateLinks'), $buffer);
		$app->setBody ($buffer);
	}

	public function updateLinks(&$matches) {
		static $parsed_urls = array();
		// Get the full request URI.
		$url = $matches[2];
		// ignore external links
		if (preg_match ('/^(https?:)?\/\//', $url)) return $matches[0];
		// ignore #
		if ($url == '#' || preg_match("/^javascript/", $url)) return $matches[0];
		// remove jsa=1 if exist
		if (preg_match('/(\?|\&|;)jsa=1(\&|$)/', $url, $match)) {
			$url = str_replace($match[2] == '&' ? 'jsa=1' . ($match[1] == ';' ? '&amp;' : '&') : $match[1] . 'jsa=1', '', $url);
		}
		if (isset($parsed_urls[$url])) return $parsed_urls[$url];
		
		$app = JFactory::getApplication();

		// find Itemid
		$Itemid = isset($this->build_routes[$url]) ? $this->build_routes[$url] : 0;
		if (!$Itemid) {
			// detect from url
			if (preg_match('/Itemid=(\d+)([^\d]|$)/', $url, $match)) {
				$Itemid = $match[1];
			} else {
				$default_menu = $app->getMenu()->getDefault();
				$Itemid = $default_menu->id;
			}
		}
		// ignore ajax if task edit/save/delete or not found Itemid
		if (preg_match('/task=/', $url) || !$Itemid) {
			$return = $matches[1].'href="'.$url;
		} else {
			$data = 'data-itemid="' . $Itemid . '" ';
			// check if this template style
			$tpl = $app->getTemplate(true);
			$menuItem = $app->getMenu()->getItem($Itemid);
			if ($menuItem->template_style_id == $tpl->id || ($menuItem->template_style_id == 0 && $tpl->home)) {
				if (AJAX_ENABLED) $data .= 'data-ajax="1" ';
			}
			$return = $matches[1] . $data . 'href="' . $url;
		}

		$parsed_urls[$url] = $return;
		return $return;
	}

	public function beforeBuildRule(&$router, &$uri) {
		//$uri->setVar('xItemid', $uri->getVar('Itemid'));
		$this->uris[] = array('uri' => $uri, 'Itemid' => $uri->getVar('Itemid'));
	}
}

?>