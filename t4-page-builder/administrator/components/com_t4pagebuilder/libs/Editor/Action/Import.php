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
use Joomla\CMS\Factory as JFactory;
use Joomla\String\StringHelper AS JStringHelper;
use JPB\Helper\File as T4bFile;
\JLoader::import('joomla.filesystem.file');
\JLoader::import('joomla.filesystem.folder');
\JLoader::import('joomla.filesystem.path');
\JLoader::import('joomla.filesystem.archive');
\JLoader::import('joomla.filesystem.archive.zip');
\JLoader::import( 'joomla.database.database.mysql' );



class Import extends Base {

	public function run() {
		$input = JFactory::getApplication()->input;
		$lang = JFactory::getLanguage();
		$extension = 'com_installer';
		$language_tag = JFactory::getLanguage()->getTag();
		$lang->load($extension, JPATH_ADMINISTRATOR, $language_tag, true);
		$package_proccess = $input->getVar('type');
		if($package_proccess == "package"){
			$result = $this->_importPackage();
			return $result;

		}
		if($package_proccess == "editor"){
			$result_editor = $this->_Editor_Import();
			return $result_editor;
		}
		$step = $input->getVar('st');
		if($step == 'step1'){
			$page_review = [];
			$installType = $input->getWord('installtype');
			switch ($installType)
			{
				case 'upload':
					$package = $this->_getPackageFromUpload();
					break;

				case 'url':
					$package = $this->_getPackageFromUrl();
					break;

				default:
					$app->setUserState('com_installer.message', \JText::_('COM_INSTALLER_NO_INSTALL_TYPE_FOUND'));
					return ["error"=> \JText::_('COM_INSTALLER_NO_INSTALL_TYPE_FOUND')];
			}
			if(isset($package['error'])){
				return $package;
			}
			if(!isset($package['dir'])){
				return ["error" => "Package upload is error"];
			}

			$filename = glob($package['dir']."/*.json");
			if(count($filename)){
				$dataJson = json_decode(file_get_contents($filename[0]),true);
			}
			$fileDetail = glob($package['dir']."/*.txt");
			if(count($filename)){
				$dataDetail = json_decode(file_get_contents($fileDetail[0]),true);
			}else{
				return ["error"=> \JText::_('COM_INSTALLER_NO_INSTALL_TYPE_FOUND')];
			}
			if(empty($dataDetail)) return ["error"=> \JText::_('COM_INSTALLER_NO_INSTALL_TYPE_FOUND')];
			foreach ($dataDetail as $data) {
				if(isset($data['thumb']) && file_exists($package['extractdir'] . "/" .$data['thumb'])){
					$thumb = str_replace(strtolower(JPATH_ROOT) , \JUri::root(true), strtolower($package['extractdir'])) ."/". $data['thumb'];
				}else{
					$thumb = $data['thumb'];
				}
				$page_review['page'][] = ['title' => $data['title'], 'id' => $data['id'], 'thumb' => $thumb];
			}
			$temp_basename = explode(".", basename($package['packagefile']));
			$page_review['basename'] = basename($package['packagefile'],".zip");
			$page_review['package'] = $package;
			return $page_review;
		}elseif ($step == "step2") {
			$data = json_decode(file_get_contents('php://input'), true);
			$pk = json_decode($data['package'],true);
			$pages = $data['page_import'];
			$files = glob($pk['dir']."/*.json");
			$dataContent = json_decode(file_get_contents($files[0]),true);
			if(empty($dataContent)) return ["message"=> "Please select pages to import"];
			foreach ($dataContent as $content) {
				if(in_array($content['id'], $pages)){
					$newId = $this->insertData($content);
					$oldId = $content['id'];
					//check and move file image if exist
					if(is_dir($pk['dir'] . "/images/")){
						$this->updateImages($pk,$content);
					}
					if(is_dir($pk['dir'] . "/fonts/")){
						$this->updateCustomFonts($pk['dir'] . "/fonts/",$newId,$oldId);
					}

					if(is_dir($pk['dir'] . "/shareblock/")){
						$block_arr = array();
						$block_arr = T4bFile::scanDirectories($pk['dir'] . "/shareblock/",$block_arr);
						$this->copyShareBlock($block_arr);
					}
				}
			}
			\JInstallerHelper::cleanupInstall($pk['packagefile'], $pk['extractdir']);
			return ["message"=> "Pages Imported!"];
		}
		return "";
	}
	protected function _importPackage()
	{

		$input = JFactory::getApplication()->input;

		// Get the URL of the package to install.
		$url = $input->getString('package_url');
		$page_style = $input->getString('page_style');

		//get package
		$pk = $this->_getPackageFromUrl($url);
		if(isset($pk['error'])){
			return $pk;
		}
		if(!isset($pk['dir'])){
			return ["error" => "Package upload is error"];
		}
		$filename = glob($pk['dir']."/*.json");

		if(!isset($filename)){
			return ["error" => "Package is invalid"];
		}

		$dataContent = json_decode(file_get_contents($filename[0]),true);
		if(empty($dataContent)) return ["error"=> "Please select pages to import"];
		$pages = json_decode($input->getString('page_import'),true);
		foreach ($dataContent as $content) {
			if(in_array($content['id'], $pages)){
				if($page_style == 1){
					$page_site_id = $input->getString('page_site_id');
					Item::updateStyle($content['css'],$page_site_id);
					return ["data"=> self::getPageTitle($page_site_id)];
				}else{
					$newId = $this->insertData($content);
					$oldid = $content['id'];
					//check and move file image if exist
					if(is_dir($pk['dir'] . "/images/")){
						$this->updateImages($pk,$content);
					}
					if(is_dir($pk['dir'] . "/fonts/")){
						$this->updateCustomFonts($pk['dir'] . "/fonts/",$newId,$oldid);
					}
					if(is_dir($pk['dir'] . "/shareblock/")){
						$block_arr = array();
						$block_arr = T4bFile::scanDirectories($pk['dir'] . "/shareblock/",$block_arr);
						$this->copyShareBlock($block_arr);
					}
				}
			}
		}
		\JInstallerHelper::cleanupInstall($pk['packagefile'], $pk['extractdir']);
		return ["message"=> "Pages Imported!"];
	}
	protected function _Editor_Import()
	{
		$input = JFactory::getApplication()->input;
		$data = json_decode(file_get_contents('php://input'), true);

		// Get the URL of the package to install.
		$url = $data['package_url'];
		$page_style = $data['page_style'];
		$asset_name = $data['asset_name'];

		//get package
		$pk = $this->_getPackageFromUrl($url);
		if(isset($pk['error'])){
			return $pk;
		}
		if(!isset($pk['dir'])){
			return ["error" => "Package upload is error"];
		}
		$filename = glob($pk['dir']."/*.json");

		if(!isset($filename)){
			return ["error" => "Package is invalid"];
		}
		$result = "";
		$dataContent = json_decode(file_get_contents($filename[0]),true);
		if(empty($dataContent)) return ["error"=> "Please select pages to import"];
		$pages = json_decode($data['page_import'],true);
		foreach ($dataContent as $content) {
			if(in_array($content['id'], $pages)){
				$page_site_id = $data['page_site_id'];
				$content['asset_name'] = $asset_name;
				$result = $this->impportPageEditor($content,$page_site_id);
				$conetnt_html  = $content['title'];
				$oldid = $content['id'];
				//check and move file image if exist
				if(is_dir($pk['dir'] . "/images/")){
					$this->updateImages($pk,$content);
				}
				if(is_dir($pk['dir'] . "/fonts/")){
					$this->updateCustomFonts($pk['dir'] . "/fonts/",$result,$oldid);
				}

				if(is_dir($pk['dir'] . "/shareblock/")){
					$block_arr = array();
					$block_arr = T4bFile::scanDirectories($pk['dir'] . "/shareblock/",$block_arr);
					$this->copyShareBlock($block_arr);
				}
			}
		}
		\JInstallerHelper::cleanupInstall($pk['packagefile'], $pk['extractdir']);
		if($result){
			$mesg = ["ok"=> true,"content_title" => $conetnt_html];
		}else{
			$mesg = ["error"=> "Pages Import fail!"];
		}
		// return ['data'=> $result];
		return $mesg;
	}
	/**
	 * Works out an installation package from a HTTP upload.
	 *
	 * @return package definition or false on failure.
	 */
	protected function _getPackageFromUpload()
	{
		// Get the uploaded file information.
		$input    = JFactory::getApplication()->input;

		// Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See JInputFiles::get.
		$userfile = $input->files->get('install_package', null, 'raw');

		// Make sure that file uploads are enabled in php.
		if (!(bool) ini_get('file_uploads'))
		{

			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE')];
		}

		// Make sure that zlib is loaded so that the package can be unpacked.
		if (!extension_loaded('zlib'))
		{

			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB')];
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile))
		{
			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED')];
		}

		// Is the PHP tmp directory missing?
		if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_NO_TMP_DIR))
		{
			
			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . \JText::_('COM_INSTALLER_MSG_WARNINGS_PHPUPLOADNOTSET')];
		}

		// Is the max upload size too small in php.ini?
		if ($userfile['error'] && ($userfile['error'] == UPLOAD_ERR_INI_SIZE))
		{

			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR') . '<br />' . \JText::_('COM_INSTALLER_MSG_WARNINGS_SMALLUPLOADSIZE')];
		}

		// Check if there was a different problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1)
		{
			
			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR')];
		}

		// Build the appropriate paths.
		$config   = JFactory::getConfig();
		$tmp_dest = $config->get('tmp_path') . '/' . $userfile['name'];
		$tmp_src  = $userfile['tmp_name'];

		// Move uploaded file.
		\jimport('joomla.filesystem.file');
		\JFile::upload($tmp_src, $tmp_dest, false, true);

		// Unpack the downloaded package file.
		$package = \JInstallerHelper::unpack($tmp_dest, true);

		return $package;
	}
	/**
	 * Install an extension from a URL.
	 *
	 * @return  Package details or false on failure.
	 *
	 * @since   1.5
	 */
	protected function _getPackageFromUrl($url)
	{

		// Did you give us a URL?
		if (!$url)
		{
			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL')];

		}

		// Download the package at the URL given.
		$p_file = \JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file)
		{
			return ['error' => \JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL')];
		}

		$config   = JFactory::getConfig();
		$tmp_dest = $config->get('tmp_path');
		$allowedExtensions = array( 'rar', 'zip' );
		$temp = explode(".", $p_file);
		$extension = strtolower(end($temp));
		if( !in_array( $extension, $allowedExtensions ) ) {
			// if(!defined(JPB_DEVMODE)) unlink($tmp_dest . '/' . $p_file);
			return ["error" => "You do not have permission!"];
		}

		// Unpack the downloaded package file.
		$package = \JInstallerHelper::unpack($tmp_dest . '/' . $p_file, true);

		return $package;
	}
	/**/
	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $parent_id  The id of the parent.
	 * @param   string   $alias      The alias.
	 * @param   string   $title      The title.
	 *
	 * @return  array    Contains the modified title and alias.
	 *
	 * @since   1.7
	 */
	protected function generateNewTitlePage($alias, $title)
	{
		// Alter the title & alias
		$table = \JTable::getInstance($type = 'Page', $prefix = 'T4pagebuilderTable', $config = array());
		while ($table->load(array('alias' => $alias)))
		{
			$title = JStringHelper::increment($title);
			$alias = JStringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}
	protected function insertData($data){
		$users = \JFactory::getUser();
		unset($data['id']);
		$title = $data['title'];
		$alias = $data['alias'];	
		list($title, $alias) = $this->generateNewTitlePage($alias, $title);
		$data['title'] = $title;
		$data['alias'] = $alias;
		$data['asset_id'] = 0;
		$data['created_by'] = $users->id;
		$data['catid'] = self::getCateId();
		// Insert the object into the user profile table.
		return Item::createPage($data);
	}
	protected function updateImages($package,$content){

		if(isset($content['thumb'])) $content['images'] = $content['images'] . "|" .$content['thumb'];
		$imageArr = explode("|", $content['images']);
		foreach ($imageArr as $img) {
			$img_path = parse_url($img);
			if(empty($img_path['scheme'])){
				$tmpfile = $package['dir'].'/'.$img;
				$pathMove = JPATH_ROOT . "/".$img;
				T4bFile::mkdir_r(dirname($pathMove));
				if(!file_exists($pathMove) && file_exists($tmpfile)){
					if(!copy($tmpfile, $pathMove)){
						$mes[] = "no file ".$img;
					}
				}
			}
		}

		return true;
	}
	protected function updateCustomFonts($font_folder,$itemid,$oldid)
	{
		if(is_file($font_folder . '/'.$oldid.'/customfonts.json')){
			T4bFile::mkdir_r($font_json_path);
			copy($font_folder . '/'.$oldid.'/customfonts.json' , $font_json_path . '/customfonts.json');
			$fonts_name = T4bFile::getCustomeFontName($font_folder . '/'.$oldid.'/customfonts.json');
			if(count($fonts_name) > 0){
				foreach ($fonts_name as $font_key => $font_name ) {
					$file_font = array();
					$file_font = T4bFile::scanDirectories($font_folder .'/'.$font_key,$file_font);
					if(!is_dir(JPATH_ROOT . "/media/t4pagebuilder/builder/customfonts/" .$font_key)){
						mkdir(JPATH_ROOT . "/media/t4pagebuilder/builder/customfonts/" .$font_key);
					}
					foreach ($file_font as $font) {
						$newFontPath = str_replace($font_folder, JPATH_ROOT . "/media/t4pagebuilder/builder/customfonts", $font);
						if(!copy($font,$newFontPath)){
							return ['error'=> "copy font was fail"];
						}
					}
				}
			}
		}
	}
	protected function copyShareBlock($data){
		if(empty($data)) return true;
		T4bFile::mkdir_r(JPB_PATH_SHARE_BLOCK);
		foreach ($data as $block) {
			$file_block =  JPB_PATH_SHARE_BLOCK . basename($block,'.txt') . '.html';
			if(!file_exists($file_block)){
				copy($block,$file_block);
			}
		}
		return true;
	}
	protected function getCateId(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__categories')
			->where("extension = ". $db->quote('com_t4pagebuilder'))
			->order('id ASC')
			->setLimit('1');
		$db->setQuery($query);
		return $db->loadResult();
	}
	protected function getPageTitle($id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id,title')
			->from('#__jae_item')
			->where("id = ". $db->quote($id));
			$db->setQuery($query);
		return $db->loadObject();
	}

	protected function impportPageEditor ($data, $itemId) {

		// create revision
		$item = Item::load($itemId);
		if (!$item) return false;
		
		unset($data['id']);
		$data['title'] = $item->title;
		$data['alias'] = $item->alias;
		$data['hits'] = 0;
		$data['catid'] = $item->catid;
		$data['tag_id'] = (int)$item->tag_id;
		$data['asset_id'] = (int)$item->asset_id;
		$data['rev'] = $item->rev;
		Table::updateRow('jae_item', $data, $itemId);

		return $itemId;
	}
}
