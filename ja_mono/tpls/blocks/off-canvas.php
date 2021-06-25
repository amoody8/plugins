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

<button class="btn btn-menu off-canvas-toggle <?php $this->_c('off-canvas') ?>" type="button" data-pos="left" data-nav="#t3-off-canvas" data-effect="<?php echo $this->getParam('addon_offcanvas_effect', 'off-canvas-effect-4') ?>">
  <span class="patty"></span>
</button>

<!-- OFF-CANVAS SIDEBAR -->
<div id="t3-off-canvas" class="t3-off-canvas <?php $this->_c('off-canvas') ?>">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
    <span class="patty"></span>
  </button>
  <div class="t3-off-canvas-body">
    <!-- LOGO -->
    <div class="logo">
      <div class="logo-<?php echo $logotype, ($logoimgsm ? ' logo-control' : '') ?>">
        <a href="<?php echo JURI::base(true) ?>" title="<?php echo strip_tags($sitename) ?>">
          <?php if($logotype == 'image'): ?>
            <img class="logo-img" src="<?php echo JURI::base(true) . '/' . $logoimage ?>" alt="<?php echo strip_tags($sitename) ?>" />
          <?php endif ?>
          <?php if($logoimgsm) : ?>
            <img class="logo-img-sm" src="<?php echo JURI::base(true) . '/' . $logoimgsm ?>" alt="<?php echo strip_tags($sitename) ?>" />
          <?php endif ?>
          <span><?php echo $sitename ?></span>
        </a>
        <small class="site-slogan"><?php echo $slogan ?></small>
      </div>
    </div>
    <!-- //LOGO -->

    <?php if ($this->countModules('languageswitcherload')) : ?>
      <!-- LANGUAGE SWITCHER -->
      <div class="languageswitcherload" data-ajax-block="block-language-switcher">
        <jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
      </div>
      <!-- //LANGUAGE SWITCHER -->
    <?php endif ?>
    
    <div class="table-cell">
      <jdoc:include type="modules" name="<?php $this->_p('off-canvas') ?>" style="T3Xhtml" />
    </div>
  </div>
</div>
<!-- //OFF-CANVAS SIDEBAR -->