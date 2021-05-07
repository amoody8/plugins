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
use Joomla\Registry\Registry;
use Joomla\String\StringHelper AS JStringHelper;
use Joomla\Utilities\ArrayHelper;
jimport('joomla.filesystem.file');

class Create extends Base {

	public function run () {
		$itemId = JFactory::getApplication()->input->getInt('id');
		$data = json_decode(file_get_contents('php://input'), true);
		if(!isset($data['id'])) $data['id'] = 0;
		if ($data['alias'] == null)
		{
			if (JFactory::getConfig()->get('unicodeslugs') == 1)
			{
				$data['alias'] = \JFilterOutput::stringURLUnicodeSlug($data['title']);
			}
			else
			{
				$data['alias'] = \JFilterOutput::stringURLSafe($data['title']);
			}

			$table = \JTable::getInstance('Page', 'T4pagebuilderTable');
			if ($table->load(array('alias' => $data['alias'])))
			{
				list($title, $alias) = $this->generateNewTitlePage($data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}

		}elseif($data['alias'] && $data['id'] == 0){
			list($title, $alias) = $this->generateNewTitlePage($data['alias'], $data['title']);
			$data['title'] = $title;
			$data['alias'] = $alias;
		}
		if(empty($data['access'])){
			$data['access'] = '1';
		}
		if(empty($data['created_by'])){
			$data['created_by'] = JFactory::getUser()->id;
		}
		if(empty($data['state'])){
			$data['state'] = 1;
		}
		if (isset($msg))
		{
			return ['error'=> $msg];
		}
		unset($data['id']);
		if(empty($data['page_key'])) $data['page_key'] = \JPB\Helper\Item::createPageKey();
		$newItemId = \JPB\Helper\Item::createPage($data);
		return ($newItemId && $newItemId != $itemId) ? ['newId' => $newItemId] : ["ok" => 1];
	}
	protected function getPageId($alias)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__jae_item')
			->where('alias = '. $db->quote($alias));
		$db->setQuery($query);
		return $db->loadResult();	
	}
		/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $parent_id  The id of the parent.
	 * @param   string   $alias      The alias.
	 * @param   string   $title      The title.
	 *
	 * @return  array    Contains the modified title and alias.
	 *
	 * @since   1.7
	 */
	protected function generateNewTitlePage($alias, $title)
	{
		// Alter the title & alias
		$table = \JTable::getInstance('Page', 'T4pagebuilderTable');
		while ($table->load(array('alias' => $alias)))
		{
			$title = JStringHelper::increment($title);
			$alias = JStringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

}