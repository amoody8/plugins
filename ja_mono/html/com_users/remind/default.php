<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2018 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
if(version_compare(JVERSION, '3.0', 'lt')){
	JHtml::_('behavior.tooltip');
}
JHtml::_('behavior.formvalidation');
?>
<div class="remind <?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form id="user-registration" class="form-inline" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="form-validate form-horizontal">

		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<fieldset>
				<?php if (isset($fieldset->label)) : ?>
					<div class="alert alert-warning"><?php echo JText::_($fieldset->label); ?></div>
				<?php endif; ?>
				<?php echo $this->form->renderFieldset($fieldset->name); ?>
			</fieldset>
		<?php endforeach; ?>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary validate"><?php echo JText::_('JSUBMIT'); ?></button>
						<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
