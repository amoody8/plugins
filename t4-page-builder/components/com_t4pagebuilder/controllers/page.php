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
defined('_JEXEC') or die;

class T4pagebuilderControllerPage extends JControllerBase
{
    /**
     * summary
     */
    public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		// JLoader::registerNamespace('JPB', __DIR__ . '/src', false, false, 'psr4');

		// // add field
		// define ('JAE_PARAM', 'jpb');
		// define ('JAE_PATH', __DIR__);
		// define ('JAE_MEDIA', '/media/editors/jaeditor/');
		// define ('JAE_MEDIA_BUILDER', JAE_MEDIA . 'builder/');
	}
}

?>