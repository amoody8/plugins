<?php
/**
 *------------------------------------------------------------------------------
 * @package       T4 Page Builder for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github
 *                & Google group to become co-author)
 *------------------------------------------------------------------------------
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use JPB\Helper\Item as Item;

// dev check if existing columns js on tables
$db = JFactory::getDbo();
$db->setQuery("SHOW COLUMNS FROM #__jae_item LIKE 'js'");
$result = $db->loadResult();
if (empty($result)) {
    $db->setQuery("ALTER TABLE `#__jae_item` ADD `js` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `css`;");
    $db->execute();
}

$app = JFactory::getApplication();
$input = $app->input;
// Create shortcut to parameters.
$params = clone $this->state->get('params');
$params->merge(new Registry($this->item));
$loadfontDefaults = file_get_contents(JPB_PATH_MEDIA_BUILDER . 'googlefonts/data.json');
$loadgooglefont = $params->get('loadfonts') ? $params->get('loadfonts') : $loadfontDefaults ;
$customFont = is_file(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json') ? json_decode(file_get_contents(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json'), true) : [];
$customFonts = $customFont ? $customFont['fonts'] : [];
$loadIcons = $params->get('loadconfigicons') ? $params->get('loadconfigicons') : "{\"awesome_icons\":{\"awesome_icons\":true,\"url_type\":\"cdn\",\"custom_url\":\"null\"},\"material_icons\":{\"material_icons\":true,\"url_type\":\"cdn\",\"custom_url\":\"\"}}";

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
JHtml::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
JHtml::_('formbehavior.chosen', 'select');
if (!defined('JPB_LOADER')) {
    $t4base = \JUri::root(true) . JPB_MEDIA_BUILDER;
    $doc = \JFactory::getDocument();
    $doc->addStyleSheet($t4base . 'css/editor.css');
    $ajaxUrl = \JUri::base(true) . '/index.php?option=com_t4pagebuilder&view=page&format=json';
    $languages = t4pagebuilderHelper::buildLanguage();
    $doc->addScriptDeclaration('
      var ajax_url="' . $ajaxUrl . '";
      var t4b = window.t4b || {}; 
      t4b.langs =' . $languages . '; 
      var loadgooglefont=' . $loadgooglefont . ';
      var customFont=' . json_encode($customFonts) . '; 
      var site_root_url = "' . \JUri::root() . '"; 
      var loadIcons = ' . json_encode($loadIcons) . '; 
      var builder_url = "' . $t4base . '";
      var builderParam="' . JPB_PARAM . '";
      var jpb_devmode="'.JPB_DEVMODE.'";
      var editor_type="com_t4pagebuilder";
      var emailTemplate ="";
      var isT4 ="'.$this->isT4.'";
    ');
    $doc->addScript($t4base . 'js/loader.js');
    $doc->addScript(\JUri::root(true) . '/media/media/js/mediafield.min.js');
    $doc->addScript(JPB_PATH_BASE . 'assets/js/t4pagebuilder.js');
    $doc->addStyleSheet(JUri::base() .  "components/com_t4pagebuilder/assets/css/googlefonts.css");
    $doc->addStylesheet($t4base . 'css/fontawesome_light.css');
    $doc->addScript(JUri::base() .  "components/com_t4pagebuilder/assets/js/googlefonts.js");
    define('JPB_LOADER', 1);
}
$this->configFieldsets  = array('editorConfig');
$this->hiddenFieldsets  = array('basic-limited');
$this->ignore_fieldsets = array('jmetadata', 'item_associations');

$user = JFactory::getUser();
$userid = $user->id;
$content = $this->item->page_html;
$working_content = $this->item->working_content;

//config view on page content
$name = $this->item->asset_name;
$asset = $this->item->asset_id;
    // In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';
$fade = ($this->item->id) ? "fade" : "show";
?>
<form action="<?php echo JRoute::_('index.php?option=com_t4pagebuilder&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="jpb-page-wrap">
		<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
		<fieldset class="form-vertical">
			<?php echo $this->form->renderField('state'); ?>
			<?php echo $this->form->renderField('catid'); ?>
			<?php echo $this->form->renderField('access'); ?>
			<?php echo $this->form->renderField('language'); ?>
			<?php echo $this->form->renderField('thumb'); ?>
			<?php echo $this->form->renderField('page_key'); ?>
			<?php echo $this->form->renderField('asset_id'); ?>
			<?php echo $this->form->renderField('asset_name'); ?>
		</fieldset>
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

                            if ($asset === 'jform.articletext') {
                                $t4id = JPB\Helper\Item::getRefId($fname, $asset, $this->item->id);
                            } else {
                                $t4id = $this->item->id;
                            }
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
							<?php echo t4pagebuilderHelper::mediafield($fid); ?>
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

<?php
$link_revision = "index.php?option=com_t4pagebuilder&view=revisions&tmpl=component&id=".(int) $this->item->id;
echo JHtml::_(
    'bootstrap.renderModal',
    'RevisionsModal',
    array(
        'title'       => JText::_('COM_T4PAGEBUILDER_REVISION_MANAGER'),
        'backdrop'    => 'static',
        'keyboard'    => false,
        'closeButton' => true,
        'url'         => $link_revision,
        'height'      => '400px',
        'width'       => '800px',
        'bodyHeight'  => '70',
        'modalWidth'  => '80',
        'footer'      => '<button type="button" class="btn btn-primary" data-dismiss="modal"'
                . ' onclick="jQuery(\'#RevisionsModal iframe\').contents().find(\'#closeBtn\').click(); jQuery(\'#RevisionsModal\').data(\'act\',\'cancel\');">'
                . JText::_('Close') . '</button>',
    )
);
?>