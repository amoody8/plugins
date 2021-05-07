<?php
/**
 * @package     Joomlart.Plugin
 * @subpackage  Editors-xtd.t4pagebuilder
 *
 * @copyright   Copyright (C) 2005 - 2019 JoomlArt. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Plugin\CMSPlugin;

JLoader::registerNamespace('JPB', JPATH_ADMINISTRATOR . '/components/com_t4pagebuilder/libs', false, false, 'psr4');
// add field
if(!defined("JPB_PARAM")) define ('JPB_PARAM', 'act');
if(!defined("JPB_DEVMODE")) define ('JPB_DEVMODE', '');
if(!defined("JPB_PATH")) define ('JPB_PATH', __DIR__);
if(!defined("JPB_PATH_BASE")) define ('JPB_PATH_BASE', \JUri::base(true)."/components/com_t4pagebuilder/");
if(!defined("JPB_MEDIA")) define ('JPB_MEDIA', '/media/t4pagebuilder/');
if(!defined("JPB_MEDIA_BUILDER")) define ('JPB_MEDIA_BUILDER', JPB_MEDIA . 'builder/');
if(!defined("JPB_PATH_MEDIA_BUILDER")) define ('JPB_PATH_MEDIA_BUILDER', JPATH_ROOT . JPB_MEDIA . 'builder/');
class PlgContentLoadt4pagebuilder extends CMSPlugin
{
	function onContentPrepare($context, &$article, &$params, $limitstart)
	{


		
		// Don't run this plugin when the content is being indexed
		if ($context === 'com_finder.indexer')
		{
			return true;
		}

		$items = [];
		\JPB\Helper\Render::loadItems($article->text, $items);

		if(!empty($items)){
			foreach ($items as $item) {
				$pages = $item;
			}
		}
		// Simple performance check to determine whether bot should process further
		if (empty($pages) || ($context == 'com_content.article' && !$pages->state))
		{
			return true;
		}
		//add your plugin codes here
		$this->_prepareDocument($pages);
		$html = $pages->page_html;
		$html = \JPB\Helper\Html::loadShareBlock($html);
		$html = \JPB\Helper\Html::unvoidJdoc($html);
		$html = \JPB\Helper\Html::renderJdoc ($html);
		$article->text = "<div class='jpb-page'>".$html."</div>";
		//return a string value. Returned value from this event will be displayed in a placeholder. 
                // Most templates display this placeholder after the article separator. 
	}
		/**
	 * Prepares the document.
	 *
	 * @return  void
	 */
	protected function _prepareDocument($data)
	{
		$app     = JFactory::getApplication();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$params = JComponentHelper::getParams('com_t4pagebuilder');
		$bootstrap4 = $params->get('bootstrap4') ? $params->get('bootstrap4') : 0;
		$icons = $params->get('loadconfigicons') ? $params->get('loadconfigicons') : "";
		$bootstrap_assigned = $params->get('btAssigned') ? json_decode($params->get('btAssigned')) : [];
		$title   = null;
		JHtml::_('jquery.framework');
		$this->document = JFactory::getDocument();
		$this->item = $data;
		$this->t4 	= self::getT4();
		if(!$this->t4){
			if($bootstrap4 == 0){
				$this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/css/bootstrap.min.css' );
			}elseif($bootstrap4 < 0 ){
				if(in_array(-$menu->id, $bootstrap_assigned)){
					$this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/css/bootstrap.min.css' );
				}

			}elseif($bootstrap4 > 0 ){
				if(in_array($menu->id, $bootstrap_assigned)){
					$this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/css/bootstrap.min.css' );
				}
			}
			$this->document->addScript(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/js/bootstrap.bundle.js' );

		}
		if(is_file(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json')){
			self::loadCustomFont();
		}
		$this->loadGoogleFonts();
		$this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/ja_pagebuilder.css');
		$this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/elements.css');
		$this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/site.css' );
		$this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/animate/animate.css' );
		$this->document->addScript(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/animate/t4b-animation.js' );
		$this->_loadFontIcons($icons);
		if (isset($this->item->css) || isset($this->item->bundle_css)){
			$protocols  = '[a-zA-Z0-9\-]+:';
			// Replace src links.
			$base   = JUri::base(true) . '/';
			$regex_url  = '\s*url\s*\(([\'\"]|\&\#0?3[49];)?(?!\/|\&\#0?3[49];|' . $protocols . '|\#)([^\)\'\"]+)([\'\"]|\&\#0?3[49];)?\)';
			$regex  = '#' . $regex_url . '#m';
			$css = "";
			if($this->item->bundle_css){
				$css = $this->item->bundle_css;
			}
			$css .= (string) $this->item->css;

			if($css){
				$css = preg_replace($regex, 'url($1' . $base. '$2$3)', $css);
				preg_match_all($regex, $this->item->css, $matches);
				$this->document->addStyleDeclaration($css);
			}
			$this->document->addScriptDeclaration("
			    jQuery(document).ready(function($){
			       	window['t4b-animation'].init({
						easing: 'ease-out-back',
						duration: 1000
					});
			    });
			");
		}
		if (isset($this->print))
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}

	protected function loadCustomFont()
	{
		static $customFonts = null;
		static $loaded = [];
		if ($customFonts === null) {
			$customFonts = json_decode(file_get_contents(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json'), true);
			if (empty($customFonts)) $customFonts = [];
		}
		if (empty($customFonts['fonts'])) return;
		$name = array_keys($customFonts['fonts'])[0];
		
		if (!empty($loaded[$name])) return;

		// mark as loaded
		$loaded[$name] = 1;
		if (empty($customFonts['fonts'][$name])) return;

		$font = $customFonts['fonts'][$name];
		if (!empty($font['type']) && $font['type'] == 'css') {
			if(!preg_match("/[^\/.\s+]/",$font['url'])){
				$font['url'] = '/'.$font['url'];
			}
			// load css file
			$this->document->addStylesheet($font['url']);
		} else {
			// add css declaration
			$css = '@font-face {';
			$css .= "font-family: '$name';";
			$css .= "src: url('{$font['url']}');";
			$css .= "}";

			$this->document->addStyleDeclaration ($css);
		}
	}
	protected function loadGoogleFonts() {
		$doc = JFactory::getDocument();
		// Load google/custom font in field with suffix _font_family
		$googleFonts = $this->params->get('loadfonts') ? json_decode($this->params->get('loadfonts')): [];

		$family = [];
		foreach ($googleFonts as $font => $weights) {
			$weight = implode(',', $weights->weight);
			$family[] = $font . ($weight == '400' ? '' : ':' . $weight);
		}

		if (count($family)) {
			$doc->addStylesheet('https://fonts.googleapis.com/css?family=' . urlencode(implode('|', $family)));
		}

	}
	protected function _loadFontIcons($data){
		if(!$data) return;
		$doc = JFactory::getDocument();

		$fData = json_decode($data,true);
		$aws = $fData['awesome_icons'];
		$material = $fData['material_icons'];
		if($aws['awesome_icons'] == false && $material['material_icons'] == false ) return;
		if($aws['url_type'] == 'cdn'){
			$doc->addStylesheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/awesome_5.11.2.min.css');
		}elseif($aws['url_type'] == 'url'){
			if($aws['custom_url']){
				$doc->addStylesheet($aws['custom_url']);
			}
		}
		if($material['url_type'] == 'cdn'){
			$doc->addStyleSheet('https://fonts.googleapis.com/icon?family=Material+Icons');
		}elseif($material['url_type'] == 'url'){
			if($material['custom_url']){
				$doc->addStylesheet($material['custom_url']);
			}
		}
	}
	protected function _loadItem($item_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
				->select('*')
				->from('#__jae_item')
				->where('asset_name = '. $db->quote("jform.articletext"))
				->where('asset_id = '. $db->quote($item_id));
				$db->setQuery($query);
		return $db->loadObject();
	}
	protected function getT4()
	{
		$tempate = JFactory::getApplication()->getTemplate();
		$return = false;
		if($tempate === 't4_blank'){
			$return = true;
		}
		return $return;
	}
}

?>