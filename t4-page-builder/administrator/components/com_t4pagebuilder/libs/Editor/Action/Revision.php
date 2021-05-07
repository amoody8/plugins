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
class Revision extends Base {
	public function run() {
		$revision = $this->revision();
		return $revision;
	}

	function updateItem($itemId, $revId) {
		$db = JFactory::getDbo();
		$item = \JPB\Helper\Item::load($itemId);
		if (!$item) return false;
		
		$content = $this->getRevisionContent($revId);
		$item_rev = $item->rev +1;
		$query = $db->getQuery(true);
		// Fields to update.
		$fields = array(
		    $db->quoteName('working_content') . ' = ' . $db->quote($content),
		    $db->quoteName('rev') . ' = ' . $db->quote($item_rev)

		);

		// Conditions for which records should be updated.
		$conditions = array(
		    $db->quoteName('id') . ' = '. $db->quote($itemId)
		);
		$itemTable = \JPB\Helper\Item::TABLE;
		$query->update($db->quoteName('#__' . $itemTable))->set($fields)->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();
		
		return $result;
	}


	function getRevisions($itemId) {
		$revisions = \JPB\Helper\Item::getRevisions($itemId);
		$item = \JPB\Helper\Item::load($itemId);
		$result = array();

		if ($revisions) {
			foreach ($revisions as $revision) {
				$rev = array();
				$rev['id'] = $revision->id;
				$rev['title'] = $revision->title;
				$rev['ctime'] = $revision->ctime;
				$rev['rev'] = $revision->rev;
				if($revision->rev == $item->rev){
					$rev['cur'] = 1;
				}else{
					$rev['cur'] = 0;
				}
				$result[] = $rev;
			}
		}

		return $result;
	}
	
	function getRevisionContent($id) {
		$revision = \JPB\Helper\Item::getRevision($id);
		return $revision ? $revision->content : '';
	}
	
	function getWorkingContent($itemId) {
		$item = \JPB\Helper\Item::load($itemId);
		return $item ? $item->working_content : '';
	}
	
	function createRevision($itemId, $title) {
		return \JPB\Helper\Item::createRevision($itemId, $title);
	}

	function delelteRevision($revId) {
		$db = JFactory::getDbo();
		$revTable = \JPB\Helper\Item::TABLEREV;
		$query = $db->getQuery(true);

		// delete all custom keys for user 1001.
		$conditions = array(
		    $db->quoteName('id') . ' = '. $revId 
		);

		$query->delete($db->quoteName('#__'.$revTable));
		$query->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();
		return $result;
	}


	function revision() {
		$input = JFactory::getApplication()->input;
		$itemId = $input->getInt('itemid');
		$task = $input->getVar('data');
		// get post data
		$data = json_decode(file_get_contents('php://input'), true);

		switch($task){
			case 'content':
				$result = $this->getRevisionContent($itemId);
				break;
			case 'reset':
				$result = $this->getWorkingContent($itemId);
				break;
			case 'revdel':
				$revId = isset($data['id']) ? $data['id'] : null;
				$result = $this->delelteRevision($revId);
				break;
			case 'save':
				$options = isset($data['options']) ? $data['options'] : null;
				if($options == 'update'){
					$revId = isset($data['revId']) ? $data['revId'] : null;

					$result = $this->updateItem($itemId,$revId);
				}elseif($options == 'insert'){
					$title = isset($data['title']) ? $data['title'] : null;
					$result = $this->createRevision($itemId, $title);
				}
				break;
			default:
				$result = $this->getRevisions($itemId);
				break;
		}
		return $result;
	}
}

