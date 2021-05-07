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

class T4pagebuilderTablePage extends JTable
{
    public function __construct(&$db)
    {
        parent::__construct('#__jae_item', 'id', $db);
        // Set the alias since the column is called state
        $this->setColumnAlias('published', 'state');
    }
}
