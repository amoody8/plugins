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
use Joomla\Utilities\ArrayHelper;

require_once JPATH_ADMINISTRATOR . '/components/com_menus/models/item.php';
require_once JPATH_ADMINISTRATOR . '/components/com_menus/tables/menu.php';		
		
/**
 * Menu Item Model for Menus.
 *
 * @since  1.6
 */
class T4pagebuilderModelMenu extends JModelAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_T4PAGEBUILDER_MENU_ITEM';

	/**
	 * Model context string.
	 *
	 * @var  string
	 */
	protected $_context = 'com_t4pagebuilder.menu';


	/**
	 * Method to test whether the state of a record can be edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected $type = 'menu';
	
	public function __construct($config = array()) {
		$lang = JFactory::getLanguage();
		$extension = 'com_menus';
		$language_tag = JFactory::getLanguage()->getTag();
		$lang->load($extension, JPATH_ADMINISTRATOR, $language_tag, true);
		parent::__construct($config);
	}
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Page', $prefix = 'T4pagebuilderTable', $config = array())
	{
		$table = JTable::getInstance($type, $prefix, $config);
		
		return $table;
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$id = $app->input->getInt('id');
		$this->setState('page.id', $id);
	}
	public function getItem($pk = null) {
		$input = JFactory::getApplication()->input;
		$id = $input->get('id',0);
		$item = parent::getItem($id);
		$menuid = $input->get('menuid', 0);
		if ($menuid) {
			$menu = $this->getMenuModel()->getItem($menuid);
		} elseif($id) {
			$menu = $this->getMenuByPage($id);
		} else {
			$menu = null;
		}

		$item = $this->syncMenuData($item, $menu);
		return $item;
	}
	/**
	 * Method to get a menu item.
	 *
	 * @param   integer  $itemId  The id of the menu item to get.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function syncMenuData($item, $menu) {
		if (!empty($menu)) {
			$item->menuid = $menu->id;
			if (is_array($menu->params)) {
				$menu_params = $menu->params;
			} else {
				$menu_params = (array) json_decode($menu->params);
			}
			$item->meta_description =  isset($menu_params['menu-meta_description']) ? $menu_params['menu-meta_description'] : '';
			$item->meta_keywords = isset($menu_params['menu-meta_keywords']) ? $menu_params['menu-meta_keywords'] : '';
			$item->robots = isset($menu_params['robots']) ? $menu_params['robots'] : '';
			$item->parent_id = $menu->parent_id;
			$item->state = $menu->published;
			$item->menutype = $menu->menutype;
			$item->access = $menu->access;
			$item->menuordering= $menu->id;
			$item->template_style_id = $menu->template_style_id;
		}
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_t4pagebuilder.menu',
			'menu',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}
	
	protected function loadFormData()
	{
		$data = $this->getItem();

		return $data;
	}
	protected function num_duplicated_alias($id, $alias)
	{
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		
		$query->select( $db->quoteName('alias') )
				
				->from( $db->quoteName('#__jae_item') )
				
				->where( $db->quoteName('alias').'='.$db->quote($alias) )
				
				->where( $db->quoteName('id').'!='.$id );
		
		$db->setQuery($query);

		$db->execute();
		
		return $db->getNumRows();
	}
	
	public function save($data)
	{
		$input = JFactory::getApplication()->input;
		$data['type'] = 'component';
		if( empty($data['alias']) )
		{
			$data['alias'] = JabuilderHeper::stringUrlsafe($data['title']);
		}
		
		// solved duplicate alias
		if( $this->num_duplicated_alias( $data['id'], $data['alias'] ) )
		{
			for ( $i = 1; $i < 100; $i++) 
			{
				$new_alias = $data['alias'].'-'.$i;
				
				if( !$this->num_duplicated_alias( $data['id'], $new_alias ) )
				{
					$data['alias'] = $new_alias;
					break;
				}
			}
		}
		// $this->addNewMenu($data);
		return $this->addNewMenu($data);
	}

	/**
	 * Gets a list of all mod_mainmenu modules and collates them by menutype
	 *
	 * @return  array
	 *
	 * @since   1.6
	 */
	public function &getModules()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->from('#__modules as a')
			->select('a.id, a.title, a.params, a.position')
			->where('module = ' . $db->quote('mod_menu'))
			->select('ag.title AS access_title')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		$db->setQuery($query);

		$modules = $db->loadObjectList();

		$result = array();

		foreach ($modules as &$module)
		{
			$params = new Registry($module->params);

			$menuType = $params->get('menutype');

			if (!isset($result[$menuType]))
			{
				$result[$menuType] = array();
			}

			$result[$menuType][] = & $module;
		}

		return $result;
	}
	public function getComponentId()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('extension_id')
			->from('#__extensions')
			->where('element = ' . $db->quote('com_t4pagebuilder'));
		$db->setQuery($query);
		return $db->loadResult();
	}

	public function getMenuModel() {
		$model = JModelAdmin::getInstance('Item', 'MenusModel');
		return $model;
	}
	
	public function getParentMenuLevel($parent_id) {
		$parent = $this->getMenuModel()->getItem($parent_id);
		return $parent->level;
	}
	
	function getMenuByPage($id=null) {
		$q = "SELECT * FROM #__menu ";
		$q .= "WHERE link = 'index.php?option=com_t4pagebuilder&view=page&id=$id' and published != -2 order by id desc";
		$db = JFactory::getDbo()->setQuery($q);
		$menu = $db->loadObject();
		return $menu;
	}

	public function addNewMenu($data) {

		$table = JTable::getInstance('Menu', 'MenusTable');
		$table->title = $data['title'];
		$table->alias = $data['alias'];
		$table->published = $data['published'];
		$table->access = $data['access'];
		$table->menutype = $data['menutype'];
		$table->parent_id = $data['parent_id'];
		$id = $this->getState('page.id');
		$table->link = $data['link'];
		$table->type = 'component';
		$component = JComponentHelper::getComponent('com_t4pagebuilder');
		$table->component_id = $component->id;
		$table->browserNav = $data['browserNav'];
		$table->template_style_id = $data['template_style_id'];
		$table->language = '*';
		$table->level = $this->getParentMenuLevel($table->parent_id) + 1;

		$params = new stdClass();
		$params->{'menu-meta_description'} = $data['meta_description'];
		$params->{'menu-meta_keywords'} = $data['meta_keywords'];
		$params->robots = $data['robots'];
		
		$table->params = json_encode($params);
		$table->setLocation($data['parent_id'], 'last-child');
		if($table->store()){
			return $table;
		}
		$error = $table->getError();
		$this->setError($error);
		// JFactory::getApplication()->enqueueMessage($error, 'error');
		return false;
	}
}
