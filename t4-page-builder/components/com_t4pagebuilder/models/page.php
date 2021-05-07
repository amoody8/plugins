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
use Joomla\Utilities\IpHelper;

class T4pagebuilderModelPage extends JModelItem
{
	protected $_context = 'com_t4pagebuilder.page';
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('page.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = JFactory::getUser();

		// If $pk is set then authorise on complete asset, else on component only
		$asset = empty($pk) ? 'com_t4pagebuilder' : 'com_t4pagebuilder.page.' . $pk;

		if ((!$user->authorise('core.edit.state', $asset)) && (!$user->authorise('core.edit', $asset)))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
		$this->setState('filter.language', JLanguageMultilang::isEnabled());

	}
	function getItem($pk = null)
	{
		$user = JFactory::getUser();
		$app = JFactory::getApplication('site');
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('page.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}
		if (!isset($this->_item[$pk]))
		{
			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true)
				->select(
					$this->getState('item.select','t4b.*')
				);
				$query->from('#__jae_item AS t4b')
					->where('t4b.id = ' . (int) $pk);
				// Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
					->innerJoin('#__categories AS c on c.id = t4b.catid')
					->where('c.published > 0');

				// Join on user table.
				$query->select('u.name AS author')
					->join('LEFT', '#__users AS u on u.id = t4b.created_by');

				// Filter by language
				if ($this->getState('filter.language'))
				{
					$query->where('t4b.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}
				// Filter by published state.
				$published = $this->getState('filter.published');
				$unpublished = 0;
				$archived = $this->getState('filter.archived');
				if (is_numeric($published))
				{
					$query->where('(t4b.state = ' . (int) $published . ' OR t4b.state =' . (int) $archived . ' OR t4b.state =' . (int) $unpublished . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();
				
				if (empty($data))
				{
					return JError::raiseError(404, JText::_('COM_T4PAGEBUILDER_ERROR_PAGE_NOT_FOUND'));
				}
				// Check for published state if filter set.
				if ((is_numeric($published) || is_numeric($archived)) && (($data->state != $published) && ($data->state != $archived)))
				{
					return JError::raiseError(404, JText::_('COM_T4PAGEBUILDER_ERROR_PAGE_UNPUBLISHED'));
				}
				if(version_compare(JVERSION, '4', 'ge')){
					$dispatcher = JFactory::getApplication();
					$dispatcher->triggerEvent('onT4bLoadItem', array (&$data));

				}else{
					$dispatcher = JEventDispatcher::getInstance();
					$dispatcher->trigger('onT4bLoadItem', array (&$data));
				}
				// Convert parameter fields to objects. $data->attribs
				$registry = new Registry("");

				$data->params = clone $this->getState('params');
				$data->params->merge($registry);
				// $data->metadata = new Registry($data->metadata);

				// Technically guest could edit an article, but lets not check that to improve performance a little.
				if (!$user->get('guest'))
				{
					$userId = $user->get('id');
					$asset = 'com_t4pagebuilder.page.' . $data->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset))
					{
						$data->params->set('access-edit', true);
					}

					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
					{
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by)
						{
							$data->params->set('access-edit', true);
						}
					}
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else
				{
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					if ($data->catid == 0 || $data->category_access === null)
					{
						$data->params->set('access-view', in_array($data->access, $groups));
					}
					else
					{
						$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
					}
				}
				$this->_item[$pk] = $data;


			} catch (Exception $e) {
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}
	/**
	 * Increment the hit counter for the article.
	 *
	 * @param   integer  $pk  Optional primary key of the article to increment.
	 *
	 * @return  boolean  True if successful; false otherwise and internal error set.
	 */
	public function hit($pk = 0)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$pk = (!empty($pk)) ? $pk : (int) $this->getState('page.id');

			$table = JTable::getInstance('Page', 'T4pagebuilderTable');
			$table->load($pk);
			$table->hit($pk);
		}

		return true;
	}
	public function getT4 (){
		$tempate = JFactory::getApplication()->getTemplate();
		$return = false;
		if($tempate === 't4_blank'){
			$return = true;
		}
		return $return;
	}
}