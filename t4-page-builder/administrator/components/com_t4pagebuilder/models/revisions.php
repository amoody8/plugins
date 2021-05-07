<?php

/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:        https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */
defined('_JEXEC') or die;

class T4pagebuilderModelRevisions extends JModelList
{
    /**
     * summary
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }
    public function getRevisions () {
    	$input = JFactory::getApplication()->input;
    	$id = $input->getInt('id');
    	$db = JFactory::getDbo();
    	$query = $db->getQuery(true);
    	$query->select ("*")
    		->from($db->quotename('#__jae_revision'))
    		->where('itemid = ' . $db->quote($id))
    		->order('id DESC');
		$db->setQuery($query);
		return $db->loadObjectList();
    }
    public function delete($cid){

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__jae_revision')
			->where($db->quotename('id') . " IN (" . implode(',', $cid) . ")");
		$db->setQuery($query);
		return $db->execute();

    }
    public function deleteAll($page_id)
    {
    	$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__jae_revision')
			->where($db->quotename('itemid') . " = " . $db->quote($page_id));
		$db->setQuery($query);
		return $db->execute();
    }
}


?>
