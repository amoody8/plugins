<?php
/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 *------------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
JLoader::registerNamespace('JPB', JPATH_ADMINISTRATOR . '/components/com_t4pagebuilder/libs', false, false, 'psr4');
JLoader::register('T4pagebuilderHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/t4pagebuilder.php');

// add field
if(!defined("JPB_PARAM")) define ('JPB_PARAM', 'act');
if(!defined("JPB_DEVMODE")) define ('JPB_DEVMODE', '');
if(!defined("JPB_PATH")) define ('JPB_PATH', __DIR__);
if(!defined("JPB_PATH_BASE")) define ('JPB_PATH_BASE', \JUri::base(true)."/components/com_t4pagebuilder/");
if(!defined("JPB_MEDIA")) define ('JPB_MEDIA', '/media/t4pagebuilder/');
if(!defined("JPB_MEDIA_BUILDER")) define ('JPB_MEDIA_BUILDER', JPB_MEDIA . 'builder/');
if(!defined("JPB_PATH_MEDIA_BUILDER")) define ('JPB_PATH_MEDIA_BUILDER', JPATH_ROOT . JPB_MEDIA . 'builder/');

$input = JFactory::getApplication()->input;

// by pass other task
$input->set('view', 'page');

$user  = JFactory::getUser();

$checkCreateEdit = ($input->get('view') === 'pages' && $input->get('layout') === 'modal') || ($input->get('view') === 'page' && $input->get('layout') === 'pagebreak');

if ($checkCreateEdit)
{
	// Can create in any category (component permission) or at least in one category
	$canCreateRecords = $user->authorise('core.create', 'com_t4pagetbuilder')
		|| count($user->getAuthorisedCategories('com_t4pagetbuilder', 'core.create')) > 0;

	// Instead of checking edit on all records, we can use **same** check as the form editing view
	$values = (array) JFactory::getApplication()->getUserState('com_t4pagetbuilder.edit.page.id');
	$isEditingRecords = count($values);

	$hasAccess = $canCreateRecords || $isEditingRecords;

	if (!$hasAccess)
	{
		JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');

		return;
	}
}
$controller = JControllerLegacy::getInstance('t4pagebuilder');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
