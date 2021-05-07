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

// check if need separated layout for Joomla 3!
if (($j3 = \JPB\Helper\Layout::j3(__FILE__))) {
	include $j3;
	return;
}
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$app = Factory::getApplication();

if ($app->isClient('site'))
{
	Session::checkToken('get') or die(Text::_('JINVALID_TOKEN'));
}

HTMLHelper::_('behavior.core');

$extension = $this->escape($this->state->get('filter.extension'));
$function  = $app->input->getCmd('function', 'jSelectCategory');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
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
