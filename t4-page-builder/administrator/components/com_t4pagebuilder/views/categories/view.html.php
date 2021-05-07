<?php

defined('_JEXEC') or die;

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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class T4pagebuilderViewCategories extends JViewLegacy
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
		if($this->getLayout() !== 'modal'){
			T4pagebuilderHelper::addSubmenu('categories');
		}
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->assoc         = $this->get('Assoc');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// Levels filter - Used in Hathor.
		$this->f_levels = array(
			JHtml::_('select.option', '1', JText::_('J1')),
			JHtml::_('select.option', '2', JText::_('J2')),
			JHtml::_('select.option', '3', JText::_('J3')),
			JHtml::_('select.option', '4', JText::_('J4')),
			JHtml::_('select.option', '5', JText::_('J5')),
			JHtml::_('select.option', '6', JText::_('J6')),
			JHtml::_('select.option', '7', JText::_('J7')),
			JHtml::_('select.option', '8', JText::_('J8')),
			JHtml::_('select.option', '9', JText::_('J9')),
			JHtml::_('select.option', '10', JText::_('J10')),
		);
		if(version_compare(JVERSION, '4', "ge")){
			$this->addToolbarJ4();
		}else{
			$this->addToolbar();

		}
		$this->sidebar = JHtmlSidebar::render();
		return parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$categoryId = $this->state->get('filter.category_id');
		$component  = $this->state->get('filter.component');
		$section    = $this->state->get('filter.section');
		$canDo      = JHelperContent::getActions($component, 'category', $categoryId);
		$user       = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');

		// Avoid nonsense situation.
		if ($component == 'com_categories')
		{
			return;
		}
		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = JFactory::getLanguage();
		$lang->load($component, JPATH_BASE, null, false, true)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

		// Load the category helper.
		JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');
		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component . ($section ? "_$section" : '')) . '_CATEGORIES_TITLE'))
		{
			$title = JText::_('COM_T4PAGEBUILDER') . ' - ' .JText::_($component_title_key);
		}
		elseif ($lang->hasKey($component_section_key = strtoupper($component . ($section ? "_$section" : ''))))
		// Else if the component section string exits, let's use it
		{
			$title = JText::_('COM_T4PAGEBUILDER') . ' - ' . JText::sprintf('COM_T4PAGEBUILDER_CATEGORIES_TITLE', $this->escape(JText::_($component_section_key)));
		}
		else
		// Else use the base title
		{
			$title = JText::_('COM_T4PAGEBUILDER') . ' - ' . JText::_('COM_T4PAGEBUILDER_CATEGORIES_BASE_TITLE');
		}
		// Load specific css component
		JHtml::_('stylesheet','com_categories/administrator/categories.css', array('version' => 'auto', 'relative' => true));

		// Prepare the toolbar.
		JToolbarHelper::title($title, 'folder categories ' . substr($component, 4) . ($section ? "-$section" : '') . '-categories');


		if ($canDo->get('core.create') || count($user->getAuthorisedCategories($component, 'core.create')) > 0)
		{
			JToolbarHelper::addNew('category.add');
		}

		if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
		{
			JToolbarHelper::editList('category.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('categories.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::archiveList('categories.archive');
		}

		if (JFactory::getUser()->authorise('core.admin'))
		{
			JToolbarHelper::checkin('categories.checkin');
		}

		// Add a batch button
		if ($canDo->get('core.create')
			&& $canDo->get('core.edit')
			&& $canDo->get('core.edit.state'))
		{
			$title = JText::_('JTOOLBAR_BATCH');
			// Instantiate a new JLayoutFile instance and render the batch button
			JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
			$message = "alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));";
			$dhtml = "<button type='button' data-toggle='modal' onclick=\"if (document.adminForm.boxchecked.value==0){" . $message . "}else{jQuery( '#collapseModal' ).modal('show'); return true;}\" class='btn btn-small'>
				<span class='fal fa-square' aria-hidden='true'></span>
				" . $title . "
			</button>";
			$bar->appendButton('Custom', $dhtml, 'batch');
		}

		if ($canDo->get('core.admin'))
		{
			// JToolbarHelper::custom('categories.rebuild', 'fal fa-redo','','JTOOLBAR_REBUILD', true);
			$title = JText::_('JTOOLBAR_REBUILD');
			// Instantiate a new JLayoutFile instance and render the batch button
			$message = "alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));";
			$dhtml = "<button type='button' data-toggle='modal' onclick=\"if (document.adminForm.boxchecked.value==0){" . $message . "}else{Joomla.submitbutton('categories.rebuild');}\" class='btn btn-small'>
				<span class='fal fa-redo' aria-hidden='true'></span>
				" . $title . "
			</button>";
			$bar->appendButton('Custom', $dhtml, 'rebuild');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete', $component))
		{
			JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('categories.trash');
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences($component);
		}

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component . ($section ? "_$section" : '')) . '_CATEGORIES_HELP_KEY'))
		{
			$ref_key = 'JHELP_COMPONENTS_' . strtoupper(substr($component, 4) . ($section ? "_$section" : '')) . '_CATEGORIES';
		}

		/*
		 * Get help for the categories view for the component by
		 * -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		 * -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		 * -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		 */
		if ($lang->hasKey($lang_help_url = strtoupper($component) . '_HELP_URL'))
		{
			$debug = $lang->setDebug(false);
			$url = JText::_($lang_help_url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
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
		$categoryId = $this->state->get('filter.category_id');
		$component  = $this->state->get('filter.component');
		$section    = $this->state->get('filter.section');
		$canDo      = ContentHelper::getActions($component, 'category', $categoryId);
		$user       = Factory::getUser();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Avoid nonsense situation.
		if ($component == 'com_categories')
		{
			return;
		}

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Factory::getLanguage();
		$lang->load($component, JPATH_BASE)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component);

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component . ($section ? "_$section" : '')) . '_CATEGORIES_TITLE'))
		{
			$title = Text::_($component_title_key);
		}
		elseif ($lang->hasKey($component_section_key = strtoupper($component . ($section ? "_$section" : ''))))
		// Else if the component section string exits, let's use it
		{
			$title = Text::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', $this->escape(Text::_($component_section_key)));
		}
		else
		// Else use the base title
		{
			$title = Text::_('COM_CATEGORIES_CATEGORIES_BASE_TITLE');
		}

		// Load specific css component
		/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = $this->document->getWebAssetManager();
		$wa->getRegistry()->addExtensionRegistryFile($component);

		if ($wa->assetExists('style', $component . '.admin-categories'))
		{
			$wa->useStyle($component . '.admin-categories');
		}
		else
		{
			$wa->registerAndUseStyle($component . '.admin-categories', $component . '/administrator/categories.css');
		}

		// Prepare the toolbar.
		ToolbarHelper::title($title, 'folder categories ' . substr($component, 4) . ($section ? "-$section" : '') . '-categories');

		if ($canDo->get('core.create') || count($user->getAuthorisedCategories($component, 'core.create')) > 0)
		{
			$toolbar->addNew('category.add');
		}

		if ($canDo->get('core.edit.state') || Factory::getUser()->authorise('core.admin'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fas fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if ($canDo->get('core.edit.state'))
			{
				$childBar->publish('categories.publish')->listCheck(true);

				$childBar->unpublish('categories.unpublish')->listCheck(true);

				$childBar->archive('categories.archive')->listCheck(true);
			}

			if (Factory::getUser()->authorise('core.admin'))
			{
				$childBar->checkin('categories.checkin')->listCheck(true);
			}

			if ($canDo->get('core.edit.state') && $this->state->get('filter.published') != -2)
			{
				$childBar->trash('categories.trash')->listCheck(true);
			}

			// Add a batch button
			if ($canDo->get('core.create')
				&& $canDo->get('core.edit')
				&& $canDo->get('core.edit.state'))
			{
				$childBar->popupButton('batch')
					->text('JTOOLBAR_BATCH')
					->selector('collapseModal')
					->listCheck(true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->standardButton('refresh')
				->text('JTOOLBAR_REBUILD')
				->task('categories.rebuild');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete', $component))
		{
			$toolbar->delete('categories.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			$toolbar->preferences($component);
		}

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component . ($section ? "_$section" : '')) . '_CATEGORIES_HELP_KEY'))
		{
			$ref_key = 'JHELP_COMPONENTS_' . strtoupper(substr($component, 4) . ($section ? "_$section" : '')) . '_CATEGORIES';
		}

		/*
		 * Get help for the categories view for the component by
		 * -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		 * -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		 * -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		 */
		if ($lang->hasKey($lang_help_url = strtoupper($component) . '_HELP_URL'))
		{
			$debug = $lang->setDebug(false);
			$url = Text::_($lang_help_url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
		}

		$toolbar->help($ref_key, ComponentHelper::getParams($component)->exists('helpURL'), $url);
	}
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.lft'       => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.title'     => JText::_('JGLOBAL_TITLE'),
			'a.access'    => JText::_('JGRID_HEADING_ACCESS'),
			'language'    => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id'        => JText::_('JGRID_HEADING_ID'),
		);
	}
}




?>