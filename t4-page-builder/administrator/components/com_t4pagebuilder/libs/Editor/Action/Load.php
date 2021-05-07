<?php

/**
 *------------------------------------------------------------------------------
* @package       T4 Page Builder for Joomla!
*------------------------------------------------------------------------------
* @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
* @license       GNU General Public License version 2 or later; see LICENSE.txt
* @authors       JoomlArt
* @forum:          https://www.joomlart.com/forums/t/t4-builder
* @Link:         https://demo.t4-builder.joomlart.com/
*------------------------------------------------------------------------------
*/

namespace JPB\Editor\Action;

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use \Joomla\CMS\Factory as JFactory;
use \Joomla\CMS\Filesystem\Folder as JFolder;
use \Joomla\CMS\Application\ApplicationHelper as JApplicationHelper;
use Joomla\CMS\Plugin\PluginHelper as JPluginHelper;
use Joomla\CMS\Filesystem\Path as JPath;
use Joomla\CMS\Language\Text as JText;
class Load extends Base
{
    public function run()
    {
        $config = $this->getConfig();
        return $config;
    }

    private function getConfig()
    {
        $configfile = JPATH_ROOT . JPB_MEDIA_BUILDER . 'blocks/t4.json';
        $config = array();
        if (is_file($configfile)) {
            $config = \JPB\Helper\Table::decodeData(file_get_contents($configfile));
            // return $config;
        }

        //get all html blocks
        $dirs = array_filter(glob(JPATH_ROOT . JPB_MEDIA . 'html/*'), 'is_dir');
        foreach ($dirs as $dir) {
            $path = $dir . '/dist/blocks.json';
            if (is_file($path)) {
                $info = @json_decode(file_get_contents($path), true);
                if (!$info) {
                    continue;
                }
                $config = array_merge_recursive($config, $info);
            }
        }

        //get user save block html
        $blocks = \JPB\Helper\Block::loadAllUserBlocks();
        foreach ($blocks as $name => $block) {

            //need add thumbnail or get default thumbs
            $thumb = '';
            $attributes = [
                'type' => 'saveblock',
                'name' => $name,
                'class' => 'fal fa-indent',
            ];
            $config['blocks'][] = [
                'label' => $name,
                'name' => $name,
                'attributes' => (object) $attributes,
                'thumb' => '',
                'content' => $block,
                'id' => $name,
                'group' => 'UserBlock',
                'components' => [],
                'package' => 't4',
                'category' => \JPB\Helper\Block::TYPE_USER,
            ];
        }

        $blocks = \JPB\Helper\Block::loadAllShareBlocks();
        foreach ($blocks as $name => $block) {

            //need add thumbnail or get default thumbs
            $thumb = '';
            $attributes = [
                'type' => 'shareblock',
                'name' => $name,
                'class' => 'fal fa-indent',
            ];
            $regex_css = "/\<style\>(.*)\<\/style\>/i";
            preg_match($regex_css, $block, $matchs);
            $css = '';
            if (!empty($matchs[1])) {
                $css = $matchs[1];
            }
           
            $config['blocks'][] = [
                'label' => $name,
                'name' => $name,
                'thumb' => '',
                'attributes' => (object) $attributes,
                'content' => [
                    'type' => 'shareblock',
                    'tagName' => "t4:block",
                    'attributes' => [
                        'name' => $name,
                        'blockname' => $name,
                    ],
                ],
                'data_content' => $block,
                'css' => $css,
                'id' => $name,
                'group' => 'UserBlock',
                'components' => [],
                'package' => 't4',
                'category' => \JPB\Helper\Block::TYPE_SHARE,
            ];
        }

        $config['menu'] = $this->getMenu();
        $config['menutype'] = $this->getMenuType();
        $config['modules'] = $this->getModules();
        $config['positions'] = $this->getPositions();
        $config['templatestyles'] = $this->getTemplateStyles();
        $config['animation'] = $this->getAnimation();
        $config['page_css'] = $this->getPageCss();
        $config['asset_type'] = $this->getAssetType();
        $config['articles'] = $this->getArticles();
        $config['acymlist'] = $this->getAcymailingList();
        $config['api_recaptcha'] = $this->getReCaptchaApi();
        return $config;
    }
    private function getAssetType()
    {
        $input = JFactory::getApplication()->input;
        $option = $input->get('option', '');
        $view = $input->get('view', '');
        if ($option) {
            return $option;
        }
        return $view;
    }
    private function getMenu()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn(array('id', 'menutype', 'title', 'client_id'), array('id', 'value', 'name', 'client_id')))
            ->from($db->quoteName('#__menu_types'))
            ->order('client_id, title');

        $query->where('client_id = 0');

        $menus = $db->setQuery($query)->loadObjectList();
        return $menus;
    }

    private function getPositions()
    {
        // get all module positions
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
        ->select('DISTINCT position')
        ->from($db->quoteName('#__modules'))
        ->where('client_id=0')
        ->where('position != ""')
        ->order('position');
        $data_positions = $db->setQuery($query)->loadColumn();

        //get position in template
        // $curTem = JFactory::getApplication()->getTemplate();
        $tpls = JFolder::folders(JPATH_ROOT . '/templates/');
        foreach ($tpls as $tpl) {
            if ($tpl == 'system') {
                continue;
            }
            $tpl_position = [];
            $xmlfile = JPATH_ROOT . '/templates/' . $tpl . '/templateDetails.xml'; //Change to your template
            if (file_exists($xmlfile)) {
                $xml = simplexml_load_file($xmlfile);
                foreach ($xml->positions->position as $position) {
                    if (!in_array(strval($position), $data_positions)) {
                        $tpl_position[] = strval($position);
                    }
                }
            }
        }

        return array_unique(array_merge($data_positions, $tpl_position));
    }

    private function getModules()
    {
        // get all modules
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
        ->select('id, title, module')
        ->from($db->quoteName('#__modules'))
        ->where('client_id=0')
        ->where('published=1')
        ->order('title');
        return $db->setQuery($query)->loadObjectList();
    }

    private function getPageCss()
    {
        $input = JFactory::getApplication()->input;
        $id = $input->getInt('id');

        // get all modules
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
        ->select('bundle_css')
        ->from($db->quoteName('#__jae_item'))
        ->where('id = ' . $db->quote($id));
        return $db->setQuery($query)->loadResult();
    }

    private function getTemplateStyles()
    {
        // get all modules
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, CONCAT(title,IF(home=1, "*", ""), " (", template, ")") as name')
            ->from($db->quoteName('#__template_styles'))
            ->where('client_id=0');

        return $db->setQuery($query)->loadObjectList();
    }
    private function getAnimation()
    {
        $animatefile = JPATH_ROOT . JPB_MEDIA_BUILDER . 'vendors/animate/animate.json';
        if (is_file($animatefile)) {
            $animate = @json_decode(file_get_contents($animatefile), true);
        }
        return $animate;
    }
    private function getMenuType()
    {
        // get all modules
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id, title, module')
            ->from($db->quoteName('#__modules'))
            ->where('client_id=0')
            ->where('published=1 AND module = "mod_menu"')
            ->order('title');
        return $db->setQuery($query)->loadObjectList();
    }
    private function getArticles(){
        $artData = array();
        // $artData['article'] = $this->getAllActicle();
        $artData['categories'] = $this->getAllCategories();
        $artData['article_layout'] = array();
        $artData['modArt'] = array(
            array(
                "value" => "mod_articles_latest",
                "name" => "Latest Articles"
            ),/*
            array(
                "value"=> "mod_articles_archive",
                "name" => "Module Article Archive"
            ),*/
            array(
                "value"=> "mod_articles_categories",
                "name" => "Articles Categories"
            ),
             array(
                "value"=> "mod_articles_category",
                "name" => "Articles Category"
            ),
            // array(
            //     "value"=> "mod_articles_news",
            //     "name" => "Module Article news"
            // ),
            // array(
            //     "value"=> "mod_articles_popular",
            //     "name" => "Module Article popular"
            // ),

        );
        $artData['article_layout']['mod_articles_latest'] = $this->getArticleLayout('mod_articles_latest');
        $artData['article_layout']['mod_articles_categories'] = $this->getArticleLayout('mod_articles_categories');
        $artData['article_layout']['mod_articles_category'] = $this->getArticleLayout('mod_articles_category');
        return $artData;
    }
    private function getAllActicle(){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id, title')
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('state') . '='. $db->quote('1'));
        return $db->setQuery($q)->loadObjectList();
    }
    private function getAllCategories(){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('id as value, title as name')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('extension') . '='. $db->quote('com_content'))
            ->where($db->quoteName('published') . '='. $db->quote('1'));
        return $db->setQuery($q)->loadObjectList();
    }
    private function getArticleLayout($module = 'mod_articles_latest'){
        $layout = array();
        $client = JApplicationHelper::getClientInfo(0);
        // Get the database object and a new query object.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Build the query.
        $query->select('element, name')
            ->from('#__extensions as e')
            ->where('e.client_id = ' . $db->quote('0'))
            ->where('e.type = ' . $db->quote('template'))
            ->where('e.enabled = 1');
        $templates = $db->setQuery($query)->loadObjectList('element');
        $t4builder = new \stdClass();
        $t4builder->element = 't4_builder';
        $t4builder->name = 't4_builder';
        $templates['t4_builder'] = $t4builder;

        // Load language file
        $lang = JFactory::getLanguage();
        $lang->load($module . '.sys', $client->path, null, false, true)
            || $lang->load($module . '.sys', $client->path . '/modules/' . $module, null, false, true);
        // Build the search paths for module layouts.
        $module_path = JPath::clean($client->path . '/modules/' . $module . '/tmpl');
         $lang->load('com_t4pagebuilder.sys', $client->path, null, false, true);
        // Prepare array of component layouts
        $module_layouts = array();

        // Prepare the grouped list
        $groups = array();

        // Add the layout options from the module path.
        if (is_dir($module_path) && ($module_layouts = JFolder::files($module_path, '^[^_]*\.php$')))
        {
            foreach ($module_layouts as $file)
            {
                // Add an option to the module group
                $value = basename($file, '.php');
                $text = $value;
                $layout[] = JHtml::_('select.option',$value, $text);
            }
            // Create the group for the module
            $groups['_'] = array();
            $groups['_']['text'] = "---From Module---";
            $groups['_']['items'] = array();

            foreach ($module_layouts as $file)
            {
                // Add an option to the module group
                $value = basename($file, '.php');
                $text = $lang->hasKey($key = strtoupper($module . '_LAYOUT_' . $value)) ? JText::_($key) : $value;
                $groups['_']['items'][] = JHtml::_('select.option', '_:' . $value, $text);
            }
        }
        // Loop on all templates
        if ($templates)
        {
            foreach ($templates as $template)
            {
                if($template->element == 't4_builder'){
                    // Load language file
                    $lang->load('com_t4pagebuilder.sys', $client->path, null, false, true)
                    || $lang->load('tpl_' . $template->element . '.sys', $client->path . '/templates/' . $template->element, null, false, true);
                }else{
                    // Load language file
                    $lang->load('tpl_' . $template->element . '.sys', $client->path, null, false, true)
                        || $lang->load('tpl_' . $template->element . '.sys', $client->path . '/templates/' . $template->element, null, false, true);
                }

                $template_path = JPath::clean($client->path . '/templates/' . $template->element . '/html/' . $module);

                // Add the layout options from the template path.
                if (is_dir($template_path) && ($files = JFolder::files($template_path, '^[^_]*\.php$')))
                {
                    foreach ($files as $i => $file)
                    {
                        // Remove layout that already exist in component ones
                        if (in_array($file, $module_layouts))
                        {
                            unset($files[$i]);
                        }
                    }

                    if (count($files))
                    {
                        // Create the group for the template
                        $groups[$template->element] = array();
                        if($template->name == 't4_builder'){
                            $fromPath = "---From T4builder---";
                        }else{
                            $fromPath =  "---From " .$template->name. " Template---";
                        }
                        $groups[$template->element]['text'] =  $fromPath ;
                        $groups[$template->element]['items'] = array();

                        foreach ($files as $file)
                        {
                            // Add an option to the template group
                            $value = basename($file, '.php');
                            $text = $lang->hasKey($key = strtoupper('TPL_' . $template->element . '_' . $module . '_LAYOUT_' . $value))
                                ? JText::_($key) : str_replace(array("_","-"), " " , $value);
                            $groups[$template->element]['items'][] = JHtml::_('select.option', $template->element . ':' . $value, $text);
                        }
                    }
                }
            }
        }
        return $groups;
    }
    private function getAcymailingList(){
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);
        $q->select('extension_id')
            ->from('#__extensions')
            ->where('element IN ("com_acym","com_acymailing")');
        if(!empty($db->setQuery($q)->loadResult())){
            $list = $db->getQuery(true);

            if(is_file(JPATH_ADMINISTRATOR . '/components/com_acym/acym.xml')){
                $list->select('id as value,name');
                $list->from('#__acym_list');
            }else{
                $list->select('listid as value,name');
                $list->from('#__acymailing_list');
            }

            return $db->setQuery($list)->loadObjectList();
        }
    }
    private function getReCaptchaApi()
    {
        $plugin = JPluginHelper::getPlugin('captcha', 'recaptcha');
        // Check if plugin is enabled
        if ($plugin) {
            // Get plugin params
            $params = new \JRegistry($plugin->params);
            return $params->get('public_key');
        }
    }
}
