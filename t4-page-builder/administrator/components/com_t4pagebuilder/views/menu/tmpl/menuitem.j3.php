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

JHtml::_('behavior.core');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', '#jform_request_filter_tag', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$input = JFactory::getApplication()->input;
$id = $input->get('id',0);

JText::script('ERROR');
JText::script('JGLOBAL_VALIDATION_FORM_FAILED');

$assoc = JLanguageAssociations::isEnabled();

// Ajax for parent items
$script = "
jQuery(document).ready(function ($){
	if($('#jform_menuid').val() !== '') {
			$('#jform_menuid').prop('readonly', true).trigger('liszt:updated');
		}
		var id = ".(int)$this->item->id.";

		$('#jform_type').val('T4 Page Builder');
		$('#jform_link').val('index.php?option=com_t4pagebuilder&view=page&id='+id);
	$('#jform_menutype').change(function(){
		var menutype = $(this).val();
		$.ajax({
			url: 'index.php?option=com_menus&task=item.getParentItem&menutype=' + menutype,
			dataType: 'json'
		}).done(function(data) {
			$('#jform_parent_id option').each(function() {
				if ($(this).val() != '1') {
					$(this).remove();
				}
			});

			$.each(data, function (i, val) {
				var option = $('<option>');
				option.text(val.title).val(val.id);
				$('#jform_parent_id').append(option);
			});
			$('#jform_parent_id').trigger('liszt:updated');
		});
	});

});

Joomla.submitbutton = function(task, type){
	if (task == 'menu.cancel' || document.formvalidator.isValid(document.getElementById('item-form')))
	{
		var data = {};
		data.alias = jQuery('#item-form #jform_alias').val();
		data.tables = 'menu';
		var url_base = '".\JUri::base(true)."';
		var url = url_base +'/index.php?option=com_t4pagebuilder&view=page&format=json&act=alias';
		fetch(url,{
			method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
		})
		.then(function (response){
       		return response.json();
        })
        .then(function (result) {
			Joomla.submitform(task, document.getElementById('item-form'));
        	if(result.data){
        		window.parent.jQuery('#menuAdd" . (int) $this->item->id . "Modal').modal('hide');
        	}
        }).catch(function (err) {
            console.log(err)
        });
	}
	else
	{
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

$input = JFactory::getApplication()->input;

// Add the script to the document head.
JFactory::getDocument()->addScriptDeclaration($script);
// In case of modal
$isModal  = $input->get('layout') == 'modal' ? true : false;
$layout   = $isModal ? 'modal' : 'menuitem';
$tmpl     = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
$clientId = 0;
$lang     = JFactory::getLanguage()->getTag();
?>
<form action="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&view=menu&layout='.$layout.'&'.$tmpl.'&client_id=' . $clientId . '&id='.$this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_MENUS_ITEM_DETAILS')); ?>
		<div class="row-fluid">
			<div class="span9">
				<?php
				echo $this->form->renderField('type');
				echo $this->form->renderField('link');
				echo $this->form->renderField('browserNav');
				echo $this->form->renderField('template_style_id');
				?>
			</div>
			<div class="span3">
				<?php
				// Set main fields.
				$this->fields = array(
					'id',
					'client_id',
					'menutype',
					'parent_id',
					'published',
					'access',
				);
				echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>

	<input type="hidden" name="task" value="menu.menuitem" />
	<?php echo $this->form->getInput('component_id'); ?>
	<?php echo $this->form->getInput('menuid'); ?>
	<?php echo JHtml::_('form.token'); ?>
</form>