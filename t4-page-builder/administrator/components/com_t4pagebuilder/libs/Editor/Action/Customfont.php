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

use \Joomla\CMS\Factory as JFactory;
use \Joomla\CMS\Language\Text as JText;
use \Joomla\CMS\Uri\Uri as JUri;
use \Joomla\CMS\Filesystem\Folder as JFolder;
use \Joomla\CMS\Filesystem\File as JFile;

class Customfont extends Base
{
    public function run()
    {
        $task = JFactory::getApplication()->input->getVar('task');
        if ($task == 'add') {
            $this->addFont();
        } elseif ($task == 'remove') {
            $this->removeFont();
        }
    }
    public static function addFont()
    {
        $warning_alert = false;
        $font = JFactory::getApplication()->input->post->getVar('fonts');

        // current addon
        $fontsfile = JPB_PATH_MEDIA_BUILDER . 'etc/'.$font["pageid"].'/customfonts.json';
        $fonts = is_file($fontsfile) ? json_decode(file_get_contents($fontsfile), true) : ['name' => 'font custom'];
        if (empty($fonts['fonts'])) {
            $fonts['fonts'] = [];
        }
        if (!is_dir(dirname($fontsfile))) {
            JFolder::create(dirname($fontsfile));
        }

        $output = [
            'ok' => 1,
            'fonts' => $font
        ];
        if (empty($font['type'])) {
            return $output = ['error' => JText::_('T4_CUSTOM_FONT_SAVE_ERROR')];
        }
        if ($font['type'] == 'css') {
            $fontCss = [];
            foreach ($font['css'] as $css) {
                if (strpos($css, 'http') === false) {
                    $css = str_replace('\\', '/', $css);
                    if(substr($css, 0, 1) == '/'){
                        $css_link = substr($css, 1);
                    }else {
                        $css_link = $css;
                    }
                    $css_url = JPATH_ROOT . '/' . $css_link;
                } else {
                    $css_url = $css;
                }
                $content = file_get_contents($css_url);
                if (empty($content)) {
                    return $output = ['error' => JText::_('T4_CUSTOM_FONT_SAVE_ERROR')];
                }
                preg_match_all('/@font\-face\s*\{([^}]*)\}/mu', $content, $matches);
                $fontsElems = [];
                foreach ($matches[1] as $match) {
                    if (preg_match_all('/font\-(family|style|weight):\s*(.*);/', $match, $matches2)) {
                        $font_match = [];

                        for ($i=0; $i < count($matches2); $i++) {
                            if (!empty($matches2[1][$i])) {
                                $font_match[$matches2[1][$i]] = trim($matches2[2][$i], '\'"');
                            }
                        }

                        $family = $font_match['family'];
                        $weight = '400';
                        if (!empty($font_match['weight'])) {
                            $weight = $font_match['weight'];
                        }
                        if (!empty($font_match['style']) && $font_match['style'] != 'normal') {
                            $weight .= substr($font_match['style'], 0, 1);
                        }

                        if (empty($fontsElems[$family])) {
                            $fontsElems[$family] = [];
                        }
                        $fontsElems[$family][] = $weight;
                    }
                }
                foreach ($fontsElems as $name_font => $fontsElem) {
                    $elem = [];
                    if (!isset($fonts['fonts'][$name_font])) {
                        $elem['name'] = $name_font;
                        $elem['styles'] = $fontsElem;
                        $elem['type'] = 'css';
                        $elem['url'] = $css;
                        $fonts['fonts'][$name_font] = $elem;
                        $fontCss[] = $elem;
                        // write to file
                        if (!JFile::write($fontsfile, json_encode($fonts))) {
                            $output = ['error' => JText::_('T4_CUSTOM_FONT_SAVE_ERROR')];
                        }
                    } else {
                        $warning_alert = true;
                    }
                }
            }


            $output = [
                'ok' => 1,
                'fonts' => $fontCss
            ];
        } else {
            $fontFile = [];
            foreach ($font['font'] as $fontUrl) {
                if (strpos($fontUrl, 'http') === false) {
                    // local
                    $file = \T4\Helper\Path::findInTheme($fontUrl);
                    if (!$file) {
                        return $output = ['error' => JText::_('T4_CUSTOM_FONT_SAVE_ERROR')];
                    }
                    $fontUrl = \T4\Helper\Path::findInTheme($fontUrl, true);
                }

                $fontUrls = [];
                if (preg_match('/[^\/]+$/', $fontUrl, $fontName)) {
                    $name = str_replace(" ", "-", preg_replace('/\.[^.]+$/', '', $fontName[0]));
                    if (!isset($fonts['fonts'][$name])) {
                        $fontUrls['name'] = $name;
                        $font['name'] = $name;
                        $fontUrls['url'] = $fontUrl;
                        $fonts['fonts'][$name] = $fontUrls;
                        // write to file
                        if (!is_dir(dirname($fontsfile))) {
                            JFolder::create(dirname($fontsfile));
                        }
                        if (!JFile::write($fontsfile, json_encode($fonts))) {
                            $output = ['error' => JText::_('T4_CUSTOM_FONT_SAVE_ERROR')];
                        }
                    } else {
                        $warning_alert = true;
                    }
                    $fontFile[] = $fontUrls;
                }
            }

            $output = [
                'ok' => 1,
                'fonts' => $fontFile
            ];
        }
        if ($warning_alert) {
            $output = ['error' => JText::_('T4_CUSTOM_FONT_SAVE_DUPLICATED_ERROR')];
        }

        header("Content-Type: application/json;");
        echo json_encode($output);
        exit;
    }
    public static function removeFont()
    {
        $name = JFactory::getApplication()->input->post->getVar('name');
        $pageid = JFactory::getApplication()->input->post->getInt('pageid');
        $index = JFactory::getApplication()->input->post->getInt('fontIndex');
        // current addon
        $fontsfile = JPB_PATH_MEDIA_BUILDER . 'etc/'.$pageid.'/customfonts.json';
        $fonts = is_file($fontsfile) ? json_decode(file_get_contents($fontsfile), true) : [];
        if (isset($fonts['fonts'][trim($name)])) {
            unset($fonts['fonts'][trim($name)]);
            // write to file
            if (!JFile::write($fontsfile, json_encode($fonts))) {
                $output = ['error' => JText::_('T4_ADDONS_DELETE_ERROR')];
            } else {
                $output = ['ok' => 1];
            }
        } else {
            $output = ['error' => JText::_('T4_CUSTOM_FONT_DELETE_NOTFOUND_ERROR')];
        }

        header("Content-Type: application/json;");
        echo json_encode($output);
        exit;
    }
}
