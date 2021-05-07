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

use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use JPB\Helper\Table AS JPBTable;

/**
 * Item Model for an Page.
 *
 * @since  1.6
 */
class T4pagebuilderModelPage extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_T4PAGEBUILDER';

	/**
	 * The type alias for this content type (for example, 'com_t4pagebuilder.page').
	 *
	 * @var    string
	 * @since  3.2
	 */
	public $typeAlias = 'com_t4pagebuilder.page';


	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (empty($record->id) || $record->state != -2)
		{
			return false;
		}

		return JFactory::getUser()->authorise('core.delete', 'com_t4pagebuilder.page.' . (int) $record->id);
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing article.
		if (!empty($record->id))
		{
			return $user->authorise('core.edit.state', 'com_t4pagebuilder.page.' . (int) $record->id);
		}
		// New article, so check against the category.
		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_t4pagebuilder.category.' . (int) $record->catid);
		}
		// Default to component settings if neither article nor category known.
		return parent::canEditState($record);
	}
	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		// Reorder the articles within the category so the new article is first
		if (empty($table->id))
		{
			$table->reorder('catid = ' . (int) $table->catid . ' AND state >= 0');
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Page', $prefix = 'T4pagebuilderTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		return $item;
	}
	public function getTemplateDefault($name = false){
		$db = JFactory::getDbo();
		// get template style info
    $query = $db->getQuery(true);
    if($name){
    	$query->select('template');
    }else{
    	$query->select('id');
    }
    $query->from( $db->quoteName('#__template_styles') );
    $query->where( $db->quoteName('client_id') . ' = 0' );
    $query->where( $db->quoteName('home') . ' = ' . $db->quote('1') );
    $db->setQuery($query);
    return $db->loadResult();
	}
	public function getIsT4(){
		$templateName = self::getTemplateDefault(true);
		$return = false;
		if(file_exists(JPATH_ROOT . '/templates/'.$templateName."/error-t4.php")){
			$return = true;
		}
		return $return;
	} 
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		// Get the form.
		$form = $this->loadForm('com_t4pagebuilder.page', 'page', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		/*
		 * The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		 * The back end uses id so we use that the rest of the time and set it to 0 by default.
		 */
		$id = $jinput->get('a_id', $jinput->get('id', 0));

		// Determine correct permissions to check.
		if ($this->getState('page.id'))
		{
			$id = $this->getState('page.id');

			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');

			// Existing record. Can only edit own articles in selected categories.
			if ($app->isClient('administrator'))
			{
				$form->setFieldAttribute('catid', 'action', 'core.edit.own');
			}
			else
			// Existing record. We can't edit the category in frontend if not edit.state.
			{
				if ($id != 0 && (!$user->authorise('core.edit.state', 'com_t4pagebuilder.page.' . (int) $id))
					|| ($id == 0 && !$user->authorise('core.edit.state', 'com_t4pagebuilder')))
				{
					$form->setFieldAttribute('catid', 'readonly', 'true');
					$form->setFieldAttribute('catid', 'filter', 'unset');
				}
			}
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_t4pagebuilder.page.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_t4pagebuilder')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Prevent messing with article language and category when editing existing article with associations
		$assoc = JLanguageAssociations::isEnabled();

		// Check if article is associated
		if ($this->getState('page.id') && $app->isClient('site') && $assoc)
		{
			$associations = JLanguageAssociations::getAssociations('com_t4pagebuilder', '#__jae_item', 'com_t4pagebuilder.item', $id);

			// Make fields read only
			if (!empty($associations))
			{
				$form->setFieldAttribute('language', 'readonly', 'true');
				$form->setFieldAttribute('catid', 'readonly', 'true');
				$form->setFieldAttribute('language', 'filter', 'unset');
				$form->setFieldAttribute('catid', 'filter', 'unset');
			}
		}

		return $form;
	}

		/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = JFactory::getApplication();
		$data = $app->getUserState('com_t4pagebuilder.edit.page.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
			if ($this->getState('page.id') == 0)
			{
				$filters = (array) $app->getUserState('com_t4pagebuilder.pages.filter');
				$data->set(
					'state',
					$app->input->getInt(
						'state',
						((isset($filters['published']) && $filters['published'] !== '') ? $filters['published'] : null)
					)
				);
				$data->set('catid', $app->input->getInt('catid', (!empty($filters['category_id']) ? $filters['category_id'] : null)));
				$data->set('language', $app->input->getString('language', (!empty($filters['language']) ? $filters['language'] : null)));
				$data->set('access',
					$app->input->getInt('access', (!empty($filters['access']) ? $filters['access'] : JFactory::getConfig()->get('access')))
				);
			}
		}

		// If there are params fieldsets in the form it will fail with a registry object
		if (isset($data->params) && $data->params instanceof Registry)
		{
			$data->params = $data->params->toArray();
		}

		$this->preprocessData('com_t4pagebuilder.page', $data);

		return $data;
	}
	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   3.7.0
	 */
	public function validate($form, $data, $group = null)
	{
		// Don't allow to change the users if not allowed to access com_users.
		if (JFactory::getApplication()->isClient('administrator') && !JFactory::getUser()->authorise('core.manage', 'com_users'))
		{
			if (isset($data['created_by']))
			{
				unset($data['created_by']);
			}

			if (isset($data['modified_by']))
			{
				unset($data['modified_by']);
			}
		}

		return parent::validate($form, $data, $group);
	}
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$input  = JFactory::getApplication()->input;
		$filter = JFilterInput::getInstance();
		if(empty($data['type'])) $data['type'] = 'page';/*
		if(empty($data['asset_type'])) $data['asset_type'] = 'asset';
		if(empty($data['asset_name'])) $data['asset_name'] = 'pagetext';*/


		JLoader::register('CategoriesHelper', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/categories.php');

		// Create new category, if needed.
		$createCategory = true;

		// If category ID is provided, check if it's valid.
		if (is_numeric($data['catid']) && $data['catid'])
		{
			$createCategory = !CategoriesHelper::validateCategoryId($data['catid'], 'com_t4pagebuilder');
		}

		// Save New Category
		if ($createCategory && $this->canCreateCategory())
		{
			$table = array();

			// Remove #new# prefix, if exists.
			$table['title'] = strpos($data['catid'], '#new#') === 0 ? substr($data['catid'], 5) : $data['catid'];
			$table['parent_id'] = 1;
			$table['extension'] = 'com_t4pagebuilder';
			$table['language'] = $data['language'];
			$table['published'] = 1;

			// Create new category and get catid back
			$data['catid'] = CategoriesHelper::createCategory($table);
		}
		if(defined("T4B_DEVMODE") && isset($data['page_key'])){
			$key = explode('-', $data['page_key']);
			$data['page_key'] = $data['catid'] ."-". $data['id'] . "-".end($key);
		}
		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));
			foreach ($origTable as $data_key => $data_value) {
				if(!in_array($data_key, array("id",'title','alias'))){
					$data[$data_key] = $data_value;
				}
			}
			if ($data['title'] == $origTable->title)
			{
				list($title, $alias) = $this->generateNewTitlePage($data['alias'], $data['title']);
				$data['title'] = $title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}
			$data['state'] = 0;
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')))
		{
			if ($data['alias'] == null)
			{
				if (JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
				}
				else
				{
					$data['alias'] = JFilterOutput::stringURLSafe($data['title']);
				}

				$table = JTable::getInstance('Page', 'T4pagebuilderTable');
				if ($table->load(array('alias' => $data['alias'])))
				{
					$msg = JText::_('COM_T4PAGEBUILDER_SAVE_WARNING_ALIAS');
				}
				list($title, $alias) = $this->generateNewTitlePage($data['alias'], $data['title']);
				$data['alias'] = $alias;

			}
			if($data['alias']){
				$table = JTable::getInstance('Page', 'T4pagebuilderTable');
				if ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid'])) && ($table->id != $data['id'] || $data['id'] == 0))
				{
					$this->setError(\JText::_('COM_T4PAGEBUILDER_SAVE_WARNING_ALIAS'));
					return false;
				}
			}

			if (isset($msg))
			{
				$this->setError($msg);
				return false;
			}
		}
		if (parent::save($data))
		{
			//init content update saved
			if(!empty($data['id'])) \JPB\Helper\Item::updateRef($data['id'],$data['asset_name'],$data['asset_id']);
			return true;
		}

		return false;
	}
	/**
	 * Allows preprocessing of the JForm object.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'page')
	{
		if ($this->canCreateCategory())
		{
			$form->setFieldAttribute('catid', 'allowAdd', 'true');

			// Add a prefix for categories created on the fly.
			$form->setFieldAttribute('catid', 'customPrefix', '#new#');
		}

		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $parent_id  The id of the parent.
	 * @param   string   $alias      The alias.
	 * @param   string   $title      The title.
	 *
	 * @return  array    Contains the modified title and alias.
	 *
	 * @since   1.7
	 */
	protected function generateNewTitlePage($alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();
		while ($table->load(array('alias' => $alias)))
		{
			$title = StringHelper::increment($title);
			$alias = StringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

	
	public function hit()
	{
		return;
	}
	/**
	 * Is the user allowed to create an on the fly category?
	 *
	 * @return  boolean
	 *
	 * @since   3.6.1
	 */
	private function canCreateCategory()
	{
		return JFactory::getUser()->authorise('core.create', 'com_t4pagebuilder');
	}
	public function delete(&$pks)
	{
		$return = parent::delete($pks);

		if ($return)
		{
			$db = JFactory::getDbo();
			// Now check to see if this page was menu Item if so delete it from the #__menu table
			foreach($pks as $pageId){
				$query = $db->getQuery(true)
					->delete($db->quoteName('#__menu'))
					->where('link =  "index.php?option=com_t4pagebuilder&view=page&id=' . $pageId . '"')
					->where('home !=  "1"');
				$db->setQuery($query);
				$db->execute();
			}
		}

		return $return;
	}
	

}
