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
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use \Joomla\CMS\Factory as JFactory;
use \Joomla\CMS\Filesystem\Folder as JFolder;
use \Joomla\CMS\Application\ApplicationHelper as JApplicationHelper;
use Joomla\CMS\Filesystem\Path as JPath;
use Joomla\CMS\Language\Text as JText;


class Articlelayout extends Base {
	public function run() {
		$app = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$mod = $input->getVar('mod');
		$layout = self::getLayout($mod);
		return $layout;
	}
	public function getLayout($module = '')
	{
        $layout = array();
        if(!$module) return $layout;
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
        // Load language file
        $lang = JFactory::getLanguage();
        $lang->load($module . '.sys', $client->path, null, false, true)
            || $lang->load($module . '.sys', $client->path . '/modules/' . $module, null, false, true);
        // Build the search paths for module layouts.
        $module_path = JPath::clean($client->path . '/modules/' . $module . '/tmpl');

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
            $groups['_']['text'] = JText::sprintf('JOPTION_FROM_MODULE');
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
                // Load language file
                $lang->load('tpl_' . $template->element . '.sys', $client->path, null, false, true)
                    || $lang->load('tpl_' . $template->element . '.sys', $client->path . '/templates/' . $template->element, null, false, true);

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
                        $groups[$template->element]['text'] = JText::sprintf('JOPTION_FROM_TEMPLATE', $template->name);
                        $groups[$template->element]['items'] = array();

                        foreach ($files as $file)
                        {
                            // Add an option to the template group
                            $value = basename($file, '.php');
                            $text = $lang->hasKey($key = strtoupper('TPL_' . $template->element . '_' . $module . '_LAYOUT_' . $value))
                                ? JText::_($key) : $value;
                            $groups[$template->element]['items'][] = JHtml::_('select.option', $template->element . ':' . $value, $text);
                        }
                    }
                }
            }
        }
        return $groups;
    }

}
