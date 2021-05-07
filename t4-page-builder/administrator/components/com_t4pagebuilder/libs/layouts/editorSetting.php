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
  $loadfontDefaults = file_get_contents(JPB_PATH_MEDIA_BUILDER . 'googlefonts/data.json');
  $loadfonts = $params->get('loadfonts') ? $params->get('loadfonts') : $loadfontDefaults;
  $customfont = [];
  if(isset($loadfonts)):
      $valArr = json_decode($loadfonts); 
  endif;
  $datas = json_decode(file_get_contents((JPB_PATH_MEDIA_BUILDER . 'googlefonts/fonts.json')));
  $i = 0;
  $pash = 1;
  $configLoadIcons = $params->get('loadconfigicons') ? $params->get('loadconfigicons') : '{"awesome_icons":{"awesome_icons":true,"url_type":"cdn","custom_url":"null"},"material_icons":{"material_icons":true,"url_type":"cdn","custom_url":""}}';
  $configdata = json_decode($configLoadIcons,true);
  $awesome_icons = $configdata['awesome_icons'];
  $material_icons = $configdata['material_icons'];
  $pageid = $displayData['pageid'];
  if($pageid) $customfont = is_file(JPB_PATH_MEDIA_BUILDER . 'etc/'.$pageid.'/customfonts.json') ? json_decode(file_get_contents(JPB_PATH_MEDIA_BUILDER . 'etc/'.$pageid.'/customfonts.json')) : [];
// var_dump(is_file(JPB_PATH_MEDIA_BUILDER . 'etc/'.$pageid.'/customfonts.json'));die;
?>
<div class="editor-settings">
  <ul class="nav t4b-nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="googlefonts-tab" data-toggle="tab" href="#googlefonts" role="tab" aria-controls="googlefonts" aria-selected="true">Google Fonts</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="customfonts-tab" data-toggle="tab" href="#customfonts" role="tab" aria-controls="customfonts" aria-selected="true">Custom Fonts</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="settingsOthers-tab" data-toggle="tab" href="#settingsOthers" role="tab" aria-controls="settingsOthers" aria-selected="false">Icon Fonts</a>
    </li>
<!--     <li class="nav-item">
      <a class="nav-link" id="customOther-tab" data-toggle="tab" href="#customOther" role="tab" aria-controls="customOther" aria-selected="false">Custom Others</a>
    </li> -->
  </ul>
  <div class="t4b-tabs-content" id="myTabContent">
    <div class="tab-pane show active" id="googlefonts" role="tabpanel" aria-labelledby="googlefonts-tab">
      <!-- Search font -->
      <div class="t4b-fonts-search cleafix">
        <input class="font-filter" name="jform-filter" id="t4-font-filter" type="text" placeholder="Type font name here...">
        <div class="t4-font-filter-message t4-font-filter" style="display:none;"></div>
      </div>
      <!-- // Search font -->

      <div class="load-google-font">
        <div class="modal-body t4-google-content t4-font">
          <div class="t4-font-weight-popup" style="position:absolute; display: none"></div>
            <input type="hidden" name="<?php echo $fontData['name'];?>" value="<?php echo htmlentities($loadfonts);?>" class="load-google-fonts" id="<?php echo $fontData['id']; ?>" />
              <div class="font-selected-wrap">
                <div class="tpb-input jpb-fonts-selected">
                  <span>Selected Font</span>
                  <ul>
                    <?php 
                    if (isset($valArr)) {
                      foreach($valArr as $arr): ?>
                      <li class="btn btn-default" data-name="<?php echo $arr->name;?>">
                        <div class="data-font-selected">
                          <span class="font-name" style="font-family: <?php echo $arr->name;?>"><?php echo $arr->name;?></span>
                          <span class="font-styles"><?php echo (isset($arr->weight)) ? implode(',', $arr->weight) : "400";?></span>
                        </div>
                        <span class="fal fa-times jpb-font-deleted"></span></li>
                      <?php endforeach; }
                    ?>
                  </ul>
                </div>
              </div>

              <div class="tab-content" id="managerTabContent">
                <div class="tab-pane active" id="jub-google-content" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="tab-content-wrap">
                      <ul class="jub-fonts">
                        <?php foreach((array) $datas AS $data):
                          if($i == '100'){$pash++;$i = 0;}
                            $dataStyle = implode(',',$data->styles);
                            $checked = isset($valArr->{$data->name}) ? ' checked' : '';
                        ?>
                      <li class="jub-font jub-google-font<?php echo $checked;?>" data-bash="<?php echo $pash;?>" data-pos="<?php echo $i;?>" style="--pos:<?php echo $i;?>;" data-name="<?php echo $data->name;?>" data-category="" data-styles="<?php echo $dataStyle;?>">
                    <div class="jub-font-container" title="<?php echo $data->name;?> : <?php echo $dataStyle;?>" style="background-position: 0 calc(-40px * <?php echo $i;?>)">
                      <span class="jub-font-name"><?php echo $data->name;?></span>
                      <span class="jub-font-styles"><?php echo count($data->styles);?> Styles</span>
                    </div>
                  </li>
                <?php $i++; endforeach;?>
                </ul>
              </div>
            </div>
          </div>  

        </div>
      </div>
    </div>
    <div class="tab-pane fade" id="settingsOthers" role="tabpanel" aria-labelledby="settingsOthers-tab">
        <div class="load-font-icons">
          <div class="load-font-awesome_icons" data-val="<?php echo htmlentities(json_encode($awesome_icons));?>">
          <div class="control-group">
            <div class="control-label">
              <label id="jform_loadfontaws-lbl" for="awesome_icons" class="hasPopover" title="" data-content="Set publication status." data-original-title="loadfontaws">Awesome Icon Fonts</label>
            </div>
            <div class="controls">
              <?php $checked = ($awesome_icons['awesome_icons']) ? 'checked' : ""; ?>
              <div id="awesome_icons" class="onoffswitch" data-icon="awesome_icons">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="onoffswitch" <?php echo $checked; ?>>
                <label class="onoffswitch-label" for="myonoffswitch">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
              </div>
            </div>
          </div>
          <div class="custom-load-font"<?php echo $checked ? "" : " style='display:none'";?>>
            <fieldset>
              <legend>Load font awesome icons</legend>
              <div class="control-group">
                <div class="control-label">
                  <label id="jform_loadfontaws-lbl" for="jform_loadfontaws" class="hasPopover" title="" data-content="Set publication status." data-original-title="loadfontaws">Load from</label>
                </div>
                <div class="controls">
                  <div class="btn-group url-type" id="url-icons" data-icon="awesome_icons" role="group" aria-label="Select Url Type">
                    <button id="" type="button" class="btn btn-cdn" data-val="cdn">From CDN</button>
                    <button type="button" class="btn btn-url" data-val="url">From Url</button>
                  </div>
                </div>
              </div>
              <div class="control-group custom-url">
                <input type="text" id="url_awesome_icons" name="url_awesome_icons" data-icon="awesome_icons" placeholder="Enter file url here" />
                <small>Ex: http(s)://domain.com/folder/sample-file.css</small>
              </div>
            </fieldset>
          </div>
          </div>
          <div class="load-font-material_icons" data-val="<?php echo htmlentities(json_encode($material_icons));?>">
          <div class="control-group">
            <div class="control-label">
              <label id="jform_material_icons-lbl" for="material_icons" class="hasPopover" title="" data-content="Set publication status." data-original-title="loadfontaws">Material Icon Fonts</label>
            </div>
            <div class="controls">
              <?php $checked = ($material_icons['material_icons']) ? 'checked' : ""; ?>
              <div id="material_icons" class="onoffswitch" data-icon="material_icons">
                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="onoffswitch" <?php echo $checked; ?>>
                <label class="onoffswitch-label" for="myonoffswitch">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
              </div>
            </div>
          </div>
          <div class="custom-load-font"<?php echo $checked ? "" : " style='display:none'";?>>
            <fieldset>
              <legend>Load material icons</legend>
              <div class="control-group">
                <div class="control-label">
                  <label id="jform_url_type-lbl" class="hasPopover" title="" data-content="Set publication status." data-original-title="loadfontaws">Load from</label>
                </div>
                <div class="controls">
                  <div class="btn-group url-type" id="url-icons" data-icon="material_icons" role="group" aria-label="Select Url Type">
                    <button id="" type="button" class="btn btn-cdn" data-val="cdn">From CDN</button>
                    <button type="button" class="btn btn-url" data-val="url">From Url</button>
                  </div>
                </div>
              </div>
              <div class="control-group custom-url">
                <input id="url_material_icons" type="text" name="url_material_icons" data-icon="material_icons" placeholder="Enter file url here..." />
                <small>Ex: http(s)://domain.com/folder/sample-file.css</small>
              </div>
            </fieldset>
          </div>
          </div>
          <input type="hidden" id="load_font_icons" name="loadfonticons" value="<?php echo htmlentities($configLoadIcons);?>">
        </div>
    </div>
    <div class="tab-pane fade" id="customfonts" role="tabpanel" aria-labelledby="customfonts-tab">
    <div class="tab-content-wrap">
        <ul class="custom-fonts">
        <?php if(!empty($customfont->fonts)):?>
        	<?php $i= 0;?>
        <?php foreach((array) $customfont->fonts AS $customFont):
          	if(!empty($customFont->styles))$dataStyle = implode(',',$customFont->styles);?>
        	<li class="custom-font custom-item" data-name="
                <?php echo $customFont->name;?>" data-category="custom" data-styles="
                <?php if(!empty($dataStyle)) echo $dataStyle;?>">
                <div class="custom-font-container" title="<?php echo $customFont->name;?>">
                    <span class="custom-font-name">
                        <?php echo $customFont->name;?></span>
                    <span class="t4-btn btn-action" data-action="fonts.remove" data-index="<?php echo $i;?>" data-pageid="<?php echo $pageid; ?>" data-tooltip="<?php echo JText::_('T4_FIELD_ADDONS_REMOVE') ?>"><i class="fal fa-trash-alt"></i></span>
                </div>
            </li>
            <?php $i++;?>
        <?php endforeach;?>
        <?php endif;?>
            <li id="custom-local" class="custom-font hide" data-name="custom-font" data-category="custom" data-styles="">
                <div class="custom-font-container" title="custom-font">
                    <span class="custom-font-name"></span>
                    <span class="t4-btn btn-action" data-action="fonts.remove" data-tooltip="<?php echo JText::_('T4_FIELD_ADDONS_REMOVE') ?>"><i class="fal fa-trash-alt"></i></span>
                </div>
            </li>
        </ul>
    </div>
    <div class="form-add-custom-font">
        <div class="add-more-custom-font">
            <span class="btn-action active" data-action="font.addcss" data-type="css">
                <?php echo JText::_('T4_THEME_FIELD_ADD_CSS_CUSTOM') ?></span>
            <span class="btn-action" data-action="font.addfont" data-type="font">
                <?php echo JText::_('T4_THEME_FIELD_ADD_FONT_CUSTOM') ?></span>
        </div>
        <div class="custom-font-form custom-fonts">
            <div class="control-group custom-font-url" style="display: none;">
                <div class="control-label"><label>
                        <?php echo JText::_('T4_THEME_FONT_CUSTOM_FONT_LABEL') ?></label></div>
                <div class="controls"><textarea id="custom-font-url" class="custom-font-input" name="custom-font-url" rows="3" data-value=""></textarea></div>
                <!-- <div class="control-helper"><?php echo JText::_('T4_THEME_FONT_CUSTOM_FONT_DESC') ?></div> -->
            </div>
            <div class="control-group custom-css">
                <div class="control-label"><label>
                        <?php echo JText::_('T4_THEME_FONT_CSS_LABEL') ?></label></div>
                <div class="controls"><textarea id="custom-css" class="custom-font-input" name="custom-css" rows="3" data-value=""></textarea></div>
                <!-- <div class="control-helper"><?php echo JText::_('T4_THEME_FONT_CSS_DESC') ?></div> -->
            </div>
            <div class="fonts-actions">
                <span class="t4-btn btn-action btn-primary" data-action="fonts.save" data-type="custom-fonts" data-pageid="<?php echo $pageid; ?>">
                    <?php echo JText::_('T4_THEME_FONT_CUSTOM_ADD') ?></span>
            </div>
        </div>
    </div>
</div>
    <div class="tab-pane fade" id="customOther" role="tabpanel" aria-labelledby="customOther-tab">Custom Others</div>
  </div>
</div>