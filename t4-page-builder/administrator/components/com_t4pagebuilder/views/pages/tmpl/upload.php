<?php

/** 
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:        https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */
defined('_JEXEC') or die;
 
$maxSize = JFilesystemHelper::fileUploadMaxSize();
// Drag and Drop installation scripts
$token = JSession::getFormToken();
$return = JFactory::getApplication()->input->getBase64('return');

?>
<fieldset class="uploadform">
    <legend>
        <?php echo JText::_('COM_T4PAGEBUILDER_PAGE_UPLOAD'); ?>
    </legend>
    <div id="uploader-wrapper">
        <div id="dragarea" data-state="pending">
            <div id="dragarea-content" class="text-center">
                <p>
                    <span id="upload-icon" class="icon-upload" aria-hidden="true"></span>
                </p>
                <div class="upload-actions">
                    <p class="lead">
                        <?php echo JText::_('COM_T4PAGEBUILDER_PAGE_DRAG_FILE_HERE'); ?>
                    </p>
                    <div class="upload-progress" style="display: none;">
                        <div class="progress progress-striped active">
                            <div class="bar bar-success" style="width: 0;" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="lead">
                            <span class="uploading-text">Uploading ...</span>
                            <span class="uploading-number">0</span><span class="uploading-symbol">%</span>
                        </p>
                    </div>
                    <p>
                        <button id="select-file-button" type="button" class="btn btn-success">
                            <span class="icon-copy" aria-hidden="true"></span>
                            <?php echo JText::_('COM_T4PAGEBUILDER_SELECT_FILE'); ?>
                        </button>
                    </p>
                    <p>
                        <?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', $maxSize); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div id="legacy-uploader" style="display: none;">
        <div class="control-group">
            <label for="install_package" class="control-label">
                <?php echo JText::_('PLG_INSTALLER_PACKAGEINSTALLER_EXTENSION_PACKAGE_FILE'); ?></label>
            <div class="controls">
                <input class="input_box" id="install_package" name="install_package" type="file" size="57" /><br>
                <?php echo JText::sprintf('JGLOBAL_MAXIMUM_UPLOAD_SIZE_LIMIT', $maxSize); ?>
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="button" id="installbutton_package" onclick="Joomla.submitbuttonpackage()">
                <?php echo JText::_('PLG_INSTALLER_PACKAGEINSTALLER_UPLOAD_AND_INSTALL'); ?>
            </button>
        </div>
        <input id="installer-return" name="return" type="hidden" value="<?php echo $return; ?>" />
        <input id="installer-token" name="token" type="hidden" value="<?php echo $token; ?>" />
    </div>
    <div class="t4b-libs-step2" style="display: none">
        <div id="step-2" class="chose-page-install" style="">
            <div class="upload-progress">
                <div class="progress progress-striped active">
                    <div class="bar bar-success" style="width: 0;" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="lead">
                    <span class="uploading-text">Uploading ...</span>
                    <span class="uploading-number">0</span><span class="uploading-symbol">%</span>
                </p>
            </div>
            <div class="t4b-pages" style="display: none;">
              <div class="checkall-toggle">
                <label for="checkall" class="custom-checkbox">
                  <input id="checkall" class="upload_checkall" type="checkbox" name="checkall-toggle" value="" class="" title="" onclick="Joomla.checkAll(this)" data-original-title="Check All Items">Check All
                  <span class="checkmark"></span>
                </label>

                <div class="t4b-action-wrap" style="display: none">
                  <button type="button" class="btn btn-primary btn-page-import"><?php echo JText::_("COM_T4PAGEBUILDER_IMPORT");?></button>
                </div>
              </div>

              <ul class="page-list"></ul>
            </div>
            <input type="hidden" name="package" id="package-loaded" value="" data-package="">
        </div>

    </div>
</fieldset>