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

$app = JFactory::getApplication();

if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

JHtml::_('behavior.core');
JHtml::_('behavior.polyfill', array('event'), 'lt IE 9');
JHtml::_('script', 'com_content/admin-articles-modal.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));
JHtml::_('bootstrap.popover', '.hasPopover', array('placement' => 'bottom'));
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
JHtml::_('formbehavior.chosen', '.multipleCategories', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_CATEGORY')));
JHtml::_('formbehavior.chosen', '.multipleAccessLevels', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_ACCESS')));
JHtml::_('formbehavior.chosen', '.multipleAuthors', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_AUTHOR')));
JHtml::_('formbehavior.chosen', 'select');

// Special case for the search field tooltip.
$searchFilterDesc = $this->filterForm->getFieldAttribute('search', 'description', null, 'filter');
JHtml::_('bootstrap.tooltip', '#filter_search', array('title' => JText::_($searchFilterDesc), 'placement' => 'bottom'));

$function  = $app->input->getCmd('function', 'jSelectArticle');

$items = $this->items;
?>

<form action="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&view=pages&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>" 
	  method="post" name="adminForm" id="adminForm">
	<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	<div class="clearfix"></div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
	
	<table class="table table-striped" id="">
		<thead>
				<tr>
					<th width="5%" class="">
						Status
					</th>
					<th width="50%" class="">
						Title
					</th>
					<th width="40%">
						Alias
					</th>
					<th width="2%" class="">
						ID
					</th>
				</tr>
		</thead>

		<tfoot>
		<tr>
			<td colspan="5">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		
		<tbody>
			
		<?php foreach($items as $i => $item ):	
			?>
			
			<tr class="sortable-group-id">
				<td>
					<a class="btn btn-micro disabled hasTooltip" href="javascript:void(0);" title="">
						<span class="icon-<?php echo $item->state ? 'publish':'unpublish' ?>"></span>
					</a>
				
				</td>
				<td>
					<a href="javascript:void(0);" title="Edit page"
					   accesskey=""onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '', null, '', '', null);" >								
						<?php echo $item->title; ?>
					</a>
					<span class="small">( alias: <?php echo $item->alias ?>)</span>
				</td>
				<td><?php echo $item->alias ?> </td>
				<td><?php echo $item->id ?></td>
			</tr>
			
		<?php endforeach; ?>
			
		</tbody>
		
    </table>
	
	<?php endif; ?>
			
	<input type="hidden" name="task" value=""/>	
	<input type="hidden" name="boxchecked" value="0"/>	
	<?php echo JHtml::_('form.token'); ?>
</form>
