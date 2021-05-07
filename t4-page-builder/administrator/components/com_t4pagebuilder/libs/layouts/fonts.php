<?php

/**
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:		  https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */
defined('_JEXEC') or die;
 
    JFactory::getDocument()->addStyleSheet(JUri::base() .  "components/com_t4pagebuilder/assets/css/googlefonts.css");
    JFactory::getDocument()->addScript(JUri::base() .  "components/com_t4pagebuilder/assets/js/googlefonts.js");
    $datas = json_decode(file_get_contents((JPATH_ADMINISTRATOR ."/components/com_t4pagebuilder/assets/googlefonts/fonts.json")));
    $app = JFactory::getApplication();
    $input = $app->input;
    if ($input->get('tmpl') === 'component') {
        JFactory::getDocument()->addStyleSheet(JUri::base() .  "components/com_t4pagebuilder/assets/css/edditorsettings.css");
    }
    $classEvent = array(
        'events' => 'google-font',
        'filter' => 'font-filter',
        'tabId' 	 => 'google-content'
    );
    $i = 0;
    $pash = 1;
    $inputName = $displayData['name'];
    $inputVal = $displayData['value'];
    $inputId = $displayData['id'];
    if (isset($inputVal)):
        $valArr = json_decode($inputVal);
    endif;

?>
<div class="tpb-loadfont">
	<div class="fonts-selected">
		<div class="tpb-input input">
			<ul>
			<?php
                if (isset($valArr)) {
                    foreach ($valArr as $arr):?>
					<li class="btn btn-default" data-name="<?php echo $arr->name; ?>"><span><?php echo $arr->name; ?></span><span class="icon-cancel"></span></li>
				<?php
                endforeach;
                }
            ?>
			</ul>
		</div>
		<div class="font-managers"><button class="btn btn-primary btn-manager-font" type="button">Manager Font</button></div>
	</div>
	<div class="t4-google-font-modal t4-fonts-manager" style="display:none">
		<div class="t4-modal modal t4-google-font-setting">
		    <div class="modal-header">
		      <h5 class="modal-title"><i class="fal fa-font"></i>Fonts Manager</h5>
		      <a type="button" class="action-t4-modal-close" data-dismiss="modal" aria-label="Close">
		        <span class="fal fa-times" aria-hidden="true"></span>
		      </a>
		    </div>
		    <div class="modal-body t4-google-content t4-font">
		    	<div class="jpb-fix-to-top">
			    	<div class="tpb-input jpb-fonts-selected">
			    		<span>Selected Font</span>
			    		<ul>
						<?php
                        if (isset($valArr)) {
                            foreach ($valArr as $arr):?>
								<li class="btn btn-default" data-name="<?php echo $arr->name; ?>"><span><?php echo $arr->name; ?></span><span class="icon-cancel"></span></li>
							<?php
                            endforeach;
                        }
                        ?>
						</ul>
			    	</div>
					<div class="t4-fonts-filter cleafix">
						<input class="font-filter" name="jform-filter" id="t4-font-filter" type="text" placeholder="Search..">
					</div>
					<ul class="nav nav-tabs" id="managerFontsTab" role="tablist">
					  <li class="nav-item active">
					    <a class="nav-link tab-google-font active" data-toggle="tab" href="#jub-google-content" role="tab" aria-controls="home" aria-selected="true">Google Fonts</a>
					  </li>
					</ul>
				</div>
				<div class="tab-content" id="managerTabContent">
					<div class="tab-pane active" id="jub-google-content" role="tabpanel" aria-labelledby="nav-home-tab">
						<div class="t4-font-filter-message t4-font-filter" style="display:none;"></div>
				      	<div class="tab-content-wrap">
					      	<ul class="jub-fonts">
				      		<?php foreach ((array) $datas as $data):
                                        if ($i == '100') {
                                            $pash++;
                                            $i = 0;
                                        }
                                        $dataStyle = implode(',', $data->styles);
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
			<div class="modal-footer t4-font-footer">
				<button type="button" class="btn btn-default t4-font-cancel">Cancel</button>
				<button type="button" class="btn btn-primary t4-font-update">Update</button>
			</div>
	    </div>
	</div>

</div>
<div class="t4-font-weight-popup" style="position:absolute; display: none"></div>
<input type="hidden" name="<?php echo $inputName;?>" value="<?php echo htmlentities($inputVal);?>" class="load-google-fonts" id="<?php echo $inputId; ?>" />