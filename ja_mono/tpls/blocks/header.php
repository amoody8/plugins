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

<!-- HEADER -->
<header id="t3-header" class="t3-header <?php if (!$this->getParam('responsive', 1)) echo 'no-responsive container'; ?>">

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

		<?php if ($this->getParam('addon_offcanvas_enable')) : ?>
			<?php $this->loadBlock ('off-canvas') ?>
		<?php endif ?>

		<?php if ($this->countModules('sidebar')) : ?>
			<?php $this->loadBlock ('sidebar') ?>
		<?php endif ?>

		<?php if ($this->countModules('head-search')) : ?>
			<!-- HEAD SEARCH -->
			<div class="head-search <?php $this->_c('head-search') ?>">
				<button class="btn-search"><i class="fa fa-search"></i></button>
			</div>
			<!-- //HEAD SEARCH -->
		<?php endif ?>

</header>
<!-- //HEADER -->