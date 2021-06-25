<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$catids = $params->get('catid');
$numberItem = $params->get('count');
if(isset($catids) && $catids['0'] != ''){
	$catid = $catids[0];	
	$jacategoriesModel = JCategories::getInstance('content');
	$jacategory = $jacategoriesModel->get($catid);
}

?>
<div class="articles-list normal">

	<div class="article-items equal-height clearfix">
		<?php $count=1; foreach ($list as $item) : ?>
		<?php 
			$extrafields = new JRegistry($item->attribs); 
		?>
		<div class="col col-xs-12 col-sm-4 col-lg-4 item article-item">
				<?php $images = json_decode($item->images); ?>
				<div class="article-img" style="background-image: url('<?php echo $images->image_intro ; ?>');"></div>
				<div class="article-content">
					<?php if ($params->get('link_titles') == 1) : ?>
						<h3 class="article-title">
							<a class="<?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
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

					<?php if ($params->get('show_introtext')) : ?>
						<p class="mod-articles-category-introtext">
							<?php echo $item->displayIntrotext; ?>
						</p>
					<?php endif; ?>

					<div class="article-footer">
					<?php if ($item->displayDate) : ?>
						<span class="mod-articles-category-date">
							<?php echo $item->displayDate; ?>
						</span>
					<?php endif; ?>

					<?php if ($params->get('show_readmore')) : ?>
						<p class="mod-articles-category-readmore">
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php if ($item->params->get('access-view') == false) : ?>
									<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
								<?php elseif ($readmore = $item->alternative_readmore) : ?>
									<?php echo $readmore; ?>
									<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
										<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
											<?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>
										<?php endif; ?>
								<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
									<?php echo JText::sprintf('TPL_MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
								<?php else : ?>
									<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
									<?php echo JHtml::_('string.truncate', ($item->title), $params->get('readmore_limit')); ?>
								<?php endif; ?>
							</a>
						</p>
					<?php endif; ?>
					</div>
				</div>
		</div>
		<?php $count++; endforeach; ?>
		<div class="actions">
			<a class="readmore" href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($jacategory->id));?>"><span><?php echo JText::_('TPL_SEE_ALL_ARTICLES'); ?></span></a>
		</div>
	</div>
</div>