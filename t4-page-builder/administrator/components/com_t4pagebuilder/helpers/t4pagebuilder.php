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
defined('_JEXEC') or die();
use \Joomla\CMS\Factory as JFactory;
use \Joomla\CMS\Helper\ContentHelper as JHelperContent;
use \Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use \Joomla\CMS\Uri\Uri as JUri;
use \Joomla\CMS\HTML\HTMLHelper as JHtml;
use \Joomla\CMS\Language\Text as JText;
use \JPB\Helper\Item as Item;

class T4pagebuilderHelper extends JHelperContent
{
    public static $extension = 'com_t4pagebuilder';

    public static function stringUrlsafe($string)
    {
        $trans = array(

        "đ"=>"d","ă"=>"a","â"=>"a","á"=>"a","à"=>"a",

        "ả"=>"a","ã"=>"a","ạ"=>"a",

        "ấ"=>"a","ầ"=>"a","ẩ"=>"a","ẫ"=>"a","ậ"=>"a",

        "ắ"=>"a","ằ"=>"a","ẳ"=>"a","ẵ"=>"a","ặ"=>"a",

        "é"=>"e","è"=>"e","ẻ"=>"e","ẽ"=>"e","ẹ"=>"e",

        "ế"=>"e","ề"=>"e","ể"=>"e","ễ"=>"e","ệ"=>"e",

        "í"=>"i","ì"=>"i","ỉ"=>"i","ĩ"=>"i","ị"=>"i",

        "ư"=>"u","ô"=>"o","ơ"=>"o","ê"=>"e",

        "Ư"=>"u","Ô"=>"o","Ơ"=>"o","Ê"=>"e",

        "ú"=>"u","ù"=>"u","ủ"=>"u","ũ"=>"u","ụ"=>"u",

        "ứ"=>"u","ừ"=>"u","ử"=>"u","ữ"=>"u","ự"=>"u",

        "ó"=>"o","ò"=>"o","ỏ"=>"o","õ"=>"o","ọ"=>"o",

        "ớ"=>"o","ờ"=>"o","ở"=>"o","ỡ"=>"o","ợ"=>"o",

        "ố"=>"o","ồ"=>"o","ổ"=>"o","ỗ"=>"o","ộ"=>"o",

        "ú"=>"u","ù"=>"u","ủ"=>"u","ũ"=>"u","ụ"=>"u",

        "ứ"=>"u","ừ"=>"u","ử"=>"u","ữ"=>"u","ự"=>"u",

        "ý"=>"y","ỳ"=>"y","ỷ"=>"y","ỹ"=>"y","ỵ"=>"y",

        "Ý"=>"Y","Ỳ"=>"Y","Ỷ"=>"Y","Ỹ"=>"Y","Ỵ"=>"Y",

        "Đ"=>"D","Ă"=>"A","Â"=>"A","Á"=>"A","À"=>"A",

        "Ả"=>"A","Ã"=>"A","Ạ"=>"A",

        "Ấ"=>"A","Ầ"=>"A","Ẩ"=>"A","Ẫ"=>"A","Ậ"=>"A",

        "Ắ"=>"A","Ằ"=>"A","Ẳ"=>"A","Ẵ"=>"A","Ặ"=>"A",

        "É"=>"E","È"=>"E","Ẻ"=>"E","Ẽ"=>"E","Ẹ"=>"E",

        "Ế"=>"E","Ề"=>"E","Ể"=>"E","Ễ"=>"E","Ệ"=>"E",

        "Í"=>"I","Ì"=>"I","Ỉ"=>"I","Ĩ"=>"I","Ị"=>"I",

        "Ư"=>"U","Ô"=>"O","Ơ"=>"O","Ê"=>"E",

        "Ư"=>"U","Ô"=>"O","Ơ"=>"O","Ê"=>"E",

        "Ú"=>"U","Ù"=>"U","Ủ"=>"U","Ũ"=>"U","Ụ"=>"U",

        "Ứ"=>"U","Ừ"=>"U","Ử"=>"U","Ữ"=>"U","Ự"=>"U",

        "Ó"=>"O","Ò"=>"O","Ỏ"=>"O","Õ"=>"O","Ọ"=>"O",

        "Ớ"=>"O","Ờ"=>"O","Ở"=>"O","Ỡ"=>"O","Ợ"=>"O",

        "Ố"=>"O","Ồ"=>"O","Ổ"=>"O","Ỗ"=>"O","Ộ"=>"O",

        "Ú"=>"U","Ù"=>"U","Ủ"=>"U","Ũ"=>"U","Ụ"=>"U",

        "Ứ"=>"U","Ừ"=>"U","Ử"=>"U","Ữ"=>"U","Ự"=>"U",);

        //remove any '-' from the string they will be used as concatonater

        $str = str_replace('-', ' ', $string);

        $str = strtr($str, $trans);

        $lang = JFactory::getLanguage();

        $str = $lang->transliterate($str);

        // remove any duplicate whitespace, and ensure all characters are alphanumeric

        $str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);

        // lowercase and trim

        $str = trim(strtolower($str));

        return $str;
    }
    public static function mediaField($fid)
    {
        $asset = 'com_t4pagebuilder';
        $authorId = JFactory::getUser()->id;
        $mediaUrlj3 = (JFactory::getApplication()->isClient('site') ? '' : '') . "index.php?option=com_media&view=images&tmpl=component&asset={$asset}&author={$authorId}&fieldid={field-media-id}&ismoo=0&folder=";
        $mediaUrlj4 = (JFactory::getApplication()->isClient('site') ? '' : '') . "index.php?option=com_media&amp;tmpl=component&amp;asset={$asset}&amp;author={$authorId}&amp;fieldid={field-media-id}&amp;path=";
        $mediaId = $fid . '_media';
        // $mediaId = $this->id;
        $outputj3 = '<joomla-field-media id="t4-media-joomla" class="field-media-wrapper" data-basepath="'.JUri::root().'" data-url="'.$mediaUrlj3.'" data-modal=".modal" data-modal-width="100%" data-modal-height="400px" data-input=".field-media-input" data-button-select=".button-select" data-button-clear=".button-clear" data-button-dismiss=".button-cancel" data-button-save-selected=".button-save-selected" data-preview="true" data-preview-as-tooltip="true" data-preview-container=".field-media-preview" data-preview-width="200" data-preview-height="200">
					<div id="imageModal_'.$mediaId.'" tabindex="-1" class="modal hide fade" style="display: none;" aria-hidden="true">
						<div class="modal-header">
							<button type="button" class="close novalidate" data-dismiss="modal">×</button>
									<h3>Select Image</h3>
						</div>
						<div class="modal-body" style="max-height: initial; overflow-y: initial;"></div>
						<div class="modal-footer">
							<a class="btn button-cancel" data-dismiss="modal">Cancel</a>
						</div>
					</div>
					<div class="input-prepend input-append" style="display:none;">
						<input name="jform[t4][layout_media]" id="'.$mediaId.'" value="" readonly="readonly" class="input-small hasTooltip field-media-input" data-original-title="" title="">
						<a class="btn add-on button-select">Select</a>
                        <a class="btn btn-secondary hasTooltip button-clear" title="Clear"><span class="fa fa-times" aria-hidden="true"></span></a>
					</div>
				</joomla-field-media>';
        $outputj4 = '<joomla-field-media id="t4-media-joomla" class="field-media-wrapper" type="image" base-path="'.JUri::root().'" root-folder="images" url="'.$mediaUrlj4.'" modal-container=".modal" modal-width="100%" modal-height="400px" input=".field-media-input" button-select=".button-select" button-clear=".button-clear" button-save-selected=".button-save-selected" style="position: relative; z-index: 10000000;">
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
    public static function getAssignmentOptions($clientId)
    {
        $options = array();
        $options[] = JHtml::_('select.option', '0', 'COM_MODULES_OPTION_MENU_ALL');
        $options[] = JHtml::_('select.option', '-', 'COM_MODULES_OPTION_MENU_NONE');

        if ($clientId == 0) {
            $options[] = JHtml::_('select.option', '1', 'COM_MODULES_OPTION_MENU_INCLUDE');
            $options[] = JHtml::_('select.option', '-1', 'COM_MODULES_OPTION_MENU_EXCLUDE');
        }

        return $options;
    }
    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     *
     * @since   1.6
     */
    public static function addSubmenu($vName)
    {
        /*JHtmlSidebar::addEntry(
            '<i class="fal fa-home"></i> ' . JText::_('Builder Home'),
            '#',
            $vName == 'home'
        );*/
        JHtmlSidebar::addEntry(
            '<span class="nav-icon"><i class="fal fa-file-alt"></i></span>' . JText::_('COM_T4PAGEBUILDER_PAGES'),
            'index.php?option=com_t4pagebuilder&view=pages',
            $vName == 'pages'
        );
        JHtmlSidebar::addEntry(
            '<span class="nav-icon"><i class="fal fa-folder"></i></span>' . JText::_('COM_T4PAGEBUILDER_CATEGORIES'),
            // 'index.php?option=com_categories&extension=com_t4pagebuilder',
            'index.php?option=com_t4pagebuilder&view=categories',
            $vName == 'categories'
        );
        /*JHtmlSidebar::addEntry(
            '<i class="fal fa-support"></i> ' . JText::_('COM_t4PAGEBUILDER_PAGES_HELP'),
            '#',
            $vName == 'document'
        );*/
    }
    /**
     * Adds Count Items for Category Manager.
     *
     * @param   stdClass[]  &$items  The category objects
     *
     * @return  stdClass[]
     *
     * @since   3.5
     */
    public static function countItems(&$items)
    {
        $config = (object) array(
            'related_tbl'   => 'jae_item',
            'state_col'     => 'state',
            'group_col'     => 'catid',
            'asset_name'     => array('pagetext'),
            'relation_type' => 'category_or_group',
        );

        return self::countRelations($items, $config);
    }
    /**
     * Adds Count relations for Category and Tag Managers
     *
     * @param   stdClass[]  &$items  The category or tag objects
     * @param   stdClass    $config  Configuration object allowing to use a custom relations table
     *
     * @return  stdClass[]
     *
     * @since   3.9.1
     */
    public static function countRelations(&$items, $config)
    {
        $db = JFactory::getDbo();

        // Allow custom state / condition values and custom column names to support custom components
        $counter_names = isset($config->counter_names) ? $config->counter_names : array(
            '-2' => 'count_trashed',
            '0'  => 'count_unpublished',
            '1'  => 'count_published',
            '2'  => 'count_archived',
        );

        // Index category objects by their ID
        $records = array();

        foreach ($items as $item) {
            $records[(int) $item->id] = $item;
        }

        // The relation query does not return a value for cases without relations of a particular state / condition, set zero as default
        foreach ($items as $item) {
            foreach ($counter_names as $n) {
                $item->{$n} = 0;
            }
        }

        // Table alias for related data table below will be 'c', and state / condition column is inside related data table
        $related_tbl = $db->quoteName('#__' . $config->related_tbl, 'c');
        $state_col   = $db->quoteName('c.' . $config->state_col);
        $asset_name   = $db->quoteName('c.asset_name');
        // Supported cases
        switch ($config->relation_type) {
            case 'tag_assigments':
                $recid_col = $db->quoteName('ct.' . $config->group_col);

                $query = $db->getQuery(true)
                    ->from($db->quoteName('#__contentitem_tag_map', 'ct'))
                    ->join(
                        'INNER',
                        $related_tbl . ' ON ' . $db->quoteName('ct.content_item_id') . ' = ' . $db->quoteName('c.id') . ' AND ' .
                        $db->quoteName('ct.type_alias') . ' = ' . $db->quote($config->extension)
                    );
                break;

            case 'category_or_group':
                $recid_col = $db->quoteName('c.' . $config->group_col);

                $query = $db->getQuery(true)
                    ->from($related_tbl);
                break;

            default:
                return $items;
        }

        /**
         * Get relation counts for all category objects with single query
         * NOTE: 'state IN', allows counting specific states / conditions only, also prevents warnings with custom states / conditions, do not remove
         */
        $count_assetname  = $config->asset_name;

        $query
            ->select($recid_col . ' AS catid, ' . $state_col . ' AS state, COUNT(*) AS count')
            ->where($recid_col . ' IN (' . implode(',', array_keys($records)) . ')')
            ->where($state_col . ' IN (' . implode(',', array_keys($counter_names)) . ')')
            ->where($asset_name . ' IN (' . implode(',', $db->quote($count_assetname)) . ')')
            ->group($recid_col . ', ' . $state_col);
        $relationsAll = $db->setQuery($query)->loadObjectList();

        // Loop through the DB data overwritting the above zeros with the found count
        foreach ($relationsAll as $relation) {
            // Sanity check in case someone removes the state IN above ... and some views may start throwing warnings
            if (isset($counter_names[$relation->state])) {
                $id = (int) $relation->catid;
                $cn = $counter_names[$relation->state];

                $records[$id]->{$cn} = $relation->count;
            }
        }

        return $items;
    }
    public static function revSettings()
    {
        $t4input 	= JFactory::getApplication()->input;
        $db 		= JFactory::getDbo();
        $params  	= JComponentHelper::getParams('com_t4pagebuilder');
        $revSetting = $params->get('revision_config', 2);
        $view 		= $t4input->get('view');
        $layout 	= $t4input->get('layout');

        if ($revSetting == 0) {
            $q = $db->getQuery(true);
            $sql = 'TRUNCATE TABLE `#__jae_revision`';
            $db->setQuery($sql);
            $db->execute();
        }
        if ($revSetting == 1 && $view == 'page' && $layout == 'edit') {
            $page_id 	= $t4input->get('id', "");
            $q2 = $db->getQuery(true)->clear();
            if ($page_id) {
                // delete all custom keys for user 1001.
                $conditions = array(
                    $db->quoteName('itemid') . ' = ' . $db->quote($page_id),
                    $db->quoteName('id') . ' NOT IN (SELECT lm.id FROM (SELECT r.id as id FROM #__jae_revision AS r WHERE r.itemid = ' .$db->quote($page_id). ' ORDER BY id DESC LIMIT 5) AS lm)'
                );

                $q2->delete($db->quoteName('#__jae_revision'))
                    ->where($conditions);

                $db->setQuery($q2);
                $result = $db->execute();
            }
        }
    }
    public static function compare_version()
    {
        $thasnew = false;
        $cxml = simplexml_load_file(JPB_PATH . '/t4pagebuilder.xml');
        // var_dump(JPB_PATH);die;
        $ctversion = $ntversion = $cxml->version[0];
        //get info
        $db = JFactory::getDbo();
        $telem = 'com_t4pagebuilder';
        $db = JFactory::getDbo();
        $input = JFactory::getApplication()->input;
        $id = $input->get('id');
        $query = $db->getQuery(true);
        $query
        ->select('*')
        ->from('#__updates')
        ->where('(element = ' . $db->q($telem) . ')');
        $db->setQuery($query);
        $info = $db->loadObject();
        // var_dump($info);die;
        if ($info && count($info)) {
            if (isset($info[$telem]) && version_compare($info[$telem]->version, $ctversion, 'gt')) {
                $thasnew = true;
                $ntversion = $info[$telem]->version;
            }
        }
        $cxml->name = str_replace("_", " ", $cxml->name);
        $return = "";
        $return .= '<div class="t4b-more-info" style="display:none;">';
        $return .= '<h4>Version and Updates</h4>';
        $return .= '<ul>';
        $return .= '<li><i class="fal fa-code-branch"></i><span>Version:</span> <strong>'.$cxml->version.'</strong></li>';
        $return .= '<li><i class="fal fa-calendar-alt"></i><span>Released Date:</span> '.$cxml->creationDate.'</li>';
        $return .= '<li><i class="fal fa-copyright"></i><span>Author: </span><a class="t4-author" href="https://'.$cxml->authorUrl.'" target="_Blank">'.$cxml->author.'</a></li>';
        $return .= '</ul>';
        $return .= '<div class="updated">';
        $return .= empty($cxml->updateservers) ? JText::_('T4_PAGE_BUILDER_LATEST_VERSION_MSG') : ($thasnew ? JText::sprintf('T4_PAGE_BUILDER_NEW_VERSION_MSG', $ctversion, $cxml->name, $ntversion) : JText::sprintf('T4_PAGE_BUILDER_LATEST_VERSION_MSG', $cxml->name));
        $return .= $thasnew ? '<div class="t4-btn btn-update btn-primary"><a href="index.php?option=com_installer&view=update" title="Update components">Update</a></div>' : ''.'</div>';
        $return .= '</div>';
        return $return;
    }
    public static function checkTables()
    {
        $config = JFactory::getConfig();
        $db  = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("COLUMN_NAME")
                ->from('INFORMATION_SCHEMA.COLUMNS')
                ->where("TABLE_SCHEMA = " . $db->quote($config['db']))
                ->where("TABLE_NAME = " . $db->quote($config['dbprefix']."jae_item"));
        $db->setQuery($query);
        $Obj = $db->loadColumn();
        if (!in_array('bundle_css', $Obj)) {
            $page_key = "ALTER TABLE `#__jae_item` ADD `bundle_css` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `css`";
            $db->setQuery($page_key);
            $db->execute();
        }
        if (!in_array('page_key', $Obj)) {
            $page_key = "ALTER TABLE `#__jae_item` ADD `page_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `type`";
            $db->setQuery($page_key);
            $db->execute();
        }
        if (!in_array('thumb', $Obj)) {
            $page_key = "ALTER TABLE `#__jae_item` ADD `thumb` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `alias`";
            $db->setQuery($page_key);
            $db->execute();
        }
        $query2 = $db->getQuery(true);
        $query2->select("*")
                ->from('INFORMATION_SCHEMA.COLUMNS')
                ->where("TABLE_SCHEMA = " . $db->quote($config['db']))
                ->where("TABLE_NAME = " . $db->quote($config['dbprefix']."jae_item"));
        $db->setQuery($query2);
        $check_tables = $db->loadObjectList();
        $lang_check = false;
        foreach ($check_tables as $table) {
            if ($table->COLUMN_NAME == 'language' && $table->COLUMN_DEFAULT != '*') {
                $lang_check = true;
            }
        }
        if ($lang_check) {
            $q = "ALTER TABLE `#__jae_item` 
				CHANGE `page_key` `page_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',  
				CHANGE `state` `state` TINYINT(3) NOT NULL DEFAULT '0', 
				CHANGE `access` `access` INT(11) NOT NULL DEFAULT '0', 
				CHANGE `created_by` `created_by` BIGINT(20) NOT NULL DEFAULT '0', 
				CHANGE `rev` `rev` INT(11) NULL DEFAULT NULL, 
				CHANGE `working_content` `working_content` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, 
				CHANGE `images` `images` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, 
				CHANGE `language` `language` CHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '*', 
				CHANGE `hits` `hits` BIGINT(20) NOT NULL DEFAULT '0', 
				CHANGE `css` `css` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
				CHANGE `bundle_css` `bundle_css` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
				CHANGE `js` `js` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;";
            $db->setQuery($q);
            $db->execute();
        }
        return true;
    }
    public static function buildLanguage()
    {
        $language = array(
            "t4bCustomFontCssMissed" => JText::_('T4_CUSTOM_FONT_CSS_MISSED'),
            "t4bCustomFontFileMissed" => JText::_('T4_CUSTOM_FONT_FILE_MISSED'),
            "t4bCustomFontConfirmRemove" => JText::_('T4_CUSTOM_FONT_CONFIRM_REMOVE'),
            "t4bCustomFontAdded" => JText::_('T4_CUSTOM_FONT_HAS_ADDED'),
            "t4bUserBlockRemoved" => JText::_('T4B_USER_BLOCK_HAS_REMOVED'),
            "t4bUserBlockRemovedFail" => JText::_('T4B_USER_BLOCK_HAS_REMOVED_FAIL'),
        );
        return json_encode($language);
    }
    public static function clearDataPage()
    {
        $db  = JFactory::getDbo();
        $query = $db->getQuery(true);
        // Fields to update.
        $fields = array(
            $db->quoteName('content') . ' = ""',
            $db->quoteName('working_content') . ' = ""'
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('1') . ' = 1',
        );
        $query->Update($db->quoteName('#__jae_item'))->set($fields)->where('1=1');

        $db->setQuery($query);
        if ($db->execute()) {
            return true;
        }
        return false;
    }
}
