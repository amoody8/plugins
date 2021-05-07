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
use Joomla\Utilities\ArrayHelper;
/**
 * Articles list controller class.
 *
 * @since  1.6
 */
class T4pagebuilderControllerCategories extends JControllerAdmin
{
	/**
	 * Proxy for getModel
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_T4PAGEBUILDER_CATEGORY';
	
	public function getModel($name = 'Category', $prefix = 'T4pagebuilderModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return  boolean  False on failure or error, true on success.
	 *
	 * @since   1.6
	 */
	public function rebuild()
	{
		$this->checkToken();

		$extension = $this->input->get('extension');
		$this->setRedirect(JRoute::_('index.php?option=com_t4pagebuilder&view=categories&extension=' . $extension, false));

		/** @var CategoriesModelCategory $model */
		$model = $this->getModel();

		if ($model->rebuild())
		{
			// Rebuild succeeded.
			$this->setMessage(JText::_('COM_CATEGORIES_REBUILD_SUCCESS'));

			return true;
		}

		// Rebuild failed.
		$this->setMessage(JText::_('COM_CATEGORIES_REBUILD_FAILURE'));

		return false;
	}

	/**
	 * Save the manual order inputs from the categories list page.
	 *
	 * @return      boolean  True on success
	 *
	 * @since       1.6
	 * @see         JControllerAdmin::saveorder()
	 * @deprecated  4.0
	 */
	public function saveorder()
	{
		$this->checkToken();

		try
		{
			JLog::add(sprintf('%s() is deprecated. Function will be removed in 4.0.', __METHOD__), JLog::WARNING, 'deprecated');
		}
		catch (RuntimeException $exception)
		{
			// Informational log only
		}

		// Get the arrays from the Request
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder))
		{
			parent::saveorder();
		}
		else
		{
			// Nothing to reorder
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));

			return true;
		}
	}

	/**
	 * Deletes and returns correctly.
	 *
	 * @return  void
	 *
	 * @since   3.1.2
	 */
	public function delete()
	{
		$this->checkToken();

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');
		$extension = $this->input->getCmd('extension', null);

		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			/** @var CategoriesModelCategory $model */
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);
			$count = $this->_countPageOfCategory($cid[0]);
			$catTitle = $this->_getTitleCategory($cid[0]);
			// Show error if items are found in the category
			if ($count > 0)
			{
				$msg = JText::sprintf('COM_CATEGORIES_DELETE_NOT_ALLOWED', $catTitle)
					. JText::plural('COM_CATEGORIES_N_ITEMS_ASSIGNED', $count);
				JError::raiseWarning(403, $msg);
				
			}else{
				// Remove the items.
				if ($model->delete($cid))
				{
					$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
				}
				else
				{
					$this->setMessage($model->getError());
				}
			}
			
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=categories&extension='.$extension, false));
	}
		/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function publish()
	{
		// Check for request forgeries
		$this->checkToken();

		// Get items to publish from the request.
		$cid = $this->input->get('cid', array(), 'array');
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			\JLog::add(\JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), \JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);

			// Publish the items.
			try
			{
				$model->publish($cid, $value);
				$errors = $model->getErrors();
				$ntext = null;

				if ($value === 1)
				{
					if ($errors)
					{
						\JFactory::getApplication()->enqueueMessage(\JText::plural($this->text_prefix . '_N_ITEMS_FAILED_PUBLISHING', count($cid)), 'error');
					}
					else
					{
						$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
					}
				}
				elseif ($value === 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value === 2)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}

				if ($ntext !== null)
				{
					$this->setMessage(\JText::plural($ntext, count($cid)));
				}
			}
			catch (\Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$extension = $this->input->get('extension');
		$extensionURL = $extension ? '&extension=' . $extension : '';
		$this->setRedirect(\JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}

	/**
	 * Check in of one or more records.
	 *
	 * Overrides JControllerAdmin::checkin to redirect to URL with extension.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.6.0
	 */
	public function checkin()
	{
		// Process parent checkin method.
		$result = parent::checkin();

		// Override the redirect Uri.
		$redirectUri = 'index.php?option=' . $this->option . '&view=' . $this->view_list . '&extension=' . $this->input->get('extension', '', 'CMD');
		$this->setRedirect(JRoute::_($redirectUri, false), $this->message, $this->messageType);

		return $result;
	}
	public function _countPageOfCategory($catid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Count the items in this category
		$query->select('COUNT(id)')
			->from('#__jae_item')
			->where('catid = ' . $catid);
		$db->setQuery($query);
		try
		{
			$count = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());

			return false;
		}

		return $count;
	}
	public function _getTitleCategory($cid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Count the items in this category
		$query->select('title')
			->from('#__categories')
			->where('id = ' . $cid);
		$db->setQuery($query);
		try
		{
			$count = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());

			return false;
		}

		return $count;
	}
}
?>