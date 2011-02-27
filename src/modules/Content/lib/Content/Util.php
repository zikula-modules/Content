<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
  * @license See license.txt
 */
class Content_Util
{

    public static function contentHasPageViewAccess($pageId = null)
    {
        return SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_READ);
    }

    public static function contentHasPageCreateAccess($pageId = null)
    {
        return SecurityUtil::checkPermission('Content:page:', '::', ACCESS_ADD);
    }

    public static function contentHasPageEditAccess($pageId = null)
    {
        return SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_EDIT);
    }

    public static function contentHasPageDeleteAccess($pageId = null)
    {
        return SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_DELETE);
    }

    public static function contentAddAccess(&$view, $pageId)
    {
        $access = array('pageCreateAllowed'    => self::contentHasPageCreateAccess($pageId),
                        'pageEditAllowed'      => self::contentHasPageEditAccess($pageId),
                        'pageDeleteAllowed'    => self::contentHasPageDeleteAccess($pageId));
        $view->assign('access', $access);
    }

    // Clear all Content caches. Call this function whenever something has been changed.
    // If you add other caching schemes then remember to clear them here
    public static function contentClearCaches()
    {
        $view = Zikula_View::getInstance('Content', true);
        // Menu blocks
        $cacheId = 'menu'; // No language: clear all versions
        $view->clear_cache(null, $cacheId);
    }

    public static function contentMainEditExpandToggle($pageId)
    {
        $expandedPageIds = SessionUtil::getVar('contentExpandedPageIds', array());
        if (isset($expandedPageIds[$pageId])) {
            unset($expandedPageIds[$pageId]);
        } else {
            $expandedPageIds[$pageId] = 1;
        }
        SessionUtil::setVar('contentExpandedPageIds', $expandedPageIds);
    }

    public static function contentMainEditExpandSet($pageId, $value)
    {
        $expandedPageIds = SessionUtil::getVar('contentExpandedPageIds', array());
        if ($value) {
            $expandedPageIds[$pageId] = 1;
        } else {
            unset($expandedPageIds[$pageId]);
        }
        SessionUtil::setVar('contentExpandedPageIds', $expandedPageIds);
    }

    public static function contentMainEditExpandGet()
    {
        return SessionUtil::getVar('contentExpandedPageIds', array());
    }

    public static function getPlugins($type='Content')
    {
        $type = in_array($type, array('Content', 'Layout')) ? trim(ucwords(strtolower($type))) . "Type" : 'ContentType';
        $modules = ModUtil::getAllMods();
        $plugins = array();
        foreach ($modules as $module) {
            $dir = DataUtil::formatForOS("modules/{$module['directory']}/lib/{$module['directory']}/$type");
            if (is_dir($dir)) {
                $files = FileUtil::getFiles($dir, false, false, "php");
                foreach ($files as $file) {
                    $parts = explode(DIRECTORY_SEPARATOR, $file);
                    $filename = array_pop($parts);
                    $pluginname = substr($filename, 0, -4);
                    $classname = $module['directory'] . "_" . $type . "_" . $pluginname;
                    $baseclass = "Content_" . $type;
                    $view = Zikula_View::getInstance($module['directory']);
                    $instance = new $classname($view);
                    if ($instance instanceof $baseclass) {
                        $plugins[] = $instance;
                    }
                }
            }
        }
        usort($plugins, array('Content_Util', 'pluginSort'));
        return $plugins;
    }

    protected static function pluginSort($a, $b)
    {
        return strcmp($a->getTitle(), $b->getTitle());
    }
}