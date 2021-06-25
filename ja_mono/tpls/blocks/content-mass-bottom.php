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

<?php if ($this->countModules('content-mass-bottom')) : ?>
  <!-- Content Mass Bottom -->
  <div class="content-mass content-mass-bottom col-xs-12<?php $this->_c('content-mass-bottom') ?>">
    <jdoc:include type="modules" name="<?php $this->_p('content-mass-bottom') ?>" style="T3xhtml" />
  </div>
  <!-- //Content Mass Bottom -->
<?php endif ?>