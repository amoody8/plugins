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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar as JToolBar;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Helper\ContentHelper as JHelperContent;

class T4pagebuilderViewPage extends JViewLegacy
{
        
    /**
     * The JForm object
     *
     * @var  JForm
     */
    protected $form;

    /**
     * The active item
     *
     * @var  object
     */
    protected $item;

    /**
     * The model state
     *
     * @var  object
     */
    protected $state;

    /**
     * The actions the user is authorised to perform
     *
     * @var  JObject
     */
    protected $canDo;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     *
     * @since   1.6
     */

    public function display($tpl = null)
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');
        $this->templateDefault = $this->get('TemplateDefault');
        $this->isT4 = $this->get('IsT4');
        $this->canDo = JHelperContent::getActions('com_t4pagebuilder', 'page', $this->item->id);
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
        //check revsion seting disabled;
        T4pagebuilderHelper::revSettings();
        $this->addToolbar();
        return parent::display($tpl);
    }
    protected function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            '<i class="fa fa-list-ul"></i> ' . JText::_('COM_T4PAGEBUILDER_PAGES'),
            'index.php?option=com_t4pagebuilder&view=pages',
            $vName == 'pages'
        );
    }
    protected function addToolBar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $user       = JFactory::getUser();
        $userId     = $user->id;
        $isNew      = ($this->item->id == 0);
        $bar 	= JToolBar::getInstance('toolbar');

        // Built the actions for new and existing records.
        $canDo = $this->canDo;
        // For new records, check the create permission.
        if ($isNew) {
            ToolbarHelper::apply('page.apply');
            ToolbarHelper::cancel('page.cancel');
            $title = 'T4 Page Builder - New Page';
        } else {

            // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
            $itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);
            // Can't save the record if it's checked out and editable
            if ($itemEditable) {
                $title = JText::_('COM_T4PAGEBUILDER_NEW_PAGE');
                $targetModalId = 'addNewPage';
                $icon = 'icon-new';
                $href = "index.php?option=com_t4pagebuilder&view=page&layout=modal&tmpl=component";
                $dhtml = '<div class="jpb-btn-group">
			   <a href="#" onclick="Joomla.submitbutton(\'page.apply\');" id="btn-save-page" class="btn btn-small button-apply btn-success"><i class="fal fa-save"></i> Save</a>
			   <a href="#" class="btn jpb-btn btn-success dropdown-toggle" data-toggle="dropdown"><i class="fal fa-chevron-down"></i></a>
			   <ul class="dropdown-menu">
			    <li><a class="btn btn-small button-save jpb-btn-save-close" onclick="Joomla.submitbutton(\'page.save\');" id="btn-save-close" href="#"><i class="fal fa-check"></i> Save &amp; Close</a></li>'.
                '<li><a class="btn btn-small button-save-copy jpb-btn-save-copy" onclick="Joomla.submitbutton(\'page.save2copy\');" id="btn-save-copy" href="#"><i class="fal fa-clone"></i> Save as Copy</a></li>
			  </ul>
			</div>';
                $bar->appendButton('custom', $dhtml, 'save');
                // We can save this record, but check the create permission to see if we can return to make a new one.
                $titleRev = JText::_('Revision Manager');
                $targetRevId = 'RevisionsModal'; //ImportModal
                $iconRev = 'fa fa-history';
                $btnRev = '<button data-toggle="modal" type="button" data-backdrop="static" data-target="#' . $targetRevId . '" class="btn btn-small btn-default btn-revisions">
				<span class="' . $iconRev . '" title="' . $titleRev . '"></span> ' . $titleRev . '</button>';
                $bar->appendButton('custom', $btnRev, 'revision');
            }
            $title = 'T4 Page Builder - Edit Page';

            ToolbarHelper::cancel('page.cancel', 'JTOOLBAR_CLOSE');
        }
 
        ToolbarHelper::title($title, 'T4 Page Builder');
    }
}
