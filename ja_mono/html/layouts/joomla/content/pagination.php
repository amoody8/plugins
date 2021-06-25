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
$pagination = $displayData['pagination'];

$show_option = $params->def('show_pagination', 2);
$pagination_type = $params->get('pagination_type');
?>

<?php if ($show_option == 1 || ($show_option == 2 && $pagination->get('pages.total') > 1)) : ?>
  <div class="pagination">
    <?php if ($params->def('show_pagination_results', 1)) : ?>
    <p class="counter pull-right">
        <?php echo $pagination->getPagesCounter(); ?>
    </p>
    <?php  endif; ?>
    <?php echo $pagination->getPagesLinks(); ?>
  </div>
<?php endif ?>
<?php if ($show_option && $pagination_type > 0) : ?>
<?php JFactory::getDocument()->addScript (T3_TEMPLATE_URL . '/js/infinitive-paging.js'); ?>
<?php JFactory::getDocument()->addScript (T3_TEMPLATE_URL . '/js/jquery.infinitescroll.js');?>
<?php   echo JLayoutHelper::render('joomla.content.pagination-infinitive', $displayData); ?>
<?php endif ?>