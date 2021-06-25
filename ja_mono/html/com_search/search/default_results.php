<?php
/**
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$db = JFactory::getDbo();
$idArr = [];
foreach ($this->results as $item) {
    if ($item->slug) {
        $ids = explode(':', $item->slug);
        $id = $ids[0];
        $idArr[] = $id;
    }
}
$imgArr = [];
if ($idArr != false) {
    $query = 'SELECT id, images FROM #__content WHERE id IN ('.implode(',', $idArr).')';
    $db->setQuery($query);
    $results = $db->loadObjectList();
    foreach ($results as $res) {
        $imagesObj = json_decode($res->images);
        if (!empty($imagesObj->image_intro) && trim($imagesObj->image_intro) != '') {
            $imgArr[$res->id] = $imagesObj->image_intro;
        }
    }
}
?>
<div class="search-results full-page <?php echo $this->pageclass_sfx; ?>">
<?php foreach ($this->results as $result) : ?>
	<div class="result-item clearfix">
		<?php $ids = explode(':', $result->slug); $id = $ids[0]; if (isset($imgArr[$id])) : ?>
			<div class="img-intro">
				<img class="img-responsive" src="<?php echo $imgArr[$id]; ?>" />
			</div>
		<?php endif; ?>
		<div class="result-detail">
			<div class="result-title">
				<?php echo $this->pagination->limitstart + $result->count.'. '; ?>
				<?php if ($result->href) : ?>
					<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) : ?> target="_blank"<?php endif; ?>>
						<?php echo $result->title; ?>
					</a>
				<?php else : ?>
					<?php // see above comment: do not escape $result->title?>
					<?php echo $result->title; ?>
				<?php endif; ?>
			</div>
			<?php if ($result->section) : ?>
				<div class="result-category">
					<span class="small<?php echo $this->pageclass_sfx; ?>">
						(<?php echo $this->escape($result->section); ?>)
					</span>
				</div>
			<?php endif; ?>
			<div class="result-text">
				<?php echo $result->text; ?>
			</div>
			<?php if ($this->params->get('show_date')) : ?>
				<div class="result-created<?php echo $this->pageclass_sfx; ?>">
					<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>
</div>
<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
