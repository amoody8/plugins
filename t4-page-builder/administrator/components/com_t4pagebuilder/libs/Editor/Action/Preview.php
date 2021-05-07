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
namespace JPB\Editor\Action;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;

class Preview extends Base
{
    protected $css = [];
    protected $stylesheets = [];
    protected $scripts = [];

    public function run()
    {
        $input = JFactory::getApplication()->input;
        $this->itemId = $input->getInt('id');
        $working = $input->getBool('working');
        $working = true;
        $tempid = $input->get('template');
        //$row = $this->helper->getItem($this->itemId);
        $row = \JPB\Helper\Item::load($this->itemId);
        $data = $row ? json_decode($working ? $row->working_content : $row->content, true) : [];
        if (!$data) {
            $data = [];
        }
        if ($data && $row->bundle_css) {
            $data['page_css'] = $row->bundle_css;
        }
        $data['html'] = !empty($data['html']) ? \JPB\Helper\Html::unvoidJdoc($data['html']) : (($row->asset_name == "pagetext") ? \JPB\Helper\Html::unvoidJdoc($row->page_html) : "");
        $data['html'] = !empty($data['html']) ? \JPB\Helper\Html::unvoidJdoc($data['html']) : (($row->asset_name == "pagetext") ? \JPB\Helper\Html::unvoidJdoc($row->page_html) : "");
        if (empty($data['css'])) {
            $data['css'] = isset($row->css) ? $row->css : '*{}';
        }
        // get head
        $base = \JUri::root(true);
        $t4base = $base . JPB_MEDIA_BUILDER;
        if ($tempid) {
            $template = \JPB\Helper\Item::getTemplateInfo($tempid);
            if (empty($template)) {
                $temp_name = JFactory::getApplication()->getTemplate();
            } else {
                $temp_name = $template->name;
            }
            $this->addStylesheet($base . '/templates/' . $temp_name . '/css/template.css');
        } else {
            $this->addStylesheet($t4base . 'css/ja_pagebuilder.css');
        }
        $this->addStylesheet($t4base . 'css/elements.css');
        $this->addStylesheet($t4base . 'css/editor-canvas.css');
        // $this->addStylesheet($t4base . 'css/editor-preview.css');

        if (isset($data['css'])) {
            // $this->addCss($data['css']);
            $this->addCss($data['css']);
        }

        // assets
        if (isset($data['assets'])) {
            $assets = $data['assets'];
            if (isset($assets['css'])) {
                foreach ($assets['css'] as $link) {
                    $url = preg_match('/^http/', $link) ? $link : $t4base . $link;
                    $this->addStylesheet($url);
                }
            }

            if (isset($assets['js'])) {
                foreach ($assets['js'] as $link) {
                    $url = preg_match('/^http/', $link) ? $link : $t4base . $link;
                    // $this->addScript($url);
                    $this->addScript($url);
                }
            }
        }

        // replace void tag jdoc:include to full tag
        $html = '';
        if (isset($data['html'])) {
            $html = $data['html'];
            $html = \JPB\Helper\Html::loadShareBlock($html);
            $html = \JPB\Helper\Html::unvoidJdoc($html);
            // add css declare into css
            $style = JFactory::getDocument()->_style;
            if (is_array($style)) {
                foreach ($style as $css) {
                    if (!is_array($css)) {
                        $this->addCss($css);
                    }
                }
            }
        }

        return [
            'data' => $data,
            'head' => $this->getHead(),
            'styles' => $this->stylesheets,
            'css' => $this->css,
            'scripts' => $this->scripts,
            'html' => $html
        ];
    }

    protected function addCss($css)
    {
        $this->css[] = $css;
    }

    protected function addStylesheet($url)
    {
        $this->stylesheets[] = $url;
    }

    protected function addScript($url)
    {
        $this->scripts[] = $url;
    }

    protected function getHead()
    {
        $head = '';
        $doc = JFactory::getDocument();

        foreach ($this->stylesheets as $url) {
            $head .= "<link type=\"text/css\" rel=\"stylesheet\" href=\"{$url}\">\n";
        }

        if (count($this->css)) {
            $head .= "<style>\n" . implode("\n", $this->css) . "\n</style>\n";
        }

        foreach ($this->scripts as $url) {
            $head .= "<script type=\"text/javascript\" src=\"{$url}\"></script>\n";
        }
        return $head;
    }
}
