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

class Item {
	const TABLE = 'jae_item';
	const TABLEREV = 'jae_revision';

	public static function save($data, $id = 0) {

		$item = self::load($id);
		$cdata = $item ? Table::decodeData($item->working_content) : [];
		// revision
		$rev = $item ? (int) $item->rev : 1;

		// void jdoc
		if (isset($data['html'])) $data['html'] = Html::voidJdoc($data['html']);
		// check need increase rev
		if (isset($data['html']) && isset($cdata['html']) && $data['html'] != $cdata['html']) $rev++;

		if ($item) {
			// update
			Table::updateRow (self::TABLE, ['working_content' => Table::encodeData($data), 'rev' => $rev], $id);
			return null;
		} else {
			$row = ['working_content' => Table::encodeData($data), 'rev' => $rev];
			if ($id) {
				$row['id'] = $id;
			}
			$itemId = Table::insertRow (self::TABLE, $row);
			return $itemId;
		}
	}
	public static function updateStyle($css,$id){
		
		$row = [
			'bundle_css' => $css,
		];
		return Table::updateRow (self::TABLE, $row, $id);
	}
	public static function load($id) {
		return Table::getRow(self::TABLE, $id);
	}

	// Reference
	public static function updatePage ($data, $itemId) {

		// create revision
		$item = self::load($itemId);
		if (!$item) return false;
		$css = $js = '';
		//check add js
		if(isset($data['blocksjs'])) $js = $data['blocksjs'];
		// if(isset($data['js'])) $js .= $data['js'];
		// check add css
		if(isset($data['blockscss'])) $css = $data['blockscss'];
		if(isset($data['css'])) $css .= $data['css'];
		$images = self::getImages($data['html'],$css);
		// create revision
		self::createRevision ($itemId);
		// update ref && content
		$row = [
			'page_html' => $data['html'],
			'css' => $css,
			'js' => $js,
			'images' => $images,
			'content' => $item->working_content,
		];
		return Table::updateRow (self::TABLE, $row, $itemId);
	}
	public static function createPageKey () {
		$sk = null;
		while (!$sk) {
			$sk = substr(md5(uniqid(rand(10000,99999), true)), 0, 5);
			if (self::pageKeyExisted($sk)) $sk = null;
		}
		return $sk;
	}
	
	public static function pageKeyExisted ($slug) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
				->select ('id')
				->from($db->quoteName('#__jae_item'))
				->where($db->quoteName('page_key').'='.$db->quote($slug));
		$db->setQuery($query);
		return $db->loadResult();
	}
	public static function createPage ($data){
		$newid = Table::insertRow(self::TABLE, $data);
		$pagetext = isset($data['asset_name']) ? $data['asset_name'] : 'pagetext';
		$asset = isset($data['asset_id']) ? $data['asset_id'] : 0;
		self::updateRef($newid, $pagetext, $asset);
		return $newid;
	}
	// Reference
	public static function updateRef ($itemId, $name, $asset, $id = null) {
		// create revision
		$item = self::load($itemId);
		if (!$item) return false;

		// create revision
		self::createRevision ($itemId);
		// update ref && content
		if (is_numeric($asset)) {
			$asset_id = $asset;
			$asset_type = 'asset';
		} else {
			$asset_type = $asset;
			$asset_id = ($id === null) ? JFactory::getApplication()->input->get('id') : $id;
		}
		$row = [
			'asset_type' => $asset_type,
			'asset_name' => $name,
			'asset_id' => $asset_id,
			'content' => $item->working_content,
		];
		return Table::updateRow (self::TABLE, $row, $itemId);
	}

	public static function cloneRef ($refid, $name, $asset) {
		$item = self::load($refid);

		if (!$item) return null;

		if (is_numeric($asset)) {
			$asset_id = $asset;
		} else {
			$asset_id = JFactory::getApplication()->input->getInt('id');
		}

		$data = [];
		$data['asset_type'] = $item->asset_type;
		$data['asset_name'] = $item->asset_name;
		$data['content'] = $item->content;
		$data['working_content'] = $item->working_content;
		$data['asset_id'] = (int)$asset_id;

		return Table::insertRow (self::TABLE, $data);
	}

	public static function getRefId ($name, $asset, $id = null) {
		if (is_numeric($asset)) {
			$asset_id = $asset;
			$asset_type = 'asset';
		} else {
			if (!$asset) $asset = JFactory::getApplication()->input->get('option');
			if (!$id) $id = JFactory::getApplication()->input->getInt('id');
			if (!$id) $id = self::detectAssetId($asset);
			$asset_type = $asset;
			$asset_id = $id;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__' . self::TABLE)
			->where($db->quoteName ('asset_type') . '=' . $db->quote($asset_type))
			->where($db->quoteName ('asset_name') . '=' . $db->quote($name))
			->where($db->quoteName ('asset_id') . '=' . $db->quote($asset_id));
			
		return $db->setQuery($query)->loadResult();
	}

	public static function detectAssetId($asset_type) {
		switch ($asset_type) {
			case 'com_virtuemart':
				$id = JFactory::getApplication()->input->getInt('virtuemart_product_id');
				return is_array($id) ? array_shift($id) : $id;
			default:
				return 0;
		}
	}

	// Revision
	public static function getLastRev($itemId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('max(rev)')
			->from('#__' . self::TABLEREV)
			->where($db->quoteName ('itemid') . '=' . $db->quote($itemId));
		$db->setQuery($query);	
		return $db->loadResult();
	}

	public static function getRevisions($itemId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__' . self::TABLEREV)
			->where($db->quoteName ('itemid') . '=' . $db->quote($itemId))
			->order('id DESC');
		$db->setQuery($query);	
		return $db->loadObjectList();
	}

	public static function getRevision($id) {
		return Table::getRow(self::TABLEREV, $id);
	}

	/*
	 * Create a revision from current item.
	 * @param: itemId or item object
	 * @param: custom revision title
	 * @return: revision id
	 */
	public static function createRevision ($itemId, $title = '') {
		$item = self::load($itemId);
		if (!$item) return false;
		$lastrev = self::getLastRev($item->id);
		if ($lastrev >= $item->rev) return false;

		$revision = [
			'itemid' => $item->id,
			'rev' => $item->rev,
			'content' => $item->working_content,
			'title' => $title
		];
		return Table::insertRow (self::TABLEREV, $revision);
	}
	public static function getImages($html,$css)
	{	
		$imgLocal = [];
	    preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $html, $images);
	   	if(isset($images[1])){
	   		foreach ($images[1] as $img) {
	   			if(!preg_match('/\/\//i', $img, $match)){
	   				$imgLocal[] = $img;
	   			}
	   		}
	   	}
	   	if(isset($css)){
	   		$re = '/url\(\s*[\'"]?(\S*\.(?:jpe?g|gif|png|jpg))[\'"]?\s*\)[^;}]*?/i';
	   		if (preg_match_all($re, $css, $matches)) {
			    foreach ($matches[1] as $imgCss) {
		   			if(!preg_match('/\/\//i', $imgCss, $matchCss)){
		   				$imgLocal[] = $imgCss;
		   			}
		   		}
			}
	   	}
	   	$imgs = !empty($imgLocal) ? implode('|', $imgLocal) : "";
	    return $imgs;
	}
	public static function getTemplateInfo($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id as id, template as name')
			->from('#__template_styles')
			->where($db->quoteName ('id') . '=' . $db->quote($id));
		$db->setQuery($query);	
		return $db->loadObject();
	}
}
