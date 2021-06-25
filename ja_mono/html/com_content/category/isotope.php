<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::addIncludePath(T3_PATH.'/html/com_content');
JHtml::addIncludePath(dirname(dirname(__FILE__)));
JHtml::_('behavior.caption');

$doc = JFactory::getDocument();
$doc->addScript (T3_TEMPLATE_URL . '/js/isotope.pkgd.js');
$doc->addScript (T3_TEMPLATE_URL . '/js/imagesloaded.pkgd.min.js');
$doc->addScript (T3_TEMPLATE_URL . '/js/packery-mode.pkgd.min.js');

?>

<div class="blog articles-list ja-isotope-wrap packery <?php echo $this->pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
  <?php if ($this->params->get('show_page_heading', 1)) : ?>
  <div class="page-header clearfix">
    <h1 class="page-title"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
  </div>
  <?php endif; ?>
  <?php 
    $inner_modules = 'portfolio-menu';
    $attrs = array();
    $attrs['style'] = 'raw';
    $result = null;
    $renderer = JFactory::getDocument()->loadRenderer('modules');
    $inner = $renderer->render($inner_modules, $attrs, $result); 
    echo $inner;
  ?>
  <div class="article-items">
  <?php if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
    <div class="page-subheader clearfix">
      <h2 class="page-subtitle"><?php echo $this->escape($this->params->get('page_subheading')); ?>
      <?php if ($this->params->get('show_category_title')) : ?>
      <small class="subheading-category"><?php echo $this->category->title;?></small>
      <?php endif; ?>
      </h2>
  </div>
  <?php endif; ?>
  
  <?php if ($this->params->get('show_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
    <?php echo JLayoutHelper::render('joomla.content.tags', $this->category->tags->itemTags); ?>
  <?php endif; ?>
  
  <?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
  <div class="category-desc clearfix">
    <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
      <img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
    <?php endif; ?>
    <?php if ($this->params->get('show_description') && $this->category->description) : ?>
      <?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (empty($this->lead_items) && empty($this->link_items) && empty($this->intro_items)) : ?>
    <?php if ($this->params->get('show_no_articles', 1)) : ?>
      <p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
    <?php endif; ?>
  <?php endif; ?>
  <div class="grid isotope clearfix grid-xs-1 grid-sm-2" id="grid">
  <?php $leadingcount = 0; ?>

  <?php
    $introcount = (count($this->intro_items));
    $counter = 0;
  ?>

  <?php if (!empty($this->intro_items)) : ?>
  <?php foreach ($this->intro_items as $key => &$item) : ?>
    <?php 
      $itemAttribs  = new JRegistry($item->attribs);
      $itemHeight = $itemAttribs->get('jcontent_height', '0');
    ?>
    <?php $rowcount = ((int) $counter % (int) $this->columns) + 1; ?>
      <div class="article-item item <?php echo ($itemHeight ? 'height2' : ''); ?> <?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
        itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
        <?php
        $this->item = &$item;
        echo $this->loadTemplate('item');
      ?>
      </div><!-- end item -->
      <?php $counter++; ?>
  <?php endforeach; ?>
  <?php endif; ?>
  </div></div>
  <?php
    $show_option = $this->params->get('show_pagination');
    $pagination_type = $this->params->get('pagination_type');
  ?>
  
  <?php
    $pagesTotal = isset($this->pagination->pagesTotal) ? $this->pagination->pagesTotal : $this->pagination->get('pages.total');

    if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($pagesTotal > 1)) : ?>
    <div class="pagination-wrap">
      <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
      <div class="counter"> <?php echo $this->pagination->getPagesCounter(); ?></div>
      <?php endif; ?>
      <?php echo $this->pagination->getPagesLinks(); ?> 
    </div>
    <?php  endif; ?>
    <!-- show load more use infinitive-scroll -->
    <?php
        if ($show_option && $pagination_type > 0){ ?>
        <div class="infinity-wrap">
    <?php JFactory::getDocument()->addScript (T3_TEMPLATE_URL . '/js/infinitive-paging.js');
            JFactory::getDocument()->addScript (T3_TEMPLATE_URL . '/js/jquery.infinitescroll.js');

            $mode = $this->params->def('pagination_type', 2) == 1 ? 'manual' : 'auto';
            if($this->pagination->get('pages.total') > 1 ) : ?>
                <script>
                    jQuery(".pagination-wrap").css('display','none');
                </script>
                <div id="infinity-next" class="readmore hide" data-mode="<?php echo $mode ?>" data-pages="<?php echo $this->pagination->get('pages.total') ?>" data-finishedmsg="<?php echo JText::_('TPL_INFINITY_NO_MORE_ARTICLE');?>"><?php echo JText::_('TPL_INFINITY_MORE_ARTICLE')?></div>
            <?php else:  ?>
                <div id="infinity-next" class="readmore disabled" data-pages="1"><?php echo JText::_('TPL_INFINITY_NO_MORE_ARTICLE');?></div>    
            <?php endif; ?>
        </div>
     <?php   }
    ?>
</div>