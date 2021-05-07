<?php

/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:        https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */
defined('_JEXEC') or die;
 
JHtml::_('behavior.core');
JHtml::_('behavior.formvalidator');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$items = $this->items;

// Search tools bar
// echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
?>
<div class="container-popup">
    <div class="toolbar btn-group pull-right">
        <button class="btn btn-small button-trash" onclick="if (document.adminForm.boxchecked.value == 0) { alert('Please first make a selection from the list.');console.log(Joomla); } else { Joomla.submitbutton('revisions.delRevision'); }">Delete</button>
        <button class="btn btn-small button-trash" onclick="if(confirm('Are you sure?') == true){ Joomla.submitbutton('revisions.removeAllRev'); }">Delete All</button>
    </div>
    <div class="clearfix"></div>
    <hr class="hr-condensed">
    <form action="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&view=revisions&id='.$this->pageId.'&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
        
    	<div class="revision">
        	<div class="t4b-revision-container">
		        <?php if (empty($this->items)) : ?>
		        <div class="alert alert-no-items">
		            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		        </div>
		        <?php else : ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="1%" class="center">
                                <?php echo JHtml::_('grid.checkall'); ?>
                            </th>
                            <th class="center">Revision Title</th>
                            <th class="center">Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $i => $item):?>
                        <tr class="row<?php echo $i % 2; ?>">
                            <td class="center">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center">
                                <?php echo $item->title ? $item->title : "Noname";?>
                            </td>
                            <td class="center">
                                <?php echo $item->ctime;?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif;?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>