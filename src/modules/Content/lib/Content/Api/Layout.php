<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: pnlayoutapi.php 406 2010-06-01 03:04:45Z drak $
 * @license See license.txt
 */

require_once 'modules/Content/common.php';
include_once 'modules/Content/includes/contentLayoutBase.php';

class Content_Api_Layout extends Zikula_Api
{
    function &getLayoutPlugins($args)
    {
        $modules = ModUtil::getAllMods();
        $plugins = array();
        foreach ($modules as $module) {
            if (ModUtil::loadApi($module['name'], 'layouttypes')) {
                // TODO: old style layouttypes plugins directory, maybe changed later
                $dir = "modules/$module[directory]/pnlayouttypesapi";
                if (is_dir($dir) && $dh = opendir($dir)) {
                    while (($filename = readdir($dh)) !== false) {
                        if (preg_match('/^([-a-zA-Z0-9_]+).php$/', $filename, $matches)) {
                            $layoutName = $matches[1];
                            if (SecurityUtil::checkPermission('Content:plugins:layout', $layoutName . '::', ACCESS_READ))
                                $plugins[] = ModUtil::apiFunc($module['name'], 'layouttypes', $layoutName);
                        }
                    }

                    closedir($dh);
                }
            }
        }

        return $plugins;
    }

    public function getLayouts($args)
    {
        $plugins = $this->getLayoutPlugins(array());
        $layouts = array();
        $names = array();

        for ($i = 0, $cou = count($plugins); $i < $cou; ++$i) {
            $plugin = &$plugins[$i];
            $layouts[$i] = array('name' => $plugin->getName(), 'title' => $plugin->getTitle(), 'description' => $plugin->getDescription(), 'numberOfContentAreas' => $plugin->getNumberOfContentAreas(), 'image' => $plugin->getImage());
            $names[$i] = $layouts[$i]['name'];
        }
        // sort the layouts array by the name
        array_multisort($names, SORT_ASC, $layouts);

        return $layouts;
    }

    public function getLayoutPlugin($args)
    {
        return ModUtil::apiFunc('Content', 'layouttypes', $args['layout']);
    }

    public function getLayout($args)
    {
        $plugin = $this->getLayoutPlugin($args);
        return array('name' => $plugin->getName(), 'title' => $plugin->getTitle(), 'description' => $plugin->getDescription(), 'numberOfContentAreas' => $plugin->getNumberOfContentAreas(), 'image' => $plugin->getImage(), 'plugin' => $plugin);
    }
}