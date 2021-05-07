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
use Joomla\String\Inflector;

HTMLHelper::_('behavior.multiselect');
$app       = JFactory::getApplication();
$user      = JFactory::getUser();
$userId    = $user->get('id');
$uri       = JUri::getInstance();
$return    = base64_encode($uri);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$menuitems = $this->menuitems;
$maxSize = JFilesystemHelper::fileUploadMaxSize();

// Drag and Drop installation scripts
$token = JSession::getFormToken();
$return = JFactory::getApplication()->input->getBase64('return');
$saveOrder = $listOrder == 'a.ordering';
$columns   = 10;

if (strpos($listOrder, 'modified') !== false) {
    $orderingColumn = 'modified';
} else {
    $orderingColumn = 'created';
}

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_t4pagebuilder&task=pages.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}

$script = array();
$script[] = 'function getUrlBase(){
	return "'.\JUri::base(true).'";
}';
$script[] = 'jQuery(document).ready(function($) {';
$script[] = '	$("#jpb-main-container").prepend($("#system-message-container"));';
$script[] = '	$("#addnewpage").addClass("t4b-modal w-50 h-50");';
$script[] = '	$("#importpage").addClass("t4b-modal w-100");';
$script[] = '	$(".modal").not("#addnewpage").on("hidden", function () {';
$script[] = '		if($(this).data("act") == "cancel") return;';
$script[] = '			window.parent.location.reload();';
$script[] = '		setTimeout(function(){';
$script[] = '			window.parent.location.reload();';
$script[] = '		},1000);';
$script[] = '	});';
$script[] = '	$(".t4b-modal").on("shown.bs.modal", function () {';
$script[] = "		if($(this).is(':hidden')) return;
					if(this.getAttribute('id') == 'addnewpage'){";
$script[] = "			$('#addnewpage .modal-body').prepend($('#system-message-container'));";
$script[] = "			Joomla.removeMessages();";
$script[] = "		}";
$script[] = "		if(this.getAttribute('id') == 'importpage'){";
$script[] = "			if($(this).is(':hidden')) return;";
$script[] = "			$('#importpage #upload').prepend($('#system-message-container'));";
$script[] = "			Joomla.removeMessages();";
$script[] = "		}";
$script[] = '	});';
$script[] = "	$('.btn-create-newpage').on('click',function(){";
$script[] = "		if ($('.jpb-page-wrap #title').val()){";
$script[] = "			var title = $('.jpb-page-wrap #title').val(),alias = $('.jpb-page-wrap #alias').val() ? $('#adnewform #alias').val() : '',data = {};";
$script[] = '			data.title = title;';
$script[] = '			data.alias = alias;';
$script[] = '			data.id = 0;';
$script[] = '			data.created_by = '.$userId.';';
$script[] = '			var url = url_base + "/index.php?option=com_t4pagebuilder&view=page&format=json&act=create";';
$script[] = '			fetch(url,{
							method: "POST",
                			body: JSON.stringify(data),
                			headers: {
                    			"Content-Type": "application/json"
                			}
						})
			        	.then(function (response){
			           		return response.json();
			            })
			            .then(function (data) {
			            	if(data.newId){
			            		window.location.href = url_base + "/index.php?option=com_t4pagebuilder&task=page.edit&id="+data.newId;
			            	}else{
				           		Joomla.renderMessages({"error":[data.error]});
				           		return false;
			           		}
			            }).catch(function (err) {
			                console.log(err)
			            });';
$script[] = "		}
					else
					{
						Joomla.renderMessages({error:['invalid: title']});
						$('.jpb-page-wrap #title').closest('.controls').addClass('has-danger');
						$('.jpb-page-wrap #title').addClass('form-control-danger invalid');
						$('.jpb-page-wrap #title-lbl').addClass('invalid');
						$('.jpb-page-wrap #title').attr('aria-invalid', 'true');
					}
				});";
$script[] = "	if(localStorage.getItem('importOpened') == 'true'){";
$script[] = "		$('#toolbar-Import button').trigger('click');";
$script[] = "		localStorage.setItem('importOpened', false);";
$script[] = "	}";
$script[] = "	
		var maxSize = '".$maxSize."';
	if(!$('body').hasClass('view-pages')) $('body').addClass('view-pages');
	t4b.updateBtn($('#toolbar'),JSON.parse(localStorage.getItem('t4bConfig')));";
$script[] = '});';
$this->document->addScript(JPB_PATH_BASE . 'assets/js/t4pagebuilder.js');
$this->document->addScriptDeclaration(implode("\n", $script));
$this->document->addStylesheet(\JUri::root(true). JPB_MEDIA_BUILDER . 'css/fontawesome_light.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&view=pages'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<?php if (!empty($this->sidebar)) : ?>
				<div id="jpb-sidebar" class="col-2">
					<?php echo $this->sidebar; ?>
				</div>	
				<div id="jpb-main-container" class="j-main-container">
			<?php else : ?>
				<div id="jpb-main-container" class="j-main-container">
			<?php endif; ?>
			<?php
            // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>
				
			<?php if (empty($this->items)) : ?>
				<div class="alert alert-info">
					<span class="fas fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php else : ?>
				<table class="table table-striped table-hover" id="page-list">
					<?php if (version_compare(JVERSION, '4', 'ge')):?>
						<caption id="captionTable" class="sr-only">
							<?php echo JText::_('COM_T4PAGEBUILDER_TABLE_CAPTION'); ?>,
							<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
							<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
						</caption>
					<?php endif;?>
					<thead>
						<tr>
							<th scope="col" class="w-1 text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'fas fa-sort'); ?>
							</th>
							<td class="w-1 text-center">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
							</td>
							<th scope="col" class="w-1 text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
							</th>
							<th style="min-width:100px;padding-left: 2.6%;" class="nowrap">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
							</th>
							
							<th width="10%" class="nowrap hidden-phone">
								<?php echo JText::_('Link Menu Item'); ?>
							</th>
							<th width="10%" class="nowrap hidden-phone">
								<?php echo HTMLHelper::_('searchtools.sort', 'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
							</th>
							<th width="10%" class="nowrap hidden-phone">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
							</th>
							<th width="1%" class="nowrap hidden-phone">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
							</th>
							<th width="1%" class="nowrap hidden-phone">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="<?php echo $columns; ?>">
								<?php echo $this->pagination->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>

					<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
							<?php foreach ($this->items as $i => $item) :
                                $item->max_ordering = 0;
                                $ordering   = ($listOrder == 'a.ordering');
                                $canEdit    = $user->authorise('core.edit', 'com_t4pagebuilder.page.' . $item->id);
                                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                $canEditOwn = $user->authorise('core.edit.own', 'com_t4pagebuilder.page.' . $item->id) && '42' == $userId;
                                $canChange  = $user->authorise('core.edit.state', 'com_t4pagebuilder.page.' . $item->id) && $canCheckin;
                                $canCreate  = $user->authorise('core.create', 'com_t4pagebuilder.category.' . $item->catid);
                                $canEditCat    = $user->authorise('core.edit', 'com_t4pagebuilder.category.' . $item->catid);
                                $canEditOwnCat = $user->authorise('core.edit.own', 'com_t4pagebuilder.category.' . $item->catid) && $item->category_uid == $userId;
                                $canEditParCat    = $user->authorise('core.edit', 'com_t4pagebuilder.category.' . $item->parent_category_id);
                                $canEditOwnParCat = $user->authorise('core.edit.own', 'com_t4pagebuilder.category.' . $item->parent_category_id) && $item->parent_category_uid == $userId;

                                ?>
								<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>" >
									<td class="text-center d-none d-md-table-cell">
										<?php
                                        $iconClass = '';
                                        if (!$canChange) {
                                            $iconClass = ' inactive';
                                        } elseif (!$saveOrder) {
                                            $iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
                                        }
                                        ?>
										<span class="sortable-handler<?php echo $iconClass ?>">
											<span class="fas fa-ellipsis-v" aria-hidden="true"></span>
										</span>
										<?php if ($canChange && $saveOrder) : ?>
											<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden">
										<?php endif; ?>
									</td>
									<td class="text-center">
										<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
									</td>
									<td class="text-center">
										<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'pages.', $canChange); ?>
									</td>
									<td class="has-context">
										<!-- Preview button -->	
										<a class="t4b-preview-alt" href="#" data-pageid="<?php echo $item->id;?>" data-url="<?php echo JRoute::_(JURI::root().'index.php?option=com_t4pagebuilder&view=page&tmpl=component&id='.$item->id.'&preview=1', false);?>" title="Preview" data-pagetitle="<?php echo $item->title;?>">Preview</a>
										<!-- // Preview button -->

										<div class="pull-left break-word">
											<?php if ($canEdit || $canEditOwn) : ?>
												<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&task=page.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
													<?php echo $this->escape($item->title); ?></a>
											<?php else : ?>
												<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
											<?php endif; ?>
											<span class="small break-word">
												<?php if (empty($item->note)) : ?>
													<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
												<?php else : ?>
													<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note)); ?>
												<?php endif; ?>
											</span>
											<div class="small">
												<?php
                                                $ParentCatUrl = JRoute::_('index.php?option=com_t4pagebuilder&task=category.edit&id=' . $item->parent_category_id . '&extension=com_t4pagebuilder');
                                                $CurrentCatUrl = JRoute::_('index.php?option=com_t4pagebuilder&task=category.edit&id=' . $item->catid . '&extension=com_t4pagebuilder');
                                                $EditCatTxt = JText::_('COM_T4PAGEBUILDER_EDIT_CATEGORY');

                                                    echo JText::_('JCATEGORY') . ': ';

                                                    if ($item->category_level != '1') :
                                                        if ($item->parent_category_level != '1') :
                                                            echo ' &#187; ';
                                                        endif;
                                                    endif;

                                                    if (JFactory::getLanguage()->isRtl()) {
                                                        if ($canEditCat || $canEditOwnCat) :
                                                            echo '<a class="hasTooltip" href="' . $CurrentCatUrl . '" title="' . $EditCatTxt . '">';
                                                        endif;
                                                        echo $this->escape($item->category_title);
                                                        if ($canEditCat || $canEditOwnCat) :
                                                            echo '</a>';
                                                        endif;

                                                        if ($item->category_level != '1') :
                                                            echo ' &#171; ';
                                                        if ($canEditParCat || $canEditOwnParCat) :
                                                                echo '<a class="hasTooltip" href="' . $ParentCatUrl . '" title="' . $EditCatTxt . '">';
                                                        endif;
                                                        echo $this->escape($item->parent_category_title);
                                                        if ($canEditParCat || $canEditOwnParCat) :
                                                                echo '</a>';
                                                        endif;
                                                        endif;
                                                    } else {
                                                        if ($item->category_level != '1') :
                                                            if ($canEditParCat || $canEditOwnParCat) :
                                                                echo '<a class="hasTooltip" href="' . $ParentCatUrl . '" title="' . $EditCatTxt . '">';
                                                        endif;
                                                        echo $this->escape($item->parent_category_title);
                                                        if ($canEditParCat || $canEditOwnParCat) :
                                                                echo '</a>';
                                                        endif;
                                                        echo ' &#187; ';
                                                        endif;
                                                        if ($canEditCat || $canEditOwnCat) :
                                                            echo '<a class="hasTooltip" href="' . $CurrentCatUrl . '" title="' . $EditCatTxt . '">';
                                                        endif;
                                                        echo $this->escape($item->category_title);
                                                        if ($canEditCat || $canEditOwnCat) :
                                                            echo '</a>';
                                                        endif;
                                                    }
                                                ?>
											</div>
										</div>
									</td>
									<td class="center">
										<div class="btn-group">
										<?php $link_pagebuilder = "index.php?option=com_t4pagebuilder&view=page&id=".$item->id; ?>
										<?php if (isset($this->menuitems[$link_pagebuilder])) : ?>
											<?php
                                            $menuitem = $this->menuitems[$link_pagebuilder];
                                            $link_menu = JRoute::_('index.php?option=com_menus&view=item&menutype='.$menuitem->menutype.'&client_id=0&layout=edit&id=' . $menuitem->id. '&return=' . $return);
                                        ?>
												<button type="button" class="btn btn-small">
													<a href="<?php echo $link_menu; ?>" target="_Blank"><?php echo JText::_('COM_T4PAGEBUILDER_MENU_ITEM_EDIT'); ?></a>
												</button>
												
										<?php elseif ($item->id) : ?>
											<?php $link_add = JRoute::_('index.php?option=com_t4pagebuilder&view=menu&id='.$item->id.'&tmpl=component&layout=modal&client_id=0'); ?>
											<button type="button" class="btn btn-small btn-primary" data-toggle="modal" data-target="#menuAdd<?php echo $item->id;?>Modal"><?php echo JText::_('COM_T4PAGEBUILDER_ADD_MENU_ITEM'); ?></button>
											<?php echo HTMLHelper::_(
                                            'bootstrap.renderModal',
                                            'menuAdd'.$item->id.'Modal',
                                            array(
                                                        'title'       => JText::_('COM_T4PAGEBUILDER_ADD_MENU_ITEM'),
                                                        'backdrop'    => 'static',
                                                        'keyboard'    => false,
                                                        'closeButton' => false,
                                                        'url'         => $link_add,
                                                        'height'      => '400px',
                                                        'width'       => '800px',
                                                        'bodyHeight'  => '70',
                                                        'modalWidth'  => '80',
                                                        'footer'      => '<button type="button" class="btn" data-dismiss="modal"'
                                                                . ' onclick="jQuery(\'#menuAdd'.$item->id.'Modal iframe\').contents().find(\'#closeBtn\').click(); jQuery(\'#menuAdd'.$item->id.'Modal\').data(\'act\',\'cancel\');">'
                                                                . JText::_('JCANCEL') . '</button>'
                                                                . '<button type="button" class="btn btn-primary"'
                                                                . ' onclick="jQuery(\'#menuAdd'.$item->id.'Modal iframe\').contents().find(\'#saveBtn\').click();">'
                                                                . JText::_('Create New Menu') . '</button>',
                                                    )
                                        ); ?>
										<?php endif; ?>
										</div>
										
									</td>
									<td class="small hidden-phone">
										<?php if ((int) $item->created_by != 0) : ?>
											<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>" title="<?php echo JText::_('JAUTHOR'); ?>">
												<?php echo $this->escape($item->author_name); ?></a>
										<?php else : ?>
											<?php echo JText::_('JNONE'); ?>
										<?php endif; ?>
									</td>
									<td class="small hidden-phone">
										<?php echo $this->escape($item->access_level); ?>
									</td>
									<td class="hidden-phone center">
										<span class="badge badge-info">
											<?php echo (int) $item->hits; ?>
										</span>
									</td>
									<td class="hidden-phone">
										<?php echo (int) $item->id; ?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

						<?php // load the pagination.?>
						<?php echo $this->pagination->getListFooter(); ?>
					<?php endif; ?>

					<input type="hidden" name="task" value="">
					<input type="hidden" name="boxchecked" value="0">
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
		</div>
	</div>
</form>


<?php
// update t4b info
$info = t4pagebuilderHelper::compare_version();
echo $info;

?>
<?php echo HTMLHelper::_(
    'bootstrap.renderModal',
    'addnewpage',
    array(
        'title'  => Text::_('COM_T4PAGEBUILDER_CREATE_NEW_PAGE'),
        'footer' => $this->loadTemplate('new_footer'),
    ),
    $this->loadTemplate('new_page')
); ?>


<?php echo HTMLHelper::_(
    'bootstrap.renderModal',
    'importpage',
    array(
        'title'  => Text::_('COM_T4PAGEBUILDER_IMPORT_PAGE'),
        'bodyHeight'  => '70',
        'modalWidth'  => '80',
    ),
    $this->loadTemplate('import')
);

echo JHtml::_(
    'bootstrap.renderModal',
    'preview',
    array(
        'bodyHeight'  => '70',
        'modalWidth'  => '80',
    ),
    $this->loadTemplate('preview')
);


?>