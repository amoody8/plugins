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

// get params
$sitename  = $this->params->get('sitename');
$slogan    = $this->params->get('slogan', '');
$logotype  = $this->params->get('logotype', 'text');
$logoimage = $logotype == 'image' ? $this->params->get('logoimage', T3Path::getUrl('images/logo.png', '', true)) : '';
$logoimgsm = ($logotype == 'image' && $this->params->get('enable_logoimage_sm', 0)) ? $this->params->get('logoimage_sm', T3Path::getUrl('images/logo-sm.png', '', true)) : false;

if (!$sitename) {
  $sitename = JFactory::getConfig()->get('sitename');
}

?>
<?php
	if (!$this->getParam('addon_offcanvas_enable')) return ;
?>

<!-- SIDEBAR BTN -->
<div class="head-sidebar <?php $this->_c('sidebar') ?>">
  <button class="btn btn-sidebar off-canvas-toggle <?php $this->_c('off-canvas') ?>" type="button" data-pos="left" data-nav="#t3-off-canvas-sidebar" data-effect="<?php echo $this->getParam('addon_offcanvas_effect', 'off-canvas-effect-4') ?>">
    <span class="patty"></span>
  </button>
</div>
<!-- //SIDEBAR BTN -->

<!-- OFF-CANVAS SIDEBAR -->
<div id="t3-off-canvas-sidebar" class="t3-off-canvas <?php $this->_c('sidebar') ?>">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
    <span class="patty"></span>
  </button>
  <div class="t3-off-canvas-body">
    <jdoc:include type="modules" name="<?php $this->_p('sidebar') ?>" style="T3Xhtml" />
  </div>
</div>
<!-- //OFF-CANVAS SIDEBAR -->