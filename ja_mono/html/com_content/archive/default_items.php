<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$params = $this->params;

$info    = $params->get('info_block_position', 2);
$aInfo1 = ($params->get('show_publish_date') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author'));
$aInfo2 = ($params->get('show_create_date') || $params->get('show_modify_date') || $params->get('show_hits'));
$topInfo = ($aInfo1 && $info != 1) || ($aInfo2 && $info == 0);
$botInfo = ($aInfo1 && $info == 1) || ($aInfo2 && $info != 0);
$icons = $params->get('access-edit') || $params->get('show_print_icon') || $params->get('show_email_icon');

?>

<div id="archive-items">
	<?php foreach ($this->items as $i => $item) : ?>
		<article class="row<?php echo $i % 2; ?>" itemscope itemtype="http://schema.org/Article">
			<!-- Aside -->
		    <?php if ($topInfo || $icons) : ?>
		    <aside class="article-aside clearfix">
		      <?php if ($topInfo): ?>
		      <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $item, 'params' => $params, 'position' => 'above')); ?>
		      <?php endif; ?>
		      
		      <?php if ($icons): ?>
		      <?php echo JLayoutHelper::render('joomla.content.icons', array('item' => $item, 'params' => $params)); ?>
		      <?php endif; ?>
		    </aside>  
		    <?php endif; ?>
		    <!-- //Aside -->

			<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $item, 'params' => $params, 'title-tag'=>'h2')); ?>

			<?php if ($params->get('show_intro')) :?>
				<div class="intro" itemprop="articleBody"> <?php echo JHtml::_('string.truncateComplex', $item->introtext, $params->get('introtext_limit')); ?> </div>
			<?php endif; ?>
			<?php $url = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug)); ?>
			<a href="<?php echo $url; ?>" class="more-icon"><i class="fa fa-plus"></i></a>

		    <!-- footer -->
		    <?php if ($botInfo) : ?>
		    <footer class="article-footer clearfix">
		      <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $item, 'params' => $params, 'position' => 'below')); ?>
		    </footer>
		    <?php endif; ?>
		    <!-- //footer -->

		</article>
	<?php endforeach; ?>
</div>

<?php 
$pagesTotal = isset($this->pagination->pagesTotal) ? $this->pagination->pagesTotal : $this->pagination->get('pages.total');
if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $pagesTotal > 1)) : ?>
  <nav class="pagination-wrap clearfix">

    <?php if ($this->params->def('show_pagination_results', 1)) : ?>
      <div class="counter">
        <?php echo $this->pagination->getPagesCounter(); ?>
      </div>
    <?php  endif; ?>
        <?php echo $this->pagination->getPagesLinks(); ?>
  </nav>
<?php endif; ?>