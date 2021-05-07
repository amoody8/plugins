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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Helper\ContentHelper as JHelperContent;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar as JToolBar;
use Joomla\CMS\Toolbar\ToolbarHelper as JToolbarHelper;

use \JPB\Factory as T4bHelper;

class T4pagebuilderViewPages extends JViewLegacy
{
        
    /**
     * The item authors
     *
     * @var  stdClass
     *
     * @deprecated  4.0  To be removed with Hathor
     */
    protected $authors;

    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var  JPagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var  object
     */
    protected $state;

    /**
     * Form object for search filters
     *
     * @var  JForm
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var  array
     */
    public $activeFilters;

    /**
     * The sidebar markup
     *
     * @var  string
     */
    protected $sidebar;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */

    public function display($tpl = null)
    {
        if ($this->getLayout() !== 'modal') {
            T4pagebuilderHelper::addSubmenu('pages');
            //check revsion seting disabled;
            T4pagebuilderHelper::revSettings();
        }
        $this->items			= $this->get('Items');
        $this->menuitems		= $this->get('MenuItems');
        $this->pagination		= $this->get('Pagination');
        $this->state         	= $this->get('State');
        $this->authors       	= $this->get('Authors');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
        T4bHelper::getJVersion() ? $this->addToolbarJ4() :  $this->addToolbar();
        $this->sidebar = JHtmlSidebar::render();
        
        return parent::display($tpl);
    }
    
    protected function addToolBar()
    {
        $state	= $this->get('State');
        $canDo	= JHelperContent::getActions('com_t4pagebuilder');
        $user	= JFactory::getUser();
        $bar 	= JToolBar::getInstance('toolbar');
        $component = "com_t4pagebuilder";
        $section = "";
        JToolBarHelper::title(JText::_('COM_T4PAGEBUILDER') . ' - ' . JText::_('COM_T4PAGEBUILDER_PAGES'));

        // Need to load the menu language file as mod_menu hasn't been loaded yet.
        $lang = JFactory::getLanguage();
        $lang->load($component, JPATH_BASE, null, false, true)
        || $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

        // new page button
        if ($canDo->get('core.admin')) {
            $title = JText::_('COM_T4PAGEBUILDER_NEW_PAGE');
            $targetModalId = 'addnewpage';
            $icon = 'icon-new';
            $dhtml = '<button data-toggle="modal" type="button" data-target="#' . $targetModalId . '" class="btn btn-small btn-success">
			<span class="' . $icon . '" title="' . $title . '"></span> ' . $title . '</button>';
            $bar->appendButton('custom', $dhtml, 'new');
            // JToolbarHelper::addNew('page.add', 'COM_t4PAGEBUILDER_NEW_PAGE');
        }
        // edit button
        if ($canDo->get('core.edit')) {
            JToolbarHelper::editList('page.edit');
        }

        // publish and unpublish button
        if ($canDo->get('core.edit.state')) {
            JToolbarHelper::publish('pages.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('pages.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            // JToolbarHelper::checkin('pages.checkin');
        }

        // delete and trush button
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
            JToolbarHelper::deleteList('', 'pages.delete', 'JTOOLBAR_EMPTY_TRASH');
        } elseif ($canDo->get('core.edit.state')) {
            JToolbarHelper::trash('pages.trash');
        }

        //import button
        $titleImport = JText::_('COM_T4PAGEBUILDER_IMPORT');
        $targetImportId = 'importpage'; //ImportModal
        $iconImport = 'icon-import';
        $import = '<button data-toggle="modal" type="button" data-backdrop="static" data-target="#' . $targetImportId . '" class="btn btn-small btn-default">
		<span class="' . $iconImport . '" title="' . $titleImport . '"></span> ' . $titleImport . '</button>';
        $bar->appendButton('custom', $import, 'Import');

        //export button
        $bar->appendButton('Standard', 'export', 'COM_T4PAGEBUILDER_EXPORT', 'pages.exports', true, false);

        //update style button
        $titleUpdateStyle = JText::_('COM_T4PAGEBUILDER_UPDATE_STYLE');
        if (version_compare(JVERSION, '4', 'ge')) {
            $UpdateStyle = '<joomla-toolbar-button id="toolbar-updateStyle" style="display:none;">
						<button class="btn btn-small button-update-style">
	                    <span class="icon-update-style" aria-hidden="true"></span>
	                    Style Update</button>
	                    </joomla-toolbar-button>';
        } else {
            $UpdateStyle = '<button class="btn btn-small button-update-style">
                <span class="icon-update-style" aria-hidden="true"></span>
                Style Update</button>';
        }
        $users = JLayoutHelper::render('layouts.users', array(), JPB_PATH . '/libs/');
        $bar->appendButton('custom', $UpdateStyle, 'updateStyle');
        //new feature
        // $bar->appendButton('custom',$users,'users');

        if ($canDo->get('core.create')) {
            JToolBarHelper::preferences('com_t4pagebuilder');
        }
    }
    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbarJ4()
    {
        $state		= $this->get('State');
        $canDo		= JHelperContent::getActions('com_t4pagebuilder');
        $user		= JFactory::getUser();
        $toolbar 	= JToolBar::getInstance('toolbar');
        $component 	= "com_t4pagebuilder";

        // Need to load the menu language file as mod_menu hasn't been loaded yet.
        $lang = JFactory::getLanguage();
        $lang->load($component, JPATH_BASE, null, false, true)
        || $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

        JToolBarHelper::title(JText::_('COM_T4PAGEBUILDER') . ' - ' . JText::_('COM_T4PAGEBUILDER_PAGES'));

        if ($canDo->get('core.create') || count($user->getAuthorisedCategories($component, 'core.create')) > 0) {
            $title = JText::_('COM_T4PAGEBUILDER_NEW_PAGE');
            $targetModalId = 'addnewpage';
            $icon = 'icon-new';
            $dhtml = '<button data-toggle="modal" type="button" data-target="#' . $targetModalId . '" class="btn btn-small btn-success">
			<span class="' . $icon . '" title="' . $title . '"></span> ' . $title . '</button>';
            $toolbar->appendButton('custom', $dhtml, 'new');
        }

        if ($canDo->get('core.edit.state') || JFactory::getUser()->authorise('core.admin')) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fas fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.edit.state')) {
                $childBar->publish('pages.publish')->listCheck(true);

                $childBar->unpublish('pages.unpublish')->listCheck(true);
            }

            if ($canDo->get('core.edit.state') && $this->state->get('filter.published') != -2) {
                $childBar->trash('pages.trash')->listCheck(true);
            }
        }
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete', $component)) {
            $toolbar->delete('pages.delete')
                ->text('JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }
        //import button
        $titleImport = JText::_('COM_T4PAGEBUILDER_IMPORT');
        $targetImportId = 'importpage'; //ImportModal
        $iconImport = 'icon-import';
        $import = '<button data-toggle="modal" type="button" data-backdrop="static" data-target="#' . $targetImportId . '" class="btn btn-small btn-default">
		<span class="' . $iconImport . '" title="' . $titleImport . '"></span> ' . $titleImport . '</button>';
        $toolbar->appendButton('custom', $import, 'Import');

        //export button
        $toolbar->appendButton('Standard', 'export', 'COM_T4PAGEBUILDER_EXPORT', 'pages.exports', true, false);

        //update style button
        $titleUpdateStyle = JText::_('COM_T4PAGEBUILDER_UPDATE_STYLE');
        if (version_compare(JVERSION, '4', 'ge')) {
            $UpdateStyle = '<joomla-toolbar-button id="toolbar-updateStyle" style="display:none;">
						<button class="btn btn-small button-update-style">
	                    <span class="icon-update-style" aria-hidden="true"></span>
	                    Style Update</button>
	                    </joomla-toolbar-button>';
        } else {
            $UpdateStyle = '<button class="btn btn-small button-update-style">
                <span class="icon-update-style" aria-hidden="true"></span>
                Style Update</button>';
        }
        $toolbar->appendButton('custom', $UpdateStyle, 'updateStyle');


        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            $toolbar->preferences($component);
        }
    }
    protected function getSortFields()
    {
        return array(
            'a.ordering'  	=> JText::_('JGRID_HEADING_ORDERING'),
            'a.state' 	=> JText::_('JSTATUS'),
            'a.title' 		=> JText::_('JGLOBAL_TITLE'),
            'a.access' 		=> JText::_('JGRID_HEADING_ACCESS'),
            'a.language' 	=> JText::_('JGRID_HEADING_LANGUAGE'),
            'a.id' 			=> JText::_('JGRID_HEADING_ID')
        );
    }
}
