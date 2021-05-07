<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<ul class="blog-list<?php echo $moduleclass_sfx; ?> mod-list row">
	<?php if ($grouped) : ?>
		<?php foreach ($list as $group_name => $group) : ?>
		<li>
			<div class="mod-articles-category-group"><?php echo JText::_($group_name); ?></div>
			<ul>
				<?php foreach ($group as $item) : ?>
					<li>
						<?php if ($params->get('link_titles') == 1) : ?>
							<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
								<?php echo $item->title; ?>
							</a>
						<?php else : ?>
							<?php echo $item->title; ?>
						<?php endif; ?>

						<?php if ($item->displayHits) : ?>
							<span class="mod-articles-category-hits">
								(<?php echo $item->displayHits; ?>)
							</span>
						<?php endif; ?>

						<?php if ($params->get('show_author')) : ?>
							<span class="mod-articles-category-writtenby">
								<?php echo $item->displayAuthorName; ?>
							</span>
						<?php endif; ?>

						<?php if ($item->displayCategoryTitle) : ?>
							<span class="mod-articles-category-category">
								(<?php echo $item->displayCategoryTitle; ?>)
							</span>
						<?php endif; ?>

						<?php if ($item->displayDate) : ?>
							<span class="mod-articles-category-date"><?php echo $item->displayDate; ?></span>
						<?php endif; ?>

						<?php if ($params->get('show_tags', 0) && $item->tags->itemTags) : ?>
							<div class="mod-articles-category-tags">
								<?php echo JLayoutHelper::render('joomla.content.tags', $item->tags->itemTags); ?>
							</div>
						<?php endif; ?>

						<?php if ($params->get('show_introtext')) : ?>
							<p class="mod-articles-category-introtext">
								<?php echo $item->displayIntrotext; ?>
							</p>
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
												<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
											<?php endif; ?>
									<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
										<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
									<?php else : ?>
										<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
										<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
									<?php endif; ?>
								</a>
							</p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</li>
		<?php endforeach; ?>
	<?php else : ?>
		<?php foreach ($list as $item) : ?>
			<li class="col-12 col-md-6 col-lg-3">
	        <!-- Item image -->
	        <?php  
	        $images = "";
	        if (isset($item->images)) {
	          $images = json_decode($item->images);
	        }

	        $imgexists = (isset($images->image_intro) and !empty($images->image_intro)) || (isset($images->image_fulltext) and !empty($images->image_fulltext));
	        
	        if ($imgexists) {			
	        	$images->image_intro = $images->image_intro?$images->image_intro:$images->image_fulltext;
	        ?>
	
	        <a class="item-image" href="<?php echo $item->link; ?>">
	          <img src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo $item->title; ?>" />
	        </a>
	        <?php } ?>
	        <!-- // Item image -->

				<div class="article-meta">
					<?php if ($item->displayCategoryTitle) : ?>
						<span class="mod-articles-category-category">
							<?php echo $item->displayCategoryTitle; ?>
						</span>
					<?php endif; ?>

					<?php if ($item->displayDate) : ?>
						<span class="item-date">
							<?php echo $item->displayDate; ?>
						</span>
					<?php endif; ?>

					<?php if ($params->get('show_author')) : ?>
						<span class="mod-articles-category-writtenby">
							<?php echo $item->displayAuthorName; ?>
						</span>
					<?php endif; ?>
				</div>

				<?php if ($params->get('link_titles') == 1) : ?>
					<h3 class="item-title"><a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h3>
				<?php else : ?>
					<h3><?php echo $item->title; ?></h3>
				<?php endif; ?>

				<?php if ($params->get('show_introtext')) : ?>
					<p class="item-introtext">
						<?php echo $item->displayIntrotext; ?>
					</p>
				<?php endif; ?>

				<?php if ($item->displayHits) : ?>
					<span class="mod-articles-category-hits">
						Hits: <?php echo $item->displayHits; ?>
					</span>
				<?php endif; ?>

				<?php if ($params->get('show_readmore')) : ?>
					<p class="item-readmore">
						<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
							<?php if ($item->params->get('access-view') == false) : ?>
								<?php echo JText::_('MOD_ARTICLES_CATEGORY_REGISTER_TO_READ_MORE'); ?>
							<?php elseif ($readmore = $item->alternative_readmore) : ?>
								<?php echo $readmore; ?>
								<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
							<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
								<?php echo JText::sprintf('MOD_ARTICLES_CATEGORY_READ_MORE_TITLE'); ?>
							<?php else : ?>
								<?php echo JText::_('MOD_ARTICLES_CATEGORY_READ_MORE'); ?>
								<?php echo JHtml::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
							<?php endif; ?>
						</a>
					</p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	<?php endif; ?>
</ul>


<style>
	.blog-list {
		list-style: none;
		padding: 0;
	}

	/* Item image */
	.item-image {
		display: block;
		margin-bottom: 24px;
	}

	/* Item date */
	.item-date {
		color: #aaa;
		margin-bottom: 8px;
	}

	/* Item title */
	.blog-list .item-title {
		color: #111;
		font-size: 20px;
		line-height: 24px;
		font-weight: 400;
		margin: 0 0 24px;
	}

	.blog-list .item-title a {
		color: #111;
		text-decoration: none;
	}

	.blog-list .item-title a:hover,
	.blog-list .item-title a:focus,
	.blog-list .item-title a:active {
		color: #c96d50;
	}

	/* Meta */
	.article-meta > span + span:before {
		content: "-";
		color: #aaa;
	}

	/* Intro text */
	.item-introtext {
	}

	/* Read more */
	.blog-list .item-readmore a {
		font-weight: 600;
	}
</style>