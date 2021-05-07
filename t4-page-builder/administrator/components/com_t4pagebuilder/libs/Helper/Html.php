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

namespace JPB\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\Registry\Registry as Registry;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry as JRegistry;

class Html
{
    public static function renderJdoc($html)
    {
        $matches = array();
        if (preg_match_all('#<jdoc:include\ type="([^"]+)"(.*)\/>#iU', $html, $matches)) {

            $replace = array();
            $with = array();
            $doc = JFactory::getDocument();
            // Step through the jdocs in reverse order.
            for ($i = count($matches[0]) - 1; $i >= 0; $i--) {
                $type = $matches[1][$i];
                $attribs = empty($matches[2][$i]) ? array() : \JUtility::parseAttributes($matches[2][$i]);
                $name = isset($attribs['name']) ? $attribs['name'] : null;
                $replace[] = $matches[0][$i];
                if ($matches[1][$i] == 'menu') {
                    $with[] = self::renderMenu($name, $attribs);
                }elseif ($matches[1][$i] == 'articles') {
                    $with[] = self::renderArticles($name, $attribs);;
                } else {
                    $with[] = $doc->getBuffer($type, $name, $attribs);
                }
            }

            $html = str_replace($replace, $with, $html);
        }
        return $html;
    }

    public static function sefUpdate($buffer)
    {

        // Replace src links.
        $app = JFactory::getApplication();
        $base   = Uri::base(true) . '/';

        // For feeds we need to search for the URL with domain.
        $prefix = $app->getDocument()->getType() === 'feed' ? Uri::root() : '';

        // Replace index.php URI by SEF URI.
        if (strpos($buffer, 'href="' . $prefix . 'index.php?') !== false) {
            preg_match_all('#href="' . $prefix . 'index.php\?([^"]+)"#m', $buffer, $matches);

            foreach ($matches[1] as $urlQueryString) {
                $buffer = str_replace(
                    'href="' . $prefix . 'index.php?' . $urlQueryString . '"',
                    'href="' . trim($prefix, '/') . Route::_('index.php?' . $urlQueryString) . '"',
                    $buffer
                );
            }

            self::checkBuffer($buffer);
        }

        // Check for all unknown protocals (a protocol must contain at least one alpahnumeric character followed by a ":").
        $protocols  = '[a-zA-Z0-9\-]+:';
        $attributes = array('href=', 'src=', 'poster=');

        foreach ($attributes as $attribute) {
            if (strpos($buffer, $attribute) !== false) {
                $regex  = '#\s' . $attribute . '"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
                $buffer = preg_replace($regex, ' ' . $attribute . '"' . $base . '$1"', $buffer);
                self::checkBuffer($buffer);
            }
        }

        if (strpos($buffer, 'srcset=') !== false) {
            $regex = '#\s+srcset="([^"]+)"#m';

            $buffer = preg_replace_callback(
                $regex,
                function ($match) use ($base, $protocols) {
                    preg_match_all('#(?:[^\s]+)\s*(?:[\d\.]+[wx])?(?:\,\s*)?#i', $match[1], $matches);

                    foreach ($matches[0] as &$src) {
                        $src = preg_replace('#^(?!/|' . $protocols . '|\#|\')(.+)#', $base . '$1', $src);
                    }

                    return ' srcset="' . implode($matches[0]) . '"';
                },
                $buffer
            );

            self::checkBuffer($buffer);
        }

        // Replace all unknown protocals in javascript window open events.
        if (strpos($buffer, 'window.open(') !== false) {
            $regex  = '#onclick="window.open\(\'(?!/|' . $protocols . '|\#)([^/]+[^\']*?\')#m';
            $buffer = preg_replace($regex, 'onclick="window.open(\'' . $base . '$1', $buffer);
            self::checkBuffer($buffer);
        }

        // Replace all unknown protocols in onmouseover and onmouseout attributes.
        $attributes = array('onmouseover=', 'onmouseout=');

        foreach ($attributes as $attribute) {
            if (strpos($buffer, $attribute) !== false) {
                $regex  = '#' . $attribute . '"this.src=([\']+)(?!/|' . $protocols . '|\#|\')([^"]+)"#m';
                $buffer = preg_replace($regex, $attribute . '"this.src=$1' . $base . '$2"', $buffer);
                self::checkBuffer($buffer);
            }
        }

        // Replace all unknown protocols in CSS background image.
        if (strpos($buffer, 'style=') !== false || strpos($buffer, '<style') !== false) {
            $regex_url  = '\s*url\s*\(([\'\"]|\&\#0?3[49];)?(?!/|\&\#0?3[49];|' . $protocols . '|\#)([^\)\'\"]+)([\'\"]|\&\#0?3[49];)?\)';
            $regex  = '#background(-image)?:' . $regex_url . '#m';
            $buffer = preg_replace($regex, 'background$1: url($2' . $base . '$3$4)', $buffer);
            self::checkBuffer($buffer);
        }

        // Replace all unknown protocols in OBJECT param tag.
        if (strpos($buffer, '<param') !== false) {
            // OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag.
            $regex  = '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
            $buffer = preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $buffer);
            self::checkBuffer($buffer);

            // OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag.
            $regex  = '#(<param\s+[^>]*)value\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
            $buffer = preg_replace($regex, '<param value="' . $base . '$2" name="$3"', $buffer);
            self::checkBuffer($buffer);
        }

        // Replace all unknown protocols in OBJECT tag.
        if (strpos($buffer, '<object') !== false) {
            $regex  = '#(<object\s+[^>]*)data\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
            $buffer = preg_replace($regex, '$1data="' . $base . '$2"', $buffer);
            self::checkBuffer($buffer);
        }

        // Use the replaced HTML body.
        return $buffer;
    }

    /**
     * Check the buffer.
     *
     * @param   string  $buffer  Buffer to be checked.
     *
     * @return  void
     */
    private static function checkBuffer($buffer)
    {
        if ($buffer === null) {
            switch (preg_last_error()) {
                case PREG_BACKTRACK_LIMIT_ERROR:
                    $message = 'PHP regular expression limit reached (pcre.backtrack_limit)';
                    break;
                case PREG_RECURSION_LIMIT_ERROR:
                    $message = 'PHP regular expression limit reached (pcre.recursion_limit)';
                    break;
                case PREG_BAD_UTF8_ERROR:
                    $message = 'Bad UTF8 passed to PCRE function';
                    break;
                default:
                    $message = 'Unknown PCRE error calling PCRE function';
            }

            throw new \RuntimeException($message);
        }
    }


    /**
     * Load share block
     */
    public static function loadShareBlock($buffer)
    {
        $buffer = preg_replace_callback(
            '/<T4:Block\s+name="([^\"]*)"\s*>\s*<\/T4:Block>/ims',
            # $buffer = preg_replace_callback('/<jdoc:include\s+type="t4"\s+name="block"\s+blockname="([^\"]*)"[^>]*>/ims',
            function ($match) {
                $block_content = \JPB\Helper\Block::loadShareBlock($match[1]);
                // parse <style>
                $block_content = preg_replace_callback('/<style\s*[^>]*>(.*)<\/style>/ims', function ($styles) {
                    JFactory::getDocument()->addStyleDeclaration($styles[1]);
                    return '';
                }, $block_content);

                return $block_content;
            },
            $buffer
        );
        return $buffer;
    }

    public static function voidJdoc($html)
    {
        if (!$html) {
            return '';
        }
        return preg_replace('/<jdoc:include([^\/]*)>[^<]*<\/jdoc:include>/', '<jdoc:include$1/>', $html);
    }

    public static function unvoidJdoc($html)
    {
        if (!$html) {
            return '';
        }
        return preg_replace('/<jdoc:include([^\/]*)\/>/', '<jdoc:include$1></jdoc:include>', $html);
    }
    public static function renderMenu($name, $data)
    {
        $modules = ModuleHelper::getModule('mod_menu');
        $menuType = 'mainmenu';
        if(!empty($data['menutype']) && $data['menutype'] != 'none' ){
            $menuType = $data['menutype'];
        }

        $st_level = !empty($data['st_level']) ? $data['st_level'] : 1;
        $ed_level = !empty($data['ed_level']) ? $data['ed_level'] : 0;
        $modulestyle = !empty($data['modulestyle']) ? $data['modulestyle'] : "raw";
        $params = new JRegistry();
        $params->loadString($modules->params);
        $menuLayout = "_:default";
        if(is_dir(JPATH_ROOT ."/templates/t4_builder/html/mod_menu")){
            $menuLayout = "t4_builder:default";
        }

        $params->set('layout', $menuLayout);
        $params->set('menutype', $menuType);
        $params->set('startLevel', $st_level);
        $params->set('endLevel', $ed_level);
        $params->set('showAllChildren', 1);

        // create a module object to render
        $module = new \stdClass;
        $module->params = $params;
        $module->module = 'mod_menu';
        $module->showtitle = !empty($data['menutitle']) ? 1 : 0;
        $module->id = 0;
        $module->name = 't4b menu';
        $module->title = !empty($data['menutitle']) ? $data['menutitle'] : $menuType;
        $module->position = 'none';
        $attr = array();
        $attr['style'] = $modulestyle;
        return ModuleHelper::renderModule($module, $attr);
    }
    public static function renderArticles($name, $data)
    {
        $modulestyle = !empty($data['modulestyle']) ? $data['modulestyle'] : "raw";
        $modulelayout = !empty($data['modulelayout']) ? $data['modulelayout'] : "_:default";
        $params = new JRegistry();
        $modules = ModuleHelper::getModule($name);
        $params->loadString($modules->params);
        switch ($name) {
           case "mod_articles_latest":
           $params_latest = '{"catid":[2],"count":5,"show_featured":"","ordering":"c_dsc","user_id":"0","layout":"_:default","moduleclass_sfx":"","cache":1,"cache_time":900,"cachemode":"static","module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}';
             $params->loadString($params_latest);
            $catid = !empty($data['catid']) ? explode(',',$data['catid']) : array();
            $count = !empty($data['count']) ? $data['count'] : 5;
            $show_featured = !empty($data['feature']) ? $data['feature'] : "";
            $ordering = !empty($data['order']) ? $data['order'] : "p_dsc";
            $params->set('layout', $modulelayout);
            $params->set('catid', $catid);
            $params->set('count', $count);
            $params->set('show_featured', $show_featured);
            $params->set('ordering', $ordering);
            $params->set('user_id', "0");
            $params->set('moduleclass_sfx', " t4b-mod-latest");
                break;
        case "mod_articles_archive":
            break;
            case "mod_articles_categories":
            $params_categories = '{"parent":2,"show_description":1,"numitems":1,"show_children":1,"count":0,"maxlevel":0,"layout":"_:default","item_heading":"4","moduleclass_sfx":"","owncache":1,"cache_time":900,"module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}';
                $params->loadString($params_categories);
                $catid = $data['catid'];
                $cate_desc = $data['cate_desc'];
                $num_art = $data['num_art'];
                $show_subcat = $data['show_subcat'];
                $params->set('layout', $modulelayout);
                $params->set('parent', $catid);
                $params->set('show_description', $cate_desc);
                $params->set('numitems', $num_art);
                $params->set('show_children', $show_subcat);
                break;
            case "mod_articles_category":
                $params_category = '{"mode":"normal","show_on_article_page":1,"count":4,"show_front":"show","category_filtering_type":1,"catid":[2],"show_child_category_articles":0,"levels":1,"author_filtering_type":1,"author_alias_filtering_type":1,"excluded_articles":"","date_filtering":"off","date_field":"a.created","start_date_range":"","end_date_range":"","relative_date":30,"article_ordering":"a.title","article_ordering_direction":"ASC","article_grouping":"none","date_grouping_field":"created","month_year_format":"F Y","article_grouping_direction":"ksort","link_titles":1,"show_date":1,"show_date_field":"created","show_date_format":"d. M","show_category":0,"show_hits":0,"show_author":0,"show_tags":0,"show_introtext":1,"introtext_limit":100,"show_readmore":1,"show_readmore_title":0,"readmore_limit":15,"layout":"_:default","moduleclass_sfx":"","owncache":1,"cache_time":900,"module_tag":"div","bootstrap_size":"0","header_tag":"h3","header_class":"","style":"0"}';
                $params->loadString($params_category);
                $params->set('layout', $modulelayout);
                $catid = !empty($data['catid']) ? explode(',',$data['catid']) : array();
                $count = !empty($data['count']) ? $data['count'] : 0;
                $show_featured = !empty($data['feature']) ? $data['feature'] : "show";
                $article_ordering = !empty($data['article_ordering']) ? $data['article_ordering'] : "a.title";
                $article_ordering_direction = !empty($data['article_ordering_direction']) ? $data['article_ordering_direction'] : "ASC";
                $params->set('catid', $catid);
                $params->set('count', $count);
                $params->set('article_ordering', $article_ordering);
                $params->set('article_ordering_direction', $article_ordering_direction);


                if(!empty($data['child_cat'])){
                    $params->set('show_child_category_articles', 1);
                    $params->set('levels', $data['child_cat_levels']);
                }else{
                    $params->set('show_child_category_articles', 0);
                }
                if(!empty($data['date'])){
                    $params->set('show_date', 1);
                    $params->set('show_date_field', $data['date_field']);
                    $params->set('show_date_format', $data['date_format']);
                }else{
                    $params->set('show_date', 0);
                }
                if(!empty($data['cat_introtext'])){
                    $params->set('show_introtext', 1);
                    $params->set('introtext_limit', $data['introtext_limit']);
                }else{
                    $params->set('show_introtext', 0);
                }
                if(!empty($data['show_readmore'])){
                    $params->set('show_readmore', 1);
                }else{
                    $params->set('show_readmore', 0);
                }
                if(!empty($data['show_cat'])){
                    $params->set('show_category', 1);
                }else{
                    $params->set('show_category', 0);
                }
                if(!empty($data['show_hits'])){
                    $params->set('show_hits', 1);
                }else{
                    $params->set('show_hits', 0);
                }
                if(!empty($data['show_tags'])){
                    $params->set('show_tags', 1);
                }else{
                    $params->set('show_tags', 0);
                }
                if(!empty($data['show_introimg'])){
                    $params->set('show_introimg', 1);
                }else{
                    $params->set('show_introimg', 0);
                }
                if(!empty($data['show_author'])){
                    $params->set('show_author', 1);
                }else{
                    $params->set('show_author', 0);
                }
                // echo '<pre>: '. print_r( $params, true ) .'</pre>';die;
                
                break;
            case "mod_articles_news":
                break;
            case "mod_articles_popular":
                break;
            default:
                break;
        }/*
        $params->set('cache', 1); 
        $params->set('cache_time', 900);
        $params->set('cachemode', "static");
        $params->set('module_tag', "div");
        $params->set('bootstrap_size', "0");
        $params->set('header_tag', "h3");
        $params->set('header_class', "");
        $params->set('style', 0);*/
        // create a module object to render
        $module = new \stdClass;
        $module->params = $params;
        $module->module = $name;
        $module->id = 0;
        $module->name = $data['title'];
        $module->showtitle = !empty($data['showtitle']) ? $data['showtitle'] : 0;
        $module->title = $data['title'];
        $module->position = 'none';

        $attr = array();
        $attr['style'] = $modulestyle;
        return ModuleHelper::renderModule($module, $attr);
    }
}
