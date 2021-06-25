<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
$item = $displayData['item'];
$author = ($item->created_by_alias ? $item->created_by_alias : $item->author);
?>

<dd class="createdby hasTooltip" title="<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', ''); ?>">
	<i class="fa fa-user"></i>
	<?php if (!empty($displayData['item']->contact_link ) && $displayData['params']->get('link_author') == true) : ?>
		<span><?php echo JHtml::_('link', $displayData['item']->contact_link, $author, array('itemprop' => 'url')); ?></span>
	<?php else :?>
		<span><?php echo $author; ?></span>
	<?php endif; ?>
</dd>