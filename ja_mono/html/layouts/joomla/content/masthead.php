<?php
/*
 * ------------------------------------------------------------------------
 * JA Mono Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
$params = $displayData['params'];
$item = $displayData['item'];
$topInfo = $displayData['topInfo'];
$icons = $displayData['icons'];
$view = $displayData['view'];
$imageBg = $displayData['imageBg'];
$event = $displayData['event'];
?>
<div class="jamasthead blend-bg" style="background-image:url(<?php echo $imageBg ?>);">
    <div class="jamasthead-detail">
        <?php if ($params->get('show_title')) : ?>
        <?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $item, 'params' => $params, 'title-tag'=>'h1')); ?>
        <?php endif; ?>

        <?php echo $item->event->afterDisplayTitle; ?>
        <!-- Aside -->
        <?php if ($topInfo || $icons) : ?>
        <aside class="article-aside clearfix">
            <?php if ($topInfo): ?>
            <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $item, 'params' => $params, 'position' => 'above')); ?>
            <?php endif; ?>
            <?php if ($icons): ?>
            <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $item, 'params' => $params, 'print' => isset($view->print) ? $view->print : null)); ?>
            <?php endif; ?>
        </aside>
        <?php endif; ?>

        <!-- //Aside -->
        <?php if (!empty($item->pagination) && $item->pagination && $item->paginationposition && $item->paginationrelative):
        echo $item->pagination;
        endif; ?>
    </div>
</div>