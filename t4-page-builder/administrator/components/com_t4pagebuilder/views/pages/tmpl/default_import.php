<?php

/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:		  https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
?>
<div class="t4b-main-container">
	<?php echo JHtml::_('bootstrap.startTabSet', 'importTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'importTab', 'libs', 'From Library'); ?>
	<?php $this->setLayout('libraries'); ?>
	<?php echo $this->loadTemplate(); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'importTab', 'upload', 'Upload'); ?>
	<?php $this->setLayout('upload'); ?>
	<?php echo $this->loadTemplate(); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

</div>