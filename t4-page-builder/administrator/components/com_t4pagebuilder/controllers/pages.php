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
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Session\Session as JSession;
use Joomla\CMS\MVC\Controller\AdminController as JControllerAdmin;
use JPB\Editor\Action\Export as Export;

/**
 * Articles list controller class.
 *
 * @since  1.6
 */
class T4pagebuilderControllerPages extends JControllerAdmin
{

    /**
     * Export page in json format
     */
    public function exports()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit('Invalid Token');
        $cid = $this->input->post->get('cid', array(), 'array');
        if (empty($cid)) {
            $app = JFactory::getApplication();
            /** Alternatively you may use chaining */
            $app->enqueueMessage(JText::_('No page selected'), 'error');
            $app->redirect(JRoute::_('index.php?option=com_t4pagebuilder&view=pages', false));
        }
        $data = Export::exports($cid);
    }

    public function getModel($name = 'Page', $prefix = 'T4pagebuilderModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
    public function clearData()
    {
        $result = T4pagebuilderHelper::clearDataPage();
        $app = JFactory::getApplication();

        if ($result) {
            /** Alternatively you may use chaining */
            $app->enqueueMessage(JText::_('Clear done!'), 'success');
        } else {
            $app->enqueueMessage(JText::_('No page selected'), 'error');
        }
        $app->redirect(JRoute::_('index.php?option=com_t4pagebuilder&view=pages', false));
    }
}
