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

JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));
JFactory::getLanguage()->load('com_menus');

// @deprecated 4.0 the function parameter, the inline js and the buttons are not needed since 3.7.0.
$function  = JFactory::getApplication()->input->getCmd('function', 'menuAdd'.$this->item->id.'Modal');

// Function to update input title when changed
JFactory::getDocument()->addScriptDeclaration('
	function jEditMenuModal() {
		if (window.parent && document.formvalidator.isValid(document.getElementById("item-form"))) {
			return window.parent.' . $this->escape($function) . '(document.getElementById("jform_title").value);
		}
	}
');
?>
<button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('menu.apply');"></button>
<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('menu.save');"></button>
<button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('menu.cancel');"></button>

<div class="container-popup">
	<?php $this->setLayout('menuitem'); ?>
	<?php echo $this->loadTemplate(); ?>
</div>
