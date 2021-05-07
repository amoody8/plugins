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
use \Joomla\CMS\Factory as JFactory;
use \Joomla\CMS\Uri\Uri as JUri;
use \Joomla\CMS\Language\Text as JText;
use \Joomla\CMS\MVC\Controller\BaseController;

if (!JFactory::getUser()->authorise('core.edit', 'com_t4pagebuilder')) {
    throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}
JLoader::registerNamespace('JPB', __DIR__ . '/libs', false, false, 'psr4');
JLoader::register('T4pagebuilderHelper', __DIR__ . '/helpers/t4pagebuilder.php');

//check tables
T4pagebuilderHelper::checkTables();

// add field
if (!defined("JPB_PARAM")) {
    define('JPB_PARAM', 'act');
}
if (!defined("JPB_DEVMODE")) {
    define('JPB_DEVMODE', '');
}
if (!defined("JPB_PATH")) {
    define('JPB_PATH', __DIR__);
}
if (!defined("JPB_PATH_BASE")) {
    define('JPB_PATH_BASE', JUri::base(true)."/components/com_t4pagebuilder/");
}
if (!defined("JPB_MEDIA")) {
    define('JPB_MEDIA', '/media/t4pagebuilder/');
}
if (!defined("JPB_PATH_MEDIA")) {
    define('JPB_PATH_MEDIA', JPATH_ROOT . JPB_MEDIA);
}
if (!defined("JPB_MEDIA_BUILDER")) {
    define('JPB_MEDIA_BUILDER', JPB_MEDIA . 'builder/');
}
if (!defined("JPB_PATH_MEDIA_BUILDER")) {
    define('JPB_PATH_MEDIA_BUILDER', JPB_PATH_MEDIA . 'builder/');
}
if (!defined("JPB_PATH_SHARE_BLOCK")) {
    define('JPB_PATH_SHARE_BLOCK', JPB_PATH_MEDIA . 'public-html/Shared-Blocks/');
}
$lang = JFactory::getLanguage();
$extension = 'com_categories';
$base_dir = JPATH_SITE;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);
$doc = JFactory::getDocument();
$doc->addStylesheet(JPB_PATH_BASE . 'assets/css/ja-pagebuilder.css');
$script[] = 'var url_base = "'.JUri::base(true).'";';
if (version_compare(JVERSION, '4', 'ge')) {
    $script[] = "jQuery('body').addClass('j4')";
} else {
    $script[] = "jQuery('body').addClass('j3')";
}
$doc->addScriptDeclaration(implode("\n", $script));
$controller = BaseController::getInstance('T4pagebuilder', array('base_path' => JPATH_COMPONENT_ADMINISTRATOR));
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
