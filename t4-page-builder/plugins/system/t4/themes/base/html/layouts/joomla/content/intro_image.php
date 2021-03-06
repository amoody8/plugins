<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;
use Joomla\CMS\Router\Route;

$params  = $displayData->params;
$images  = json_decode($displayData->images);
?>
<?php if (isset($images->image_intro) && !empty($images->image_intro)) : ?>
	<?php $imgfloat = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro; ?>
	<figure class="pull-<?php echo htmlspecialchars($imgfloat, ENT_COMPAT, 'UTF-8'); ?> item-image">
		<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
			<a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>">
				<img src="<?php echo JUri::root(true).'/'. htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8'); ?>"
					 alt="<?php echo htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8'); ?>"
					 itemprop="image"
				/>
			</a>
		<?php else : ?>
			<img src="<?php echo htmlspecialchars($images->image_intro, ENT_COMPAT, 'UTF-8'); ?>"
				 alt="<?php echo htmlspecialchars($images->image_intro_alt, ENT_COMPAT, 'UTF-8'); ?>"
				 itemprop="image"
			>
		<?php endif; ?>
		<?php if ($images->image_intro_caption !== '') : ?>
			<figcaption class="caption"><?php echo htmlspecialchars($images->image_intro_caption, ENT_COMPAT, 'UTF-8'); ?></figcaption>
		<?php endif; ?>
	</figure>
<?php endif; ?>
