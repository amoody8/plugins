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

<?php // if ($this->countModules('masthead')) : ?>
	<!-- MASTHEAD -->
	<div class="t3-masthead col-sm-12<?php $this->_c('masthead') ?>">
			<jdoc:include type="modules" name="<?php $this->_p('masthead') ?>" />
      <?php if ($this->countModules('head-search')) : ?>
        <!-- MAST SEARCH -->
        <div class="mast-search <?php $this->_c('head-search') ?>">
         <jdoc:include type="modules" name="<?php $this->_p('head-search') ?>" style="raw" />
        </div>
        <!-- MAST SEARCH -->
      <?php endif ?>
	</div>
	<!-- //MASTHEAD -->
<?php // endif ?>