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

/**
 * HTML Page View class for the T4 Page builder component
 *
 * @since  1.5
 */
class T4pagebuilderViewPage extends JViewLegacy
{
    protected $item;

    protected $params;

    protected $print;

    protected $state;

    protected $user;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        $app        			= JFactory::getApplication();
        $user       			= JFactory::getUser();
        $doc 					= JFactory::getDocument();

        $this->item  			= $this->get('Item');
        $this->item->text       = $this->item->page_html;
        $this->print 			= $app->input->getBool('print');
        $this->state 			= $this->get('State');
        $this->user  			= $user;
        $this->templateDefault 	= $app->getTemplate('template')->id;
        $this->params 			= $this->state->get('params', '');
        $this->plg_t4   		= defined("T4_PLUGIN") ? true : false;
        $active       			= $app->getMenu()->getActive();
        $this->loadlegacy  		= $this->params->get('loadlegacy', 0);
        $this->bt4 				= false;
        $this->t4 				= $this->get('t4');
        JHtml::_('jquery.framework');
        JHtml::_('behavior.keepalive');
        $item = $this->item;
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));
            return false;
        }

        $access = $user->getAuthorisedViewLevels();
        if (empty($this->item) && !in_array($this->item->access, $access)) {
            if ($this->user->get('guest')) {
                $return = base64_encode(JUri::getInstance());
                $login_url_with_return = JRoute::_('index.php?option=com_users&view=login&return=' . $return);
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
                $app->redirect($login_url_with_return, 403);
                return;
            } else {
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                $app->setHeader('status', 403, true);

                return;
            }
        }

        $groups_can_view_unpublish = array(
            '6' => '6',
            '7' => '7',
            '8' => '8'
        );
        if ($this->item->state == 0 && count(array_intersect($user->groups, $groups_can_view_unpublish)) == 0) {
            //return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            
            $app->setHeader('status', 403, true);
            
            return;
        }
        $offset = $this->state->get('list.offset');
        /**
         * Check for no 'access-view',
         * - Redirect guest users to login
         * - Deny access to logged users with 403 code
         * NOTE: we do not recheck for no access-view + show_noauth disabled ... since it was checked above
         */
        if ($this->item->params->get('access-view') == false) {
            if ($this->user->get('guest')) {
                $return = base64_encode(JUri::getInstance());
                $login_url_with_return = JRoute::_('index.php?option=com_users&view=login&return=' . $return);
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
                $app->redirect($login_url_with_return, 403);
            } else {
                $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                $app->setHeader('status', 403, true);

                return;
            }
        }

        // Process the content plugins.
        // Process the content plugins.
        JPluginHelper::importPlugin('content');
        $this->_triggerEvent('onContentPrepare', array ('com_t4pagebuilder.page', &$item, &$item->params, $offset));

        $item->event = new stdClass;
        $results = $this->_triggerEvent('onContentAfterTitle', array('com_t4pagebuilder.page', &$item, &$item->params, $offset));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $this->_triggerEvent('onContentBeforeDisplay', array('com_t4pagebuilder.page', &$item, &$item->params, $offset));
        $item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $this->_triggerEvent('onContentAfterDisplay', array('com_t4pagebuilder.page', &$item, &$item->params, $offset));
        $item->event->afterDisplayContent = trim(implode("\n", $results));

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

        $this->_prepareDocument();

        parent::display($tpl);
    }
    protected function _triggerEvent($event, $arr)
    {
        if (version_compare(JVERSION, '4', 'ge')) {
            $results = JFactory::getApplication()->triggerEvent($event, $arr);
        } else {
            $dispatcher = JEventDispatcher::getInstance();
            $results = $dispatcher->trigger($event, $arr);
        }
        return $results;
    }

    /**
     * Prepares the document.
     *
     * @return  void
     */
    protected function _prepareDocument()
    {
        $app     = JFactory::getApplication();
        $menus   = $app->getMenu();
        $pathway = $app->getPathway();
        $params = $this->params;
        $bootstrap4 = $params->get('bootstrap4') ? $params->get('bootstrap4') : 0;
        $icons = $params->get('loadconfigicons') ? $params->get('loadconfigicons') : '{"awesome_icons":{"awesome_icons":true,"url_type":"cdn","custom_url":"null"},"material_icons":{"material_icons":true,"url_type":"cdn","custom_url":""}}';
        $bootstrap_assigned = $params->get('btAssigned') ? json_decode($params->get('btAssigned')) : [];
        $title   = null;
        /**
         * Because the application sets a default page title,
         * we need to get it from the menu item itself
         */
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
        }

        $title = $this->params->get('page_title', '');

        $id = (int) @$menu->query['id'];

        // If the menu item does not concern this article
        if ($menu && ($menu->query['option'] !== 'com_t4pagebuilder' || $menu->query['view'] !== 'page' || $id != $this->item->id)) {
            // If a browser page title is defined, use that, then fall back to the article title if set, then fall back to the page_title option
            $title = $this->item->params->get('page_title', $this->item->title ?: $title);
        }

        // Check for empty title and add site name if param is set
        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        if (empty($title)) {
            $title = $this->item->title;
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        if ($app->get('MetaAuthor') == '1') {
            $author = $this->item->created_by ?: $this->item->author;
            $this->document->setMetaData('author', $author);
        }

        // If there is a pagebreak heading or title, add it to the page title
        if (!empty($this->item->page_title)) {
            $this->item->title = $this->item->title . ' - ' . $this->item->page_title;
            $this->document->setTitle(
                $this->item->page_title . ' - ' . JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1)
            );
        }
        if (!$this->plg_t4 || !$this->t4) {
            if ($bootstrap4 == 0) {
                $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/css/bootstrap.min.css');
                $this->bt4 = true;
            } elseif ($bootstrap4 < 0) {
                if (in_array(-$menu->id, $bootstrap_assigned)) {
                    $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/css/bootstrap.min.css');
                    $this->bt4 = true;
                }
            } elseif ($bootstrap4 > 0) {
                if (in_array($menu->id, $bootstrap_assigned)) {
                    $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/css/bootstrap.min.css');
                    $this->bt4 = true;
                }
            }
        }
        if ($this->bt4) {
            JHtml::_('bootstrap.framework');
            $this->document->addScript(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/bootstrap4/js/bootstrap.bundle.js');
        }
        if (is_file(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json')) {
            self::loadCustomFont();
        }
        $this->_loadOther($params);
        $this->loadGoogleFonts();
        $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/ja_pagebuilder.css');
        $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/elements.css');
        $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/site.css');
        $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/animate/animate.css');
        $this->document->addStyleSheet(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/animate/t4b-animation.css');
        $this->document->addScript(\JUri::Base(true).'/media/t4pagebuilder/builder/vendors/animate/t4b-animation.js');
        $this->document->addScript(\JUri::Base(true).'/media/t4pagebuilder/builder/js/t4pagebuilder.js');
        $this->_loadFontIcons($icons);

        if (isset($this->item->css) || isset($this->item->bundle_css)) {
            $protocols  = '[a-zA-Z0-9\-]+:';
            // Replace src links.
            $base   = JUri::base(true) . '/';
            $regex_url  = '\s*url\s*\(([\'\"]|\&\#0?3[49];)?(?!\/|\&\#0?3[49];|' . $protocols . '|\#)([^\)\'\"]+)([\'\"]|\&\#0?3[49];)?\)';
            $regex  = '#' . $regex_url . '#m';
            $css = "";
            if ($this->item->bundle_css) {
                $css = $this->item->bundle_css;
            }
            $css .= (string) $this->item->css;

            if ($css) {
                $css = preg_replace($regex, 'url($1' . $base. '$2$3)', $css);
                preg_match_all($regex, $this->item->css, $matches);
                $this->document->addStyleDeclaration($css);
            }
        }
        if ($this->print) {
            $this->document->setMetaData('robots', 'noindex, nofollow');
        }
    }
    protected function loadCustomFont()
    {
        static $customFonts = null;
        static $loaded = [];
        if ($customFonts === null) {
            if (is_file(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json')) {
                $customFonts = json_decode(file_get_contents(JPB_PATH_MEDIA_BUILDER . 'etc/'.$this->item->id.'/customfonts.json'), true);
            } else {
                return;
            }
        }
        if (empty($customFonts['fonts'])) {
            return;
        }
        $names = array_keys($customFonts['fonts']);
        foreach ($names as $name) {
            $font = $customFonts['fonts'][$name];
            if (in_array($font['url'], $loaded)) {
                continue;
            }
            $loaded[] = $font['url'];
            if (!empty($font['type']) && $font['type'] == 'css') {
                if (!preg_match("/[^\/.\s+]/", $font['url'])) {
                    $font['url'] = '/'.$font['url'];
                }
                $font_url = \JUri::root(true) . $font['url'];
                // load css file
                $this->document->addStylesheet($font_url);
            } else {
                // add css declaration
                $css = '@font-face {';
                $css .= "font-family: '$name';";
                $css .= "src: url('{$font['url']}');";
                $css .= "}";

                $this->document->addStyleDeclaration($css);
            }
        }
    }
    protected function loadGoogleFonts()
    {
        $doc = JFactory::getDocument();
        // Load google/custom font in field with suffix _font_family
        $googleFonts = $this->params->get('loadfonts') ? json_decode($this->params->get('loadfonts')): [];

        $family = [];
        foreach ($googleFonts as $font => $weights) {
            $weight = implode(',', $weights->weight);
            $family[] = $font . ($weight == '400' ? '' : ':' . $weight);
        }

        if (count($family)) {
            $doc->addStylesheet('https://fonts.googleapis.com/css?family=' . urlencode(implode('|', $family)));
        }
    }
    protected function _loadFontIcons($data)
    {
        if (!$data) {
            return;
        }
        $doc = JFactory::getDocument();

        $fData = json_decode($data, true);
        $aws = $fData['awesome_icons'];
        $material = $fData['material_icons'];
        if ($aws['awesome_icons'] == false && $material['material_icons'] == false) {
            return;
        }
        if ($aws['awesome_icons'] && $aws['url_type'] == 'cdn') {
            if (!$this->t4) {
                $doc->addStylesheet(\JUri::Base(true).'/media/t4pagebuilder/builder/css/awesome_5.11.2.min.css');
            }
        } elseif ($aws['awesome_icons'] &&$aws['url_type'] == 'url') {
            if ($aws['custom_url']) {
                $doc->addStylesheet($aws['custom_url']);
            }
        }
        if ($material['material_icons'] && $material['url_type'] == 'cdn') {
            $doc->addStyleSheet('https://fonts.googleapis.com/icon?family=Material+Icons');
        } elseif ($material['material_icons'] && $material['url_type'] == 'url') {
            if ($material['custom_url']) {
                $doc->addStylesheet($material['custom_url']);
            }
        }
    }
    protected function _loadOther($params)
    {
        $otherCss = $params->get('loadothercss', '');
        $otherJs = $params->get('loadotherjs', '');
        if ($otherJs) {
            $arrJs = explode("\n", $otherJs);
            foreach ($arrJs as $js) {
                $this->document->addScript(trim($js));
            }
        }
        if ($otherCss) {
            $arrCss = explode("\n", $otherCss);
            foreach ($arrCss as $css) {
                $this->document->addStyleSheet(trim($css));
            }
        }
    }
}
