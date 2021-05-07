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

class Table {
	public static function getRow ($table, $id, $key = 'id') {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__' . $table)
			->where($key . '=' . $db->quote($id));
		$row = $db->setQuery($query)->loadObject();

		return $row;
	}

	public static function updateRow ($table, $data, $id, $key = 'id') {
		$db = JFactory::getDbo();
                $result = false;
		$fields = [];
		foreach ($data as $name => $value) {
			$fields[] = $db->quoteName ($name) . '=' . $db->quote($value);
		}
		// field update
		$fields[] = $db->quoteName ('updated') . '=NOW()';
		$query = $db->getQuery(true);
		$query->update('#__' . $table)
			->set($fields)
			->where($db->quoteName ($key) . '=' . $db->quote($id));

		$db->setQuery($query);

		if($db->execute()){
	        $result = true;
	    }
		return $result;
	}

	public static function insertRow ($table, $data) {
		$db = JFactory::getDbo();
		$fields = [];
		foreach ($data as $name => $value) {
			if($name == 'tag_id'){
				$fields[$db->quoteName ($name)] = $db->quote((int)$value);
			}else{
				$fields[$db->quoteName ($name)] = $db->quote($value);
			}
			
		}
		if($table == 'jae_item'){
			if(!isset($data['asset_type'])) $fields[$db->quoteName ('asset_type')] = $db->quote('asset');
			if(!isset($data['asset_name'])) $fields[$db->quoteName ('asset_name')] = $db->quote('pagetext');
			if(!isset($data['type'])) $fields[$db->quoteName ('type')] = $db->quote('page');
			if(!isset($data['hits'])) $fields[$db->quoteName ('hits')] = $db->quote(0);
			if(!isset($data['catid'])) $fields[$db->quoteName ('catid')] = $db->quote(8);
			
		}
		$query = $db->getQuery(true);
		$query->insert('#__' . $table)
			->columns(array_keys($fields))
			->values(implode(',', $fields));
		$db->setQuery($query);
		$db->execute();
		return $db->insertid();
	}

    public static function encodeData ($data) {
    	return @json_encode($data);
    	return base64_encode(gzdeflate(rawUrlEncode(json_encode($data))));
    }

    public static function decodeData ($data) {
    	$data = @json_decode($data, true);
    	return $data ? $data : [];

    	$_data = rawUrlDecode(gzinflate(base64_decode($data)));
    	return json_decode($_data, true);
    }	
}