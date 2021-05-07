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

header('Access-Control-Allow-Origin: *');
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Filesystem\File as JFile;
class Saveblock extends Base {

	public function run() {
		$input = JFactory::getApplication()->input;
		if($input->getInt('rm','')){
			$st = $this->removeBlock();
			return $st;

		}else{
			$st = $this->saveHtml();
			return $st;
		}
	}
	public function savejson(){
		$input = JFactory::getApplication()->input;
		$this->itemId = $input->getInt('id');
		$dir = JPATH_ROOT . JPB_MEDIA . 'html/userblocks';
		$content = $input->get('content','','raw');
		//$content = str_replace(' ', '', $content);
		//$content = json_decode($content);
		$filename = $input->get('name');
		$css_id = $input->get('css', '');
		$cate_block = $input->get('category');

          //build json
     	if($content){
         	$data = array();
			$data['label'] = $filename;
			$data['name'] = $filename;
			$data['content'] = $content;
	          $components = array();
			$block = array('blocks' => array($data),
	                                   'components'=>$components,
	                                   'category'=>'UserBlocks',
	                                   'package'=>'t4'); 
			$block_save = array();
			$block_save['blocks'] = array($block);
			$file = $dir."/".$filename.".json";
			JFile::write($file, json_encode($block_save));
        }
		
	}

	private function saveHtml(){
		$return = false;
		$input = JFactory::getApplication()->input;

		$type = $input->get('category');
		$name = $input->get('name');

		// get post content
		$data = json_decode(file_get_contents('php://input'), true);
		$content = isset($data['content']) ? $data['content'] : null;
		//$content = $input->get('content','','raw');
		// $css = $input->get('css', '');

		if (!$content) return false;

		return \JPB\Helper\Block::save($name, $content, $type);



        /* TO BE DELETED */

		$this->itemId = $input->getInt('id');
		$dir = JPATH_ROOT . JPB_MEDIA . 'public-html/';

		$cate_block = $input->get('category');
		if(!is_dir($dir)){
			mkdir($dir);
		}
		$content = $input->get('content','','raw');
		//$content = str_replace(' ', '', $content);
		//$content = json_decode($content);
		$filename = $input->get('name');
		$css = $input->get('css', '');
        //build html
        if($content){
			$block = $content."\n".$css;
			$file = $dir.$cate_block."/".$filename.".html";
			JFile::write($file, $block);
			$return = true;
        }
        return $return;
	}
	private function removeBlock(){
		$input = JFactory::getApplication()->input;
		$type = $input->get('type');
		$name = $input->get('name');
		return \JPB\Helper\Block::removeBlock($name, $type);
	}
}