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

use JPB\Helper\Item as Item;
use JPB\Helper\Table as Table;
use JPB\Helper\File as T4bFile;

use \Joomla\CMS\Factory as JFactory;
use \Joomla\CMS\Filesystem\Folder as JFolder;
use \Joomla\CMS\Filesystem\File as JFile;
use \Joomla\CMS\Exception\ExceptionHandler as Exception;
use \Joomla\CMS\Language\Text as JText;

class Export extends Base
{
    public static function exports($data)
    {
        $name = '';
        // Force the download
        $filename = 't4builder' . $name. '_' . date('Ymdhms');
        $tmpPath = JFactory::getConfig()->get('tmp_path');
        $path_file = $tmpPath . "/" . $filename;
        if (!JFolder::exists($path_file)) {
            JFolder::create($path_file);
        }
        $images_paths = $path_file . "/images/";
        if (!JFolder::exists($images_paths)) {
            JFolder::create($images_paths);
        }
        $customfont_paths = $path_file . "/fonts/";
        if (!JFolder::exists($customfont_paths)) {
            JFolder::create($customfont_paths);
        }
        $dataExport = [];
        $detail = [];
        $images = [];
        // copy ( $detail_file, $customfont_paths . 'aa.txt' );

        if (isset($data)) {
            foreach ($data as $id) {
                $item = Item::load($id);
                $html = Table::decodeData($item->working_content);
                if (!empty($item->css && defined("T4B_DEVMODE"))) {
                    $html['css'] = "";
                    $item->working_content = Table::encodeData($html);
                    $item->bundle_css = $item->css;
                    // $item->css = "";
                }
                if(!empty($html['shareblock'])){
                    $dest_block = $path_file . '/shareblock/';
                    self::cpShareBlock($html['shareblock'], $dest_block);
                }

                if (is_file(JPB_PATH_MEDIA_BUILDER . "etc/".$id. '/customfonts.json')) {
                    $font_path = self::cpCustomFont(JPB_PATH_MEDIA_BUILDER . "etc/".$id. '/customfonts.json', $customfont_paths);
                    if (!is_dir($customfont_paths .$id)) {
                        mkdir($customfont_paths .$id);
                    }
                    copy(JPB_PATH_MEDIA_BUILDER . "etc/".$id. '/customfonts.json', $customfont_paths .$id. '/customfonts.json');
                }
                $dataExport[$item->alias] = $item;
                $detail[] = array(
                    "id"=> $item->id,
                    "title"=> $item->title,
                    "thumb"=> $item->thumb,
                    "page_key"=> $item->page_key,
                );
                if ($item->images) {
                    $images[] = explode("|", $item->images);
                }
                if (file_exists(JPATH_ROOT ."/". $item->thumb)) {
                    $images[] = $item->thumb;
                }
                if (count($data) == 1) {
                    $name .= "_".$item->alias;
                }
            }
        }

        $content = json_encode($dataExport);
        $filename_xml = 't4builder' . $name. '_' . date('Ymdhms');
        $xml_file = $path_file ."/" . $filename_xml. '.json';
        JFile::write($xml_file, $content);
        //create install detail
        $detail_data = json_encode($detail);
        $detail_file = $path_file . "/detail.txt";
        JFile::write($detail_file, $detail_data);
        $blank = array();
        $filenameZip = $path_file .'.zip';
        $result = self::zipper($path_file, $blank, $filenameZip, true);
        if (file_exists($filenameZip)) {
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header('Content-Type: application/zip, application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filenameZip).'"');
            header("Content-Transfer-Encoding: binary");
            header('Content-Length: ' . filesize($filenameZip));
            ob_clean();
            ob_end_flush();
            @readfile($filenameZip);
            // delete file
            unlink($filenameZip);
            unlink($xml_file);
            unlink($detail_file);
            rmdir($path_file);

            exit;
        } else {
            return [ 'error', 'Don\'t create file!' ];
        }
    }
    public static function zipper($directory, $ignore = array(), $the_file = '', $overwrite = false)
    {

        // Prevents overwriting an existing archive if overwrite is false.
        if (file_exists($the_file) && !$overwrite) {
            return false;
        }
            
        $files = array();
        
        // Loop through the directory and get the files to include in the zip.
        if (is_dir($directory)) {
            $files = T4bFile::scanDirectories($directory, $files);
        } else {
            throw new Exception(JText::_('COM_T4PAGEBUILDER_EXPORT_ERROR_MESSAGE_NOT_FOUND'), 404);
            return false;
        }

        // If there are any files within the directory, we can create the zip.
        if (count($files) > 0) {
            require_once JPB_PATH . '/libs/vendor/autoload.php';
            require_once JPB_PATH . '/libs/vendor/nelexa/zip/src/ZipFile.php';

            $zipper =  new \PhpZip\ZipFile();
            // Add each file to the archive.
            foreach ($files as $file) {
                $file_new = str_replace($directory . '/', "", $file);
                $file_arr = explode('.', basename($file));
                if(!in_array('html',$file_arr)){
                    $zipper->addFile($file, $file_new);
                }
            }
            $zipper->saveAsFile($the_file);
            $zipper->close();
            
            // Return confirmation that it now exists!
            return file_exists($the_file);
        } else {
            throw new Exception(JText::_('COM_T4PAGEBUILDER_EXPORT_ERROR_MESSAGE_NOT_FOUND'), 404);
            return false;
        }
    }

    public static function cpCustomFont($jsonData, $rest)
    {
        $dataFont = json_decode(file_get_contents($jsonData), true);
        $font_path = array();
        if (count($dataFont['fonts'])) {
            foreach ($dataFont['fonts'] as $fonts) {
                $file_fonts = array();
                if (strpos($fonts['url'], 'http') === false) {
                    $path = str_replace(basename($fonts['url']), "", $fonts['url']);
                    if (!in_array($path, $font_path)) {
                        $font_path[] = $path;
                        $name_arr = explode('/', $path);
                        $font_name = basename($path);
                        $file_fonts = T4bFile::scanDirectories(JPATH_ROOT . $path, $file_fonts);
                        foreach ($file_fonts as $file) {
                            if (!is_dir($rest .$font_name ."/")) {
                                mkdir($rest .$font_name ."/");
                            }
                            copy($file, str_replace(JPATH_ROOT . $path, $rest .$font_name, $file));
                        }
                    }
                }
            }
        }

        return $font_path;
    }
    public static function cpShareBlock($arr,$dest)
    {
        if(empty($arr)) return true;
        T4bFile::mkdir_r($dest);
        foreach ($arr as $block) {
            $file_block = JPB_PATH_SHARE_BLOCK . $block .'.html';
            if(is_file($file_block)){
                if(!copy($file_block, $dest . $block .'.txt')){
                    return false;
                }
            }
        }
        return true;
    }
}
