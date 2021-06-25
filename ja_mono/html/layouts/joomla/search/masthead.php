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
?>
<div class="jamasthead blend-bg search" >
    <div class="jamasthead-detail">
      <?php if ($params->get('show_page_heading', 1)) : ?>
        <h3 class="jamasthead-title">
          <?php if ($item->escape($params->get('page_heading'))) : ?>
            <?php echo $item->escape($params->get('page_heading')); ?>
          <?php else : ?>
            <?php echo $item->escape($params->get('page_title')); ?>
          <?php endif; ?>
        </h3>
      <?php endif; ?>

      <?php echo $item->loadTemplate('form'); ?>
    </div>
</div>