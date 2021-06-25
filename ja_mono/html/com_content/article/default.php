<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::addIncludePath(T3_PATH . '/html/com_content');
JHtml::addIncludePath(dirname(dirname(__FILE__)));

JHtml::_('behavior.caption');
JHtml::_('bootstrap.tooltip');

// Create shortcuts to some parameters.
$params   = $this->item->params;
$urls     = json_decode($this->item->urls);
$canEdit  = $params->get('access-edit');
$user     = JFactory::getUser();
$info    = $params->get('info_block_position', 2);
$aInfo1 = ($params->get('show_publish_date') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author'));
$aInfo2 = ($params->get('show_create_date') || $params->get('show_modify_date') || $params->get('show_hits'));
$topInfo = ($aInfo1 && $info != 1) || ($aInfo2 && $info == 0);
$botInfo = ($aInfo1 && $info == 1) || ($aInfo2 && $info != 0);
$icons = !empty($this->print) || $canEdit || $params->get('show_print_icon') || $params->get('show_email_icon');

$images   = json_decode($this->item->images);
$imagesBg = $images->image_fulltext 
	? $images->image_fulltext 
	: JUri::base(true) .'/images/joomlart/hero/bg-1.jpg';

$app = JFactory::getApplication();
$menu = $app->getMenu()->getActive();
$modules = JModuleHelper::getModules('masthead');
$modules = array_filter($modules, function($m) {
	return $m->module === 'mod_jamasthead';
});

if ($menu 
	&& $menu->link === 'index.php?option=com_content&view=article&id=' . $this->item->id
	&& $modules) {
	$module = array_shift($modules);
	$moduleParams = new JRegistry($module->params);
	$item_id = $moduleParams->get('menuitem_config')->item_id;
	$background = $moduleParams->get('menuitem_config')->background;

	foreach ($item_id as $key => $value) {
		if (in_array($menu->id, $value)) {
			if (!empty($background[$key])) {
				$imagesBg = $background[$key];
			}
		}
	}
}


$doc = JFactory::getDocument();
$data = array(
	'item' => $this->item, 
	'params' => $params, 
	'imageBg' => $imagesBg,
	'topInfo'=>$topInfo, 
	'icons' => $icons, 
	'view' => $this, 
	'event' => $this->item->event
);

$masthead = JLayoutHelper::render('joomla.content.masthead', $data);
$doc->setBuffer($masthead, array('type' => 'modules', 'name' => 'masthead', 'title' => ''));
?>
<!-- Article -->
<article itemscope itemtype="http://schema.org/Article" class="item-page">
	<?php echo $this->item->event->beforeDisplayContent; ?>
	
	<?php if(!empty($this->print)): 
		if ($icons): 
	  echo JLayoutHelper::render('joomla.content.icons', array('item' => $this->item, 'params' => $params, 'print' => isset($this->print) ? $this->print : null));
	 endif;
		if ($params->get('show_title')) : 
	   echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h1')); 
	endif; 
	endif; ?>

  <meta itemscope itemprop="mainEntityOfPage"  itemType="https://schema.org/WebPage" itemid="https://google.com/article"/>

	<time>
		<meta content="<?php echo JHtml::_('date', $this->item->publish_up, 'c'); ?>" itemprop="datePublished">
	<meta content="<?php echo JHtml::_('date', $this->item->modified, 'c'); ?>" itemprop="dateModified">
	</time>

  <span itemprop="headline" style="display: none;" itemType="https://schema.org/Text" >
	<?php echo $this->escape($this->item->title); ?>
  </span>

  <?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author); ?>
  <span itemprop="author" style="display: none;">
		<span itemprop="name"><?php echo $author; ?></span>
	  <span itemtype="https://schema.org/Organization" itemscope="" itemprop="publisher" style="display: none;">
		<span itemtype="https://schema.org/ImageObject" itemscope="" itemprop="logo">
			<img itemprop="url" alt="logo" src="<?php echo JURI::base(); ?>/templates/<?php echo JFactory::getApplication()->getTemplate() ?>/images/logo.png">
			<meta content="auto" itemprop="width">
			<meta content="auto" itemprop="height">
		</span>
		<meta content="<?php echo $author; ?>" itemprop="name">
	</span>
	</span>

	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />

	<?php if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) : ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

	<!-- <?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?> -->

	<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '0')) || ($params->get('urls_position') == '0' && empty($urls->urls_position))) || (empty($urls->urls_position) && (!$params->get('urls_position')))): ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>

	<?php	if ($params->get('access-view')): ?>
		<?php echo JLayoutHelper::render('joomla.content.fulltext_image', array('item' => $this->item, 'params' => $params)); ?>

		<?php	if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
			echo $this->item->pagination;
		endif; ?>

		<section class="article-content clearfix" itemprop="articleBody">
			<?php echo $this->item->text; ?>
		</section>

		<?php if ($params->get('show_tags', 1) && !empty($this->item->tags)) : ?>
			<?php echo JLayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
		<?php endif; ?>

	  <!-- footer -->
	  <?php if ($botInfo) : ?>
	  <footer class="article-footer clearfix">
		<?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
	  </footer>
	  <?php endif; ?>
	  <!-- //footer -->

		<?php if (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))): ?>
			<?php echo $this->loadTemplate('links'); ?>
		<?php endif; ?>

		<?php //optional teaser intro text for guests ?>
	<?php elseif ($params->get('show_noauth') == true and  $user->get('guest')) : ?>

		<?php echo $this->item->introtext; ?>
		<?php //Optional link to let them register to see the whole article. ?>
		<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			?>
			<section class="readmore">
				<a href="<?php echo $link; ?>" itemprop="url">
							<span>
							<?php $attribs = json_decode($this->item->attribs); ?>
							<?php
							if ($attribs->alternative_readmore == null) :
								echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
							elseif ($readmore = $this->item->alternative_readmore) :
								echo $readmore;
								if ($params->get('show_readmore_title', 0) != 0) :
									echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
								endif;
							elseif ($params->get('show_readmore_title', 0) == 0) :
								echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
							else :
								echo JText::_('COM_CONTENT_READ_MORE');
								echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
							endif; ?>
							</span>
				</a>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative): ?>
		<?php echo $this->item->pagination; ?>
	<?php endif; ?>

	<?php echo $this->item->event->afterDisplayContent; ?>
</article>
<!-- //Article -->