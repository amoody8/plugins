<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

$regex = '@class="([^"]*)"@';
$lbreg = '@" class="([^"]*)"@';
$label = 'class="$1 control-label"';
$input = 'class="$1 form-control"';

if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>

<div class="contact-form">
	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate form-horizontal">
		<fieldset>
			<legend class="hide"><?php echo JText::_('COM_CONTACT_FORM_LABEL'); ?></legend>
			<div class="form-group">
				<div class="col-md-6 contact-name">
					<?php echo preg_replace($regex, $label, $this->form->getLabel('contact_name'), 1); ?>
					<?php echo preg_replace($regex, $input, $this->form->getInput('contact_name')); ?>
				</div>
				<div class="col-md-6 contact-email">
					<?php echo preg_replace($regex, $label, $this->form->getLabel('contact_email'), 1); ?>
					<?php echo preg_replace($regex, $input, $this->form->getInput('contact_email')); ?>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-12">
					<?php echo preg_replace($regex, $label, $this->form->getLabel('contact_subject'), 1); ?>
					<?php echo preg_replace($regex, $input, $this->form->getInput('contact_subject')); ?>
				</div>
			</div>
			<div class="form-group contact-mes">
				<div class="col-sm-12">
					<?php echo preg_replace($regex, $label, $this->form->getLabel('contact_message'), 1); ?>
					<?php echo preg_replace($regex, $input, $this->form->getInput('contact_message')); ?>
				</div>
			</div>
			<?php //Dynamically load any additional fields from plugins. ?>
			<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
				<?php if ($fieldset->name != 'contact'):?>
					<?php if ($fieldset->name === 'captcha' && !$this->captchaEnabled) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<?php $fields = $this->form->getFieldset($fieldset->name); ?>
					<?php if (count($fields)) : ?>
						<fieldset>
							<?php if (isset($fieldset->label) && ($legend = trim(JText::_($fieldset->label))) !== '') : ?>
								<legend><?php echo $legend; ?></legend>
							<?php endif; ?>
							<?php foreach ($fields as $field) : ?>
								<?php echo $field->renderField(); ?>
							<?php endforeach; ?>
						</fieldset>
					<?php endif; ?>
				<?php endif ?>
			<?php endforeach;?>
			<div class="form-group control-action">
				<?php if ($this->params->get('show_email_copy')) { ?>
						<div class="col-sm-12">
							<div class="checkbox">
								<?php echo $this->form->getInput('contact_email_copy'); ?>
								<?php echo $this->form->getLabel('contact_email_copy'); ?>
							</div>
						</div>
				<?php } ?>
				<div class="col-sm-12 control-btn">
					<button class="btn-flip-wrap validate" type="submit">
						<span class="btn btn-lg btn-border btn-flip-front"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></span>
						<span class="btn btn-lg btn-inverse btn-flip-back"><?php echo JText::_('TPL_CONTACT_THANKS'); ?></span>
					</button>
				</div>
				
				<input type="hidden" name="option" value="com_contact" />
				<input type="hidden" name="task" value="contact.submit" />
				<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
				<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</fieldset>
	</form>
</div>
