<?php
/**
 * ------------------------------------------------------------------------
 * JA Mono Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

/**
 * Mainbody 1 columns, content only
 */

if (is_array($this->getParam('skip_component_content')) && 
    in_array(JFactory::getApplication()->input->getInt('Itemid'), $this->getParam('skip_component_content'))) 
  return;
?>

<div id="t3-mainbody" class="t3-mainbody col-xs-12 col-sm-12">
  <div class="row">
    <!-- MAIN CONTENT -->
    <div id="t3-content" class="t3-content col-xs-11 col-sm-8">
      <?php if($this->hasMessage()) : ?>
      <jdoc:include type="message" />
      <?php endif ?>
      <jdoc:include type="component" />
    </div>
    <!-- //MAIN CONTENT -->
  </div>
</div> 