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

JHtml::_('behavior.core');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', '#jform_request_filter_tag', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$input = JFactory::getApplication()->input;
$id = $input->get('id',0);
use Joomla\Registry\Registry;
use JPB\Helper\Item AS Item;
// Create shortcut to parameters.
$params = clone $this->state->get('params');
$params->merge(new Registry($this->item));
$user = JFactory::getUser();
$userid = $user->id;
$content = $this->item->page_html;
$app = JFactory::getApplication();
$input = $app->input;

// Ajax for parent items
$script = "
	Joomla.submitbutton = function(task, type){
		if (task == 'menu.cancel' || document.formvalidator.isValid(document.getElementById('item-form')))
		{
			Joomla.submitform(task, document.getElementById('item-form'));
			window.parent.jQuery('#addNewPage').modal('hide');
		}
		else
		{
			console.log(jQuery('#item-form .modal-value.invalid'));
			// special case for modal popups validation response
			jQuery('#item-form .modal-value.invalid').each(function(){
				var field = jQuery(this),
					idReversed = field.attr('id').split('').reverse().join(''),
					separatorLocation = idReversed.indexOf('_'),
					nameId = '#' + idReversed.substr(separatorLocation).split('').reverse().join('') + 'name';
				jQuery(nameId).addClass('invalid');
			});
		}
		
	};
";


// Add the script to the document head.
JFactory::getDocument()->addScriptDeclaration($script);
//config view on page content
$name = 'pagetext';
$asset = $this->item->asset_id;
	// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));

?>
<form action="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="jpb-page-wrap">
		<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden"  name="id" value="">
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
