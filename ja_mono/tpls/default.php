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
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>"
	  class='<jdoc:include type="pageclass" />'>

<head>
	<jdoc:include type="head" />
	<?php $this->loadBlock('head') ?>
  <?php $this->addCss('layouts/docs') ?>
</head>

<body>

<div class="t3-wrapper"> <!-- Need this wrapper for off-canvas menu. Remove if you don't use of-canvas -->

  <?php $this->loadBlock('header') ?>
  
  <?php if ($this->getParam('addon_ajax_enabled', 1)): ?>
  <div class="load-indicator"><div class="load-indicator-progress"></div></div>
  <?php endif ?>
  
  <div class="two-blocks-wrapper <?php if (!$this->getParam('responsive', 1)) echo 'no-responsive container'; ?>">
    <div class="block-left col-xs-12 col-lg-6">
      <?php $this->loadBlock('block-left', array('AJAX-BLOCK')) ?>
    </div>

    <div class="block-right col-xs-12 col-lg-6 col-lg-offset-6">
      <?php $this->loadBlock('block-right', array('AJAX-BLOCK')) ?>
    </div>

    <div class="block-footer col-xs-12 col-lg-6 col-lg-offset-6">
      <?php $this->loadBlock('footer') ?>
    </div>
  </div>
</div>

<?php $this->loadBlock('block-invisible', array('AJAX-BLOCK')) ?>

</body>

</html>