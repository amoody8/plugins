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

<div class="block-invisible" data-ajax-block="block-invisible">
	<jdoc:include type="modules" name="<?php $this->_p('block-invisible') ?>" style="raw" />
	<meta name="page-title" content="<?php echo $this->getTitle() ?>" />
	<meta name="page-class" content='<jdoc:include type="pageclass" />' />
</div>

<?php if ($this->countModules('languageswitcherload')) : ?>
<!-- LANGUAGE SWITCHER -->
<div class="languageswitcherload hidden" data-ajax-block="block-language-switcher">
<jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
</div>
<!-- //LANGUAGE SWITCHER -->
<?php endif ?>