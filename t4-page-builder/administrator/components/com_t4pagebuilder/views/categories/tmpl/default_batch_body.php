<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
// check if need separated layout for Joomla 3!
if (($j3 = \JPB\Helper\Layout::j3(__FILE__))) {
	include $j3;
	return;
}
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$published = $this->state->get('filter.published');
$extension = $this->escape($this->state->get('filter.extension'));

?>

<div class="container-fluid">
	<div class="row">
		<div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.language', []); ?>
			</div>
		</div>
		<div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.access', []); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<?php if ($published >= 0) : ?>
			<div class="form-group col-md-6">
				<div class="controls">
					<?php echo LayoutHelper::render('joomla.html.batch.item', ['extension' => $extension, 'addRoot' => true]); ?>
				</div>
			</div>
		<?php endif; ?>
		<div class="form-group col-md-6">
			<div class="controls">
				<?php echo LayoutHelper::render('joomla.html.batch.tag', []); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-md-6">
			<div class="control-group">
				<label id="flip-ordering-id-lbl" for="flip-ordering-id" class="control-label">
					<?php echo Text::_('JLIB_HTML_BATCH_FLIPORDERING_LABEL'); ?>
				</label>
				<fieldset id="flip-ordering-id">
					<?php echo HTMLHelper::_('select.booleanlist', 'batch[flip_ordering]', array(), 0, 'JYES', 'JNO', 'flip-ordering-id'); ?>
				</fieldset>
			</div>
		</div>
	</div>
</div>

