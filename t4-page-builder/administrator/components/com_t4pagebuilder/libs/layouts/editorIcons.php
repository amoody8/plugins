<?php

/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:      https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */
defined('_JEXEC') or die;
 
	$fontData = ['id' => 'editor_font', 'name' => 'setting_fonts', 'value' => ''];
    $params = JComponentHelper::getParams('com_t4pagebuilder');
    $material = json_decode(file_get_contents(JPATH_ROOT . JPB_MEDIA_BUILDER . '/vendors/icons/material.json'));
    $awesome = json_decode(file_get_contents(JPATH_ROOT . JPB_MEDIA_BUILDER . '/vendors/icons/awesome.json'));
    // $datas = json_decode(file_get_contents((JPATH_ROOT . JPB_MEDIA_BUILDER . 'googlefonts/fonts.json')));
    $i = 0;
    $pash = 1;
?>
<div class="editor-icons">
  <ul class="nav t4b-nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="material-tab" data-toggle="tab" href="#material" role="tab" aria-controls="material" aria-selected="true">Material</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="awesome-tab" data-toggle="tab" href="#awesome" role="tab" aria-controls="awesome" aria-selected="false">Awesome</a>
    </li>
  </ul>
  <!-- Search font -->
  <div class="t4b-icons-search cleafix">
    <input class="icon-filter" name="icon-filter" id="t4-icon-filter" type="text" placeholder="Type icon name here...">
  </div>
  <!-- // Search font -->

  <div class="t4b-tabs-content" id="myTabContent">
    <div class="tab-pane show active" id="material" role="tabpanel" aria-labelledby="material-tab">
      <div class="library-container">
        <?php foreach($material->categories as $icons):?>
          <div class="category-container">
            <h2 class="category-name"><?php echo $icons->name;?></h2>

            <div id="<?php echo $icons->name;?>">
              <ul class="t4b-icons-list">
              <?php foreach($icons->icons as $icon):?>
                <li class="t4b-icon" data-val="<?php echo $icon->id;?>" data-tab="material" data-name="<?php echo str_replace("_"," ",$icon->id);?>" title="<?php echo str_replace("_"," ",$icon->id);?>"><i class="material-icons"><?php echo $icon->id;?></i><span class="t4b-icon-name"><?php echo str_replace("_"," ",$icon->id);?></span></li>
              <?php endforeach;?>
              </ul>
            </div>
          </div>
          <?php $i++;?>
        <?php endforeach;?>
      </div>
    </div>

    <div class="tab-pane fade" id="awesome" role="tabpanel" aria-labelledby="awesome-tab">
      <div class="library-container">
          <ul class="t4b-icons-list">
            <?php foreach($awesome as $class => $icons):?>
              <?php $style = $icons->styles;?>
                <?php foreach($icons->styles as $style):?>
                <?php
                  $dataVal = '';
                  switch ($style) {
                    case 'brands':
                     $dataVal ='fab';
                      break;
                    case 'solid':
                      $dataVal ='fas';
                      break;
                    case 'regular':
                     $dataVal ='far';
                      break;
                    
                    default:
                      $dataVal ='fa';
                      break;
                  }
                ?>
                <li class="t4b-icon" data-val="<?php echo $dataVal.' fa-'.$class;?>" data-tab="fa" data-name="<?php echo $icons->label;?>" title="<?php echo $icons->label;?>"><i class="<?php echo $dataVal.' fa-'.$class;?>"></i><span class="t4b-icon-name"><?php echo $icons->label;?></span></li>
              <?php endforeach;?>
            <?php endforeach;?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>