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


// Initialise related data.
JLoader::register('MenusHelper', JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');
JLoader::register('ModulesHelper', JPATH_ADMINISTRATOR . '/components/com_modules/helpers/modules.php');
JFactory::getLanguage()->load('com_modules');
$menuTypes = MenusHelper::getMenuLinks();
$params = JComponentHelper::getParams('com_t4pagebuilder');
$bootstrap4 = $params->get('bootstrap4') ? $params->get('bootstrap4') : 0;
$bootstrap_assigned = $params->get('btAssigned') ? json_decode($params->get('btAssigned')) : [];

JHtml::_('script', 'jui/treeselectmenu.jquery.min.js', array('version' => 'auto', 'relative' => true));

$script = "
	jQuery(document).ready(function()
	{

		menuHide(jQuery('#jform_bootstrap4').val());
		jQuery('#jform_bootstrap4').change(function()
		{
			menuHide(jQuery(this).val());
		});
		jQuery('.jpb_btassigned').change(function(){
			btAssigned();
		});
		// extend joomla submit button
        /*var _submitform = Joomla.submitform;
        Joomla.submitform = function(task, form, validate) {
            // call before submit
            console.log(jQuery('#jform_loadfonts').val());
            _submitform(task, form, validate);
        }*/
	});
	function menuHide(val)
	{
		if (val == 0 || val == '-')
		{
			jQuery('#menuselect-group').hide();
		}
		else
		{
			jQuery('#menuselect-group').show();
			btAssigned();
		}
	}

	function btAssigned (){
		var btAssigned = [];
		jQuery('#jform_menuselect :input').each(function(){
			if(jQuery(this).prop('checked')){
				btAssigned.push(jQuery(this).val());
			}	
		});
		jQuery('#jform_btAssigned').val(JSON.stringify(btAssigned));
	}
";

// Add the script to the document head
JFactory::getDocument()->addScriptDeclaration($script);
?>
<div id="jform_loadbootstrap" class="controls">
	<select name="jform[bootstrap4]" id="jform_bootstrap4">
		<?php echo JHtml::_('select.options', ModulesHelper::getAssignmentOptions(0), 'value', 'text', $bootstrap4, true); ?>
	</select>
</div>
<div id="menuselect-group" class="control-group">
	<label id="jform_menuselect-lbl" class="control-label" for="jform_menuselect"><?php echo JText::_('JGLOBAL_MENU_SELECTION'); ?></label>

	<div id="jform_menuselect" class="controls">
		<?php if (!empty($menuTypes)) : ?>
		<?php $id = 'jform_menuselect'; ?>

		<div class="well well-small">
			<div class="form-inline">
				<span class="small"><?php echo JText::_('JSELECT'); ?>:
					<a id="treeCheckAll" href="javascript://"><?php echo JText::_('JALL'); ?></a>,
					<a id="treeUncheckAll" href="javascript://"><?php echo JText::_('JNONE'); ?></a>
				</span>
				<span class="width-20">|</span>
				<span class="small"><?php echo JText::_('COM_MODULES_EXPAND'); ?>:
					<a id="treeExpandAll" href="javascript://"><?php echo JText::_('JALL'); ?></a>,
					<a id="treeCollapseAll" href="javascript://"><?php echo JText::_('JNONE'); ?></a>
				</span>
				<input type="text" id="treeselectfilter" name="treeselectfilter" class="input-medium search-query pull-right" size="16"
					autocomplete="off" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" aria-invalid="false" tabindex="-1">
			</div>

			<div class="clearfix"></div>

			<hr class="hr-condensed" />

			<ul class="treeselect">
				<?php foreach ($menuTypes as &$type) : ?>
				<?php if (count($type->links)) : ?>
					<?php $prevlevel = 0; ?>
					<li>
						<div class="treeselect-item pull-left">
							<label class="pull-left nav-header"><?php echo $type->title; ?></label></div>
					<?php foreach ($type->links as $i => $link) : ?>
						<?php
						if ($prevlevel < $link->level)
						{
							echo '<ul class="treeselect-sub">';
						} elseif ($prevlevel > $link->level)
						{
							echo str_repeat('</li></ul>', $prevlevel - $link->level);
						} else {
							echo '</li>';
						}
						$selected = 0;
						if ($bootstrap4 == 0)
						{
							$selected = 1;
						} elseif ($bootstrap4 < 0)
						{
							$selected = in_array(-$link->value, $bootstrap_assigned);
						} elseif ($bootstrap4 > 0)
						{
							$selected = in_array($link->value, $bootstrap_assigned);
						}
						?>
							<li>
								<div class="treeselect-item pull-left">
									<?php
									$uselessMenuItem = in_array($link->type, array('separator', 'heading', 'alias', 'url'));
									?>
									<input type="checkbox" class="pull-left novalidate jpb_btassigned" name="jform[btAssigned][]" id="<?php echo $id . $link->value; ?>" value="<?php echo (int) $link->value; ?>"<?php echo $selected ? ' checked="checked"' : ''; echo $uselessMenuItem ? ' disabled="disabled"' : ''; ?> />
									<label for="<?php echo $id . $link->value; ?>" class="pull-left">
										<?php echo $link->text; ?> <span class="small"><?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($link->alias)); ?></span>
										<?php if (JLanguageMultilang::isEnabled() && $link->language != '' && $link->language != '*') : ?>
											<?php if ($link->language_image) : ?>
												<?php echo JHtml::_('image', 'mod_languages/' . $link->language_image . '.gif', $link->language_title, array('title' => $link->language_title), true); ?>
											<?php else : ?>
												<?php echo '<span class="label" title="' . $link->language_title . '">' . $link->language_sef . '</span>'; ?>
											<?php endif; ?>
										<?php endif; ?>
										<?php if ($link->published == 0) : ?>
											<?php echo ' <span class="label">' . JText::_('JUNPUBLISHED') . '</span>'; ?>
										<?php endif; ?>
										<?php if ($uselessMenuItem) : ?>
											<?php echo ' <span class="label">' . JText::_('COM_MODULES_MENU_ITEM_' . strtoupper($link->type)) . '</span>'; ?>
										<?php endif; ?>
									</label>
								</div>
						<?php

						if (!isset($type->links[$i + 1]))
						{
							echo str_repeat('</li></ul>', $link->level);
						}
						$prevlevel = $link->level;
						?>
						<?php endforeach; ?>
					</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<div id="noresultsfound" style="display:none;" class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
			<div style="display:none;" id="treeselectmenu">
				<div class="pull-left nav-hover treeselect-menu">
					<div class="btn-group">
						<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-micro">
							<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li class="nav-header"><?php echo JText::_('COM_MODULES_SUBITEMS'); ?></li>
							<li class="divider"></li>
							<li class=""><a class="checkall" href="javascript://"><span class="icon-checkbox" aria-hidden="true"></span> <?php echo JText::_('JSELECT'); ?></a>
							</li>
							<li><a class="uncheckall" href="javascript://"><span class="icon-checkbox-unchecked" aria-hidden="true"></span> <?php echo JText::_('COM_MODULES_DESELECT'); ?></a>
							</li>
							<div class="treeselect-menu-expand">
							<li class="divider"></li>
							<li><a class="expandall" href="javascript://"><span class="icon-plus" aria-hidden="true"></span> <?php echo JText::_('COM_MODULES_EXPAND'); ?></a></li>
							<li><a class="collapseall" href="javascript://"><span class="icon-minus" aria-hidden="true"></span> <?php echo JText::_('COM_MODULES_COLLAPSE'); ?></a></li>
							</div>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
