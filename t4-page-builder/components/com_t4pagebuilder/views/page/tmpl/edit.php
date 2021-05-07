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

// check if need separated layout for Joomla 3!
if (($j3 = \JPB\Helper\Layout::j3(__FILE__))) {
	include $j3;
	return;
}
defined('_JEXEC') or die;

// Load the com_media language files, default to the admin file and fall back to site if one isn't found
$lang = JFactory::getLanguage();
$lang->load('com_t4pagebuilder', JPATH_ADMINISTRATOR, null, false, true)
||	$lang->load('com_t4pagebuilder', JPATH_SITE, null, false, true);

// add field
if(!defined("JPB_PARAM")) define ('JPB_PARAM', 'act');
if(!defined("JPB_DEVMODE")) define ('JPB_DEVMODE', '');
if(!defined("JPB_PATH")) define ('JPB_PATH', __DIR__);
if(!defined("JPB_PATH_BASE")) define ('JPB_PATH_BASE', \JUri::base(true)."/components/com_t4pagebuilder/");
if(!defined("JPB_MEDIA")) define ('JPB_MEDIA', '/media/t4pagebuilder/');
if(!defined("JPB_MEDIA_BUILDER")) define ('JPB_MEDIA_BUILDER', JPB_MEDIA . 'builder/');

// Hand processing over to the admin base file
// require_once JPATH_COMPONENT_ADMINISTRATOR . '/views/page/tmpl/edit.php';
// require_once JPATH_COMPONENT_ADMINISTRATOR . '/t4pagebuilder.php';

use Joomla\Registry\Registry;
use JPB\Helper\Item AS Item;

// Create shortcut to parameters.
$params = clone $this->state->get('params');
$params->merge(new Registry($this->item));
$loadfontDefaults = file_get_contents(JPATH_ROOT . JPB_MEDIA_BUILDER . 'googlefonts/data.json');
$customFont = is_file(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json') ? json_decode(file_get_contents(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json'),true) : [];
$loadgooglefont = $params->get('loadfonts') ? $params->get('loadfonts') : $loadfontDefaults ;
$customFonts = $customFont ? $customFont['fonts'] : [];
$loadIcons = $params->get('loadconfigicons') ? $params->get('loadconfigicons') : "{\"awesome_icons\":{\"awesome_icons\":true,\"url_type\":\"cdn\",\"custom_url\":\"null\"},\"material_icons\":{\"material_icons\":true,\"url_type\":\"cdn\",\"custom_url\":\"\"}}";

if (!defined('JPB_LOADER')) {
	$t4base = \JUri::root(true) . JPB_MEDIA_BUILDER;
	$doc = \JFactory::getDocument();
	$doc->addStyleSheet($t4base . 'css/editor.css');
	$ajaxUrl = \JUri::base(true) . '/index.php?option=com_t4pagebuilder&view=page&format=json';
	if(!defined('JPB_DEVMODE')) define('JPB_DEVMODE',false);
	$doc->addScriptDeclaration('var ajax_url="' . $ajaxUrl . '";var loadgooglefont=' . $loadgooglefont . ';var customFont=' . json_encode($customFonts) . '; var site_root_url = "' . \JUri::root() . '"; var loadIcons = ' . json_encode($loadIcons) . '; var siteUrl = "' . \JUri::root() . '";var builder_url = "' . $t4base . '";var builderParam="' . JPB_PARAM . '";var jpb_devmode="'.JPB_DEVMODE.'";var isT4 ="'.$this->t4.'";');
	$doc->addScript($t4base . 'js/loader.js');
	$doc->addScript(\JUri::root(true) . '/media/media/js/mediafield.min.js');
	// $doc->addScript(\JUri::root(true) . 'administrator/components/com_t4pagebuilder/assets/js/t4pagebuilder.js');
	$doc->addStyleSheet(\JUri::root(true) .  "administrator/components/com_t4pagebuilder/assets/css/googlefonts.css");
	$doc->addScript(\JUri::root(true) .  "administrator/components/com_t4pagebuilder/assets/js/googlefonts.js");
	define('JPB_LOADER', 1);

}
$this->configFieldsets  = array('editorConfig');
$this->hiddenFieldsets  = array('basic-limited');
$this->ignore_fieldsets = array('jmetadata', 'item_associations');

$user = JFactory::getUser();
$userid = $user->id;
$content = $this->item->page_html;
$working_content = $this->item->working_content;

$app = JFactory::getApplication();
$input = $app->input;
//config view on page content
$name = $this->item->asset_name;
$asset = $this->item->asset_id;
$xml = JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/page.xml";
$this->form = JForm::getInstance('page',$xml);
// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
$fade = ($this->item->id) ? "fade" : "show";
if(empty($this->form)) $this->form = '';
?>
<form action="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="jpb-page-wrap">
		<div class="form-horizontal">
			<div class="row-fluid">
				<div class="span12">
					<fieldset class="adminform">
						<?php 
							// echo $this->form->getInput('pagetext');
						$modifier = $params->get('fullScreenMod', '') !== '' ? implode($params->get('fullScreenMod', ''), ' + ') . ' + ' : '';

							// check if string is encoded
							if (preg_match('/[<>\"]+/', $content, $match)) {
								$content = htmlentities($content);
							}

							// parse field name
							$fname = trim($name, '[]');
							$fname = preg_replace('/[\]\[]+/', '.', $fname);
							$fid = "jpb-field-" . str_replace('.', '-', $fname);

							// $t4id = JPB\Helper\Item::getRefId ($fname, $asset,$this->item->id);
							$t4id = $this->item->id;
							$htmlFiled = $this->form->getField('page_html');
							$htmlCss = $this->form->getField('css');
							$editorDisabled = empty($this->item->id) ? " disabled" : '';
							?>
							
							
							<input type="hidden" name="<?php echo $htmlFiled->name ?>" id="<?php echo $htmlFiled->id ?>" class="jpb-item" value="<?php echo htmlentities($htmlFiled->value) ?>" style="display: none" data-template="<?php echo $this->templateDefault;?>" data-name="<?php echo $fid ?>" />
							<input id="<?php echo $fid ?>" type="hidden" name="jpb-fields[<?php echo $fname ?>]" value="<?php echo $t4id; ?>" />
								<div class="jpb-edit" style="display: none;">
									<span class="btn btn-customize jpb-btn-edit<?php echo $editorDisabled;?>"><span class="fal fa-pencil"></span><strong>Edit</strong></span>
								</div>
								<div class="jpb-add-block" style="display: none;">
									<span class="btn btn-add-block jpb-btn-edit<?php echo $editorDisabled;?>">
										<span class="jpb-icon-wrap"><i class="fal fa-plus-circle"></i></span>Add block(s)</span>
								</div>
							<?php echo T4pagebuilderHelper::mediafield($fid); ?>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
	<input type="hidden" name="forcedLanguage" value="<?php echo $input->get('forcedLanguage', '', 'cmd'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<style>
	.jpb-btn-edit,
	.jpb-preview {
		display: none;
	}
</style>