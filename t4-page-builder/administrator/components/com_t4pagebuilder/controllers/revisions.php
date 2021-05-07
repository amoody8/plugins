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
class T4pagebuilderControllerRevisions extends JControllerAdmin
{

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  JModelLegacy
	 *
	 * @since   1.6
	 */
	function display ($cachable = false, $urlparams = false)
	{
		$this->input->set('view', 'revisions');
		parent::display();
	}

	public function getModel($name = 'revisions', $prefix = 'T4pagebuilderModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
	public function delRevision(){

		$this->checkToken();

		// Get items to remove from the request.
		$cid = $this->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_('COM_T4PAGEBUILDER_NO_ITEM_REVISION_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(JText::plural('COM_T4PAGEBUILDER_N_ITEMS_REVISION_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_t4pagebuilder&view=revisions&tmpl=component&id='. $this->input->getInt('id') , false
			)
		);

	}
	public function removeAllRev() {
		// Get items to remove from the request.
		$page_id = $this->input->getInt('id');
		// Get the model.
		$model = $this->getModel();

		// Remove the items.
		if ($model->deleteAll($page_id))
		{
			$this->setMessage(JText::_('COM_T4PAGEBUILDER_ALL_ITEMS_REVISION_DELETED'));
		}
		else
		{
			$this->setMessage($model->getError());
		}
		
		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_t4pagebuilder&view=revisions&tmpl=component&id='. $this->input->getInt('id') , false
			)
		);
	}
}
?>