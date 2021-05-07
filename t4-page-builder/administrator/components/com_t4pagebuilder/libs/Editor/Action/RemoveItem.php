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

class RemoveItem extends Base {
	public function run() {
		$data = json_decode(file_get_contents('php://input'), true);
		if(isset($data['id'])){
			$result = self::itemDel($data['id']);
			return $result;
		}
		return false;
	}
	protected function itemDel($id){
		self::removeRevision($id);
		$db = \JFactory::getDbo();
		// delete all custom keys for user 1001.
		$conditions = array(
		    $db->quoteName('id') . ' = ' .$db->quote($id)
		);

		/*$query = $db->getQuery(true)
					->update($db->quoteName('#__jae_item'))
					->set($db->quoteName('content') . '=' .$db->quote(''))
					->set($db->quoteName('state') . '=' .$db->quote('0'))
					->set($db->quoteName('working_content') . '=' .$db->quote(''))
					->where($conditions);*/
		$query = $db->getQuery(true)
					->delete($db->quoteName('#__jae_item'))
					->where($conditions);

		$db->setQuery($query);
		return $db->execute();

	}
	protected function removeRevision($id)
	{
		$db = \JFactory::getDbo();
		// delete all custom keys for user 1001.
		$conditions = array(
		    $db->quoteName('itemid') . ' = ' .$db->quote($id)
		);

		$query = $db->getQuery(true)
					->delete($db->quoteName('#__jae_revision'))
					->where($conditions);

		$db->setQuery($query);
		return $db->execute();
	}

}

