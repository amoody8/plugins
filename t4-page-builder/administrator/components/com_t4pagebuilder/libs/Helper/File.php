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

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;

class File {
	/**
	 * Read the contents of a file
	 *
	 * @param   string   $filename   The full file path
	 * @param   boolean  $incpath    Use include path
	 * @param   integer  $amount     Amount of file to read
	 * @param   integer  $chunksize  Size of chunks to read
	 * @param   integer  $offset     Offset of the file
	 *
	 * @return  mixed  Returns file contents or boolean False if failed
	 *
	 * @since   1.7.0
	 * @deprecated  4.0 - Use the native file_get_contents() instead.
	 */
	public static function read($filename, $incpath = false, $amount = 0, $chunksize = 8192, $offset = 0)
	{
		Log::add(__METHOD__ . ' is deprecated. Use native file_get_contents() syntax.', Log::WARNING, 'deprecated');

		$data = null;

		if ($amount && $chunksize > $amount)
		{
			$chunksize = $amount;
		}

		if (false === $fh = fopen($filename, 'rb', $incpath))
		{
			Log::add(Text::sprintf('JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE', $filename), Log::WARNING, 'jerror');

			return false;
		}

		clearstatcache();

		if ($offset)
		{
			fseek($fh, $offset);
		}

		if ($fsize = @ filesize($filename))
		{
			if ($amount && $fsize > $amount)
			{
				$data = fread($fh, $amount);
			}
			else
			{
				$data = fread($fh, $fsize);
			}
		}
		else
		{
			$data = '';

			/*
			 * While it's:
			 * 1: Not the end of the file AND
			 * 2a: No Max Amount set OR
			 * 2b: The length of the data is less than the max amount we want
			 */
			while (!feof($fh) && (!$amount || strlen($data) < $amount))
			{
				$data .= fread($fh, $chunksize);
			}
		}

		fclose($fh);

		return $data;
	}
	public static function scanDirectories($rootDir, $allData=array()) {
	    // set filenames invisible if you want
	    $invisibleFileNames = array(".", "..", ".htaccess", ".htpasswd");
	    // run through content of root directory
	    $dirContent = scandir($rootDir);
	    foreach($dirContent as $key => $content) {
	        // filter all files not accessible
	        $path = $rootDir.'/'.$content;
	        if(!in_array($content, $invisibleFileNames)) {
	            // if content is file & readable, add to array
	            if(is_file($path) && is_readable($path)) {
	                // save file name with path
	                $allData[] = $path;
	            // if content is a directory and readable, add path and name
	            }elseif(is_dir($path) && is_readable($path)) {
	                // recursive callback to open new directory
	                $allData = self::scanDirectories($path, $allData);
	            }
	        }
	    }
	    return $allData;
	}
	public static function getCustomeFontName($jsonData){
		$dataFont = json_decode(file_get_contents($jsonData),true);
		$font_path = array();
		if(count($dataFont['fonts'])){
			foreach($dataFont['fonts'] as $fonts){
				$file_fonts = array();
				if (strpos($fonts['url'], 'http') === false) {
				 	$path = str_replace(basename($fonts['url']), "", $fonts['url']);
					if(!in_array($path, $font_path)){
						$font_name = basename($path);
						$font_path[$font_name] = $path;
					}
				}
			}
		}

		return $font_path;
	}
    public static function mkdir_r($dirName, $rights=0777){
        $dirs = explode('/', $dirName);
        $dir='';
        foreach ($dirs as $part) {
            $dir.=$part.'/';
            if (!is_dir($dir) && strlen($dir)>0)
                mkdir($dir, $rights);
        }
    }
}