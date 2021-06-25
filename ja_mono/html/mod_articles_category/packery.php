<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$doc->addScript (T3_TEMPLATE_URL . '/js/isotope.pkgd.js');
$doc->addScript (T3_TEMPLATE_URL . '/js/imagesloaded.pkgd.min.js');
$doc->addScript (T3_TEMPLATE_URL . '/js/packery-mode.pkgd.min.js');

$catids = $params->get('catid');
if(isset($catids) && $catids['0'] != ''){
	$catid = $catids[0];	
	$jacategoriesModel = JCategories::getInstance('content');
	$jacategory = $jacategoriesModel->get($catid);
}

?>
<div class="articles-list ja-isotope-wrap packery">

	<div class="article-items">
		<div id="grid" class="grid isotope clearfix grid-xs-1 grid-sm-2">
			<?php $count=0; foreach ($list as $item) : ?>
			<?php $itemAttribs  = new JRegistry($item->attribs); $itemHeight = $itemAttribs->get('jcontent_height', '0'); ?>
			<div class="article-item item <?php echo ($itemHeight ? 'height2' : ''); ?>">
					<?php $images = json_decode($item->images); ?>
					<a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>"><span class="article-img" style="background-image: url('<?php echo $images->image_intro ; ?>');"></span></a>
					<div class="article-content">
						<?php if ($params->get('link_titles') == 1) : ?>
							<h3 class="article-title">
								<a title="<?php echo $item->title; ?>" href="<?php echo $item->link; ?>">
									<?php echo $item->title; ?>
								</a>
							</h3>
						<?php else : ?>
							<?php echo $item->title; ?>
						<?php endif; ?>

						<?php if ($item->displayCategoryTitle) : ?>
							<span class="mod-articles-category-category">
								<?php echo $item->displayCategoryTitle; ?>
							</span>
						<?php endif; ?>
					</div>
			</div>
			<?php $count++; endforeach; ?>
		</div>
		<div class="actions">
			<a class="readmore" href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($jacategory->id));?>"><span><?php echo JText::_('TPL_SEE_ALL_PORTFOLIO'); ?></span></a>
		</div>
	</div>
</div>