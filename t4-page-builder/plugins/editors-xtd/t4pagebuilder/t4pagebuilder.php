<?php
/**
 * @package     Joomlart.Plugin
 * @subpackage  Editors-xtd.t4pagebuilder
 *
 * @copyright   Copyright (C) 2005 - 2019 JoomlArt. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use \JPB\Helper as T4Helper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Editor T4 Pagebuilder buton
 *
 * @since  3.x
 */
class PlgButtonT4pagebuilder extends CMSPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.x
     */
    protected $autoloadLanguage = true;

    /**
     * Display the button.
     *
     * @param   string   $name    The name of the button to display.
     * @param   string   $asset   The name of the asset being edited.
     * @param   integer  $author  The id of the author owning the asset being edited.
     *
     * @return  JObject  The button options as JObject or false if not allowed
     *
     * @since   1.5/Users/kienduong/works/joom/ja_editor/plugins/editors-xtd/t4pagebuilder/t4pagebuilder.php
     */
    public function onDisplay($name, $asset, $author)
    {
        $app = JFactory::getApplication();

        $assets = self::getAssets($name);
        if (empty($assets->name)) {
            return;
        }
        JHtml::_('behavior.core');
        if (version_compare(JVERSION, '4', 'ge')) {
            $app->getDocument()->getWebAssetManager()->useScript('webcomponent.field-media');
        } else {
            JHtml::_('script', 'media/media/js/mediafield.min.js', array('version' => 'auto', 'relative' => false));
        }
        JHtml::_('script', 'plugins/editors-xtd/t4pagebuilder/assets/js/button.js', array('version' => 'auto', 'relative' => false));
        JHtml::_('stylesheet', 'plugins/editors-xtd/t4pagebuilder/assets/css/style.css', array('version' => 'auto', 'relative' => false));
        $params = JComponentHelper::getParams('com_t4pagebuilder');
        $loadfontDefaults = file_get_contents(JPATH_ROOT . '/media/t4pagebuilder/builder/googlefonts/data.json');
        $loadgooglefont = $params->get('loadfonts') ? $params->get('loadfonts') : $loadfontDefaults ;
        $loadIcons = $params->get('loadconfigicons') ? $params->get('loadconfigicons') : "{\"awesome_icons\":{\"awesome_icons\":true,\"url_type\":\"cdn\",\"custom_url\":\"null\"},\"material_icons\":{\"material_icons\":true,\"url_type\":\"cdn\",\"custom_url\":\"\"}}";

        $pageDetails = self::getPageDetails($assets->id, str_replace("_", ".", $name));

        if (empty($pageDetails)) {
            $pageDetails = new stdclass();
            $pageDetails->id = 0;
            $pageDetails->status = false;
        }
        $buttons = false;
        $lang = JFactory::getLanguage();
        $extension = 'com_t4pagebuilder';
        $base_dir = JPATH_ADMINISTRATOR;
        $language_tag = 'en-GB';
        $reload = true;
        $lang->load($extension, $base_dir, $language_tag, $reload);
        JLoader::register('T4pagebuilderHelper', JPATH_ADMINISTRATOR . '/components/com_t4pagebuilder/helpers/t4pagebuilder.php');
                
        // Pass some data to javascript
        JFactory::getDocument()->addScriptOptions(
            'xtd-t4pagebuilder',
            array(
                'exists' => JText::_('PLG_READMORE_ALREADY_EXISTS', true),
                't4b-builder' 	=> (Object)array(
                    'element_name' 	=> $name,
                    "front_end"		=> JFactory::getApplication()->isClient('site'),
                    'ajax_url' 		=>  \JUri::base(true)."/index.php?option=com_t4pagebuilder&view=page&format=json",
                    'builderParam' 	=> "act",
                    'jabuilderPath' => \JUri::root()."administrator/components/com_t4pagebuilder/",
                    'builderUrl' 	=> \JUri::root(true)."/media/t4pagebuilder/builder/",
                    "baseUrl"		=> JUri::Base(true),
                    "asset_id" 		=> $assets->id,
                    "asset_name" 	=> $assets->name,
                    'buttons' 		=> $this->_displayButtons($assets->id, $buttons, $asset, $author),
                    'page_id' 		=> $pageDetails->id,
                    't4editor_status'=> $pageDetails->status,
                    'siteUrl' 		=> JUri::root(),
                    'devmode' 		=> defined('JPB_DEVMODE') ? defined('JPB_DEVMODE') : false,
                    'loadgooglefont' => $loadgooglefont,
                    'loadIcons' 	=> $loadIcons,
                    't4blangs' 	=>  t4pagebuilderHelper::buildLanguage(),
                    'templateDefault' => self::getTemplateDefault(),
                    'user_id' 		=> JFactory::getUser()->id,
                    'mediaField' 	=> self::mediaField('jpb-field-'.str_replace("jform_", "", $name)),

                )
            )
        );
        JText::script("T4_PAGE_BUIDLER_BUTTON_DISABLED");
        JText::script("T4_PAGE_BUIDLER_BUTTON_ENABLED");
        JText::script("T4_PAGE_BUIDLER_BUTTON_DISABLED_WARNING");
        JText::script("T4_PAGE_BUIDLER_BUTTON_EDIT");
        JText::script("T4_PAGE_BUIDLER_WARNING_CREATE_NEW_ARTICLE");
        JText::script("T4_PAGE_BUIDLER_WARNING_CREATE_NEW_CATEGORY");
        JText::script("T4_PAGE_BUIDLER_WARNING_CREATE_NEW_MOD");
        JText::script("T4_PAGE_BUIDLER_BUTTON_DISABLED");
        JText::script("T4_PAGE_BUIDLER_SAVED");
        JText::script("T4_PAGE_BUIDLER_SAVE_ERROR");
        $button = new JObject;
        $button->modal   = false;
        $button->class   = 'btn t4b-toogle-editor';
        $button->onclick = "Joomla.t4PageBuilder.toggleT4Builder('" . $name . "');return false;";
        $button->text    = JText::_('PLG_T4PAGEBUILDER_BUTTON_LABEL');
        $button->name    = 'arrow-down';
        $button->link    = '#';

        return "";
    }
    protected function getPageDetails($asset_id, $asset_name)
    {
        if (!$asset_id) {
            return "";
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id,state as status')
            ->from('#__jae_item')
            ->where('asset_name = ' . $db->quote($asset_name))
            ->where('asset_id = ' . $db->quote($asset_id));
        $db->setQuery($query);

        return $db->loadObject();
    }
    protected function getTemplateDefault()
    {
        // get all modules
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__template_styles'))
            ->where('client_id=0&&home=1');

        return $db->setQuery($query)->loadResult();
    }
    protected function mediaField($fid)
    {
        $asset = 'com_template';
        $authorId = JFactory::getUser()->id;
        $mediaUrlj3 = (JFactory::getApplication()->isClient('site') ? '' : "") . "index.php?option=com_media&view=images&tmpl=component&asset={$asset}&author={$authorId}&fieldid={field-media-id}&ismoo=0&folder=";
        $mediaUrlj4 = (JFactory::getApplication()->isClient('site') ? '' : "") . "index.php?option=com_media&amp;tmpl=component&amp;asset={$asset}&amp;author={$authorId}&amp;fieldid={field-media-id}&amp;path=";
        $mediaId = $fid . '_media';
        // $mediaId = $this->id;
        $outputj3 = '<joomla-field-media id="t4-media-joomla" class="field-media-wrapper" data-basepath="'.\JUri::root().'" data-url="'.$mediaUrlj3.'" data-modal=".modal" data-modal-width="100%" data-modal-height="400px" data-input=".field-media-input" data-button-select=".button-select" data-button-clear=".button-clear" data-button-save-selected=".button-save-selected" data-preview="true" data-preview-as-tooltip="true" data-preview-container=".field-media-preview" data-preview-width="200" data-preview-height="200">
					<div id="imageModal_'.$mediaId.'" tabindex="-1" class="modal hide fade" style="display: none;" aria-hidden="true">
						<div class="modal-header">
							<button type="button" class="close novalidate" data-dismiss="modal">×</button>
									<h3>Select Image</h3>
						</div>
						<div class="modal-body" style="max-height: initial; overflow-y: initial;"></div>
						<div class="modal-footer">
							<a class="btn" data-dismiss="modal">Cancel</a>
						</div>
					</div>
					<div class="input-prepend input-append" style="display:none;">
						<input name="jform[t4][layout_media]" id="'.$mediaId.'" value="" readonly="readonly" class="input-small hasTooltip field-media-input" data-original-title="" title="">
						<a class="btn add-on button-select">Select</a>
					</div>
				</joomla-field-media>';
        $outputj4 = '<joomla-field-media id="t4-media-joomla" class="field-media-wrapper" type="image" base-path="'.\JUri::root().'" root-folder="images" url="'.$mediaUrlj4.'" modal-container=".modal" modal-width="100%" modal-height="400px" input=".field-media-input" button-select=".button-select" button-clear=".button-clear" button-save-selected=".button-save-selected" style="position: relative; z-index: 10000000;">
				<div id="imageModal_'.$mediaId.'" role="dialog" tabindex="-1" class="joomla-modal modal fade" data-url="'.$mediaUrlj4.'" data-iframe="<iframe class=&quot;iframe&quot; src=&quot;'.$mediaUrlj4.'&quot; name=&quot;Change Image&quot; height=&quot;100%&quot; width=&quot;100%&quot;></iframe>">
					<div class="modal-dialog modal-lg jviewport-width80" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h3 class="modal-title">Change Image</h3>
								<button type="button" class="close novalidate" data-dismiss="modal" aria-label="Cancel">
									<span aria-hidden="true">×</span>
								</button>
							</div>
							<div class="modal-body jviewport-height60"></div>
							<div class="modal-footer">
								<button class="btn btn-secondary button-save-selected">Select</button>
								<button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							</div>
						</div>
					</div>
				</div>
				<div class="input-group" style="display: none;">
					<input name="jform[t4][layout_media]" id="'.$mediaId.'" value="" readonly="readonly" class="form-control hasTooltip field-media-input" type="text">
					<div class="input-group-append">
						<a class="btn btn-secondary button-select">Select</a>
						<a class="btn btn-secondary hasTooltip button-clear" title="Clear"><span class="fa fa-times" aria-hidden="true"></span></a>
					</div>
				</div>
			</joomla-field-media>';
        $output = (\Joomla\CMS\Version::MAJOR_VERSION <= 3) ? $outputj3 : $outputj4;
        return $output;
    }
    protected function getAssets($name)
    {
        $input = JFactory::getApplication()->input;

        $options = $input->get('option', 'com_t4pagebuilder');
        $view = $input->get('view', 'category');
        $assets = new stdclass();
        $isSite = JFactory::getApplication()->isClient('site');

        if ($options == 'com_t4pagebuilder' && $view == 'category') {
            $assets->id = JFactory::getApplication()->input->getInt('id', "");
            $assets->name = "";//jform.description
        } else {
            $assets->id = JFactory::getApplication()->input->getInt('id', "");
            $assets->name = str_replace("_", ".", $name);
        }

        switch ($options) {
            case 'com_virtuemart':
                $assets->id = JFactory::getApplication()->input->getInt('virtuemart_product_id');
                if (!$name) {
                    $assets->name = "vm.description";
                }
                break;
            case 'com_content':
                $assets->id = $isSite ? $input->getInt('a_id') : $input->getInt('id', "");
                if (!$name) {
                    $assets->name = "jform.articletext";
                }
                break;
            case 'com_categories':
                $assets->id = $isSite ? JFactory::getApplication()->input->getInt('a_id') : JFactory::getApplication()->input->getInt('id', "");
                $assets->name = ""; //jform.description
                break;
            case 'com_modules':
                $assets->id = $isSite ? JFactory::getApplication()->input->getInt('a_id') : JFactory::getApplication()->input->getInt('id', "");
                if (!$name) {
                    $assets->name = "jform.content";
                }
                break;
            
            default:
                break;
        }
        return $assets;
    }
    /**
     * Displays the editor buttons.
     *
     * @param   string  $name     The control name.
     * @param   mixed   $buttons  [array with button objects | boolean true to display buttons]
     * @param   string  $asset    The object asset
     * @param   object  $author   The author.
     *
     * @return  void|string HTML
     */
    protected function _displayButtons($name, $buttons, $asset, $author)
    {
        // support later

        $return = '';
        
        if (is_array($buttons) || (is_bool($buttons) && $buttons)) {
            if (method_exists($this, 'getDispatcher')) {
                $buttonsEvent = new Event(
                    'getButtons',
                    [
                        'editor'    => $name,
                        'buttons' => $buttons,
                    ]
                );

                $buttonsResult = $this->getDispatcher()->dispatch('getButtons', $buttonsEvent);
                $buttons       = $buttonsResult['result'];
            } else {
                $buttons = $this->_subject->getButtons($name, $buttons, $asset, $author);
            }
            foreach ($buttons as &$button) {
                $cls = $button->get('class');
                $cls = $cls ? $cls . ' t4-xtd-button' : 't4-xtd-button';
                $button->set('class', $cls);
            }
            return JLayoutHelper::render('joomla.editors.buttons', $buttons);
        }
    }
}
