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

class T4pagebuilderViewMenu extends JViewLegacy
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

	function display($tpl=null)
	{
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$input = JFactory::getApplication()->input;
		$opt = $input->get('opt');
		if ($opt) {
			$option = json_decode( base64_decode($opt) );
			$title = isset($option->title) ? $option->title: '';
			$alias = isset($option->alias) ? $option->alias: '';
			$this->form->setValue('title','', $title);
			$this->form->setValue('alias','', $alias);
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
 
			return false;
		}
		
		// Set the toolbar
		$this->addToolBar();
		parent::display($tpl);
	}
	
	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;
 
		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);
 
		$isNew = ($this->item->id == 0);
		
		JToolBarHelper::apply('menu.apply');
		
		JToolBarHelper::save('menu.save');
		
		JToolBarHelper::cancel(
			'menu.cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
		
		if ($isNew)
		{
			$title = 'New Page';
		}
		else
		{
			$title = 'Edit Page';
			
			$bar = JToolbar::getInstance('toolbar');
		
			$layout = new JLayoutFile('toolbar.live');

			$bar->appendButton('Custom', $layout->render(array()), '');
		}
 
		JToolBarHelper::title($title, 'Ja Builder');
	}
}




?>