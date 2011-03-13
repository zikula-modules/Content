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
        $access = array('pageCreateAllowed' => self::contentHasPageCreateAccess($pageId),
            'pageEditAllowed' => self::contentHasPageEditAccess($pageId),
            'pageDeleteAllowed' => self::contentHasPageDeleteAccess($pageId));
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

        // trigger event
        $event = new Zikula_Event('module.content.getTypes', new Content_Types());
        $plugins = EventUtil::getManager()->notify($event)->getSubject()->getValidatedPlugins($type);

        return $plugins;
    }

    public static function getTypes(Zikula_Event $event) {
        $types = $event->getSubject();
        // add content types
        $types->add('Content_ContentType_Author');
        $types->add('Content_ContentType_Block');
        $types->add('Content_ContentType_Breadcrumb');
        $types->add('Content_ContentType_Camtasia');
        $types->add('Content_ContentType_ComputerCode');
        $types->add('Content_ContentType_Directory');
        $types->add('Content_ContentType_Flickr');
        $types->add('Content_ContentType_GoogleMap');
        $types->add('Content_ContentType_Heading');
        $types->add('Content_ContentType_Html');
        $types->add('Content_ContentType_JoinPosition');
        $types->add('Content_ContentType_ModuleFunc');
        $types->add('Content_ContentType_OpenStreetMap');
        $types->add('Content_ContentType_PageNavigation');
        $types->add('Content_ContentType_PagesetterPub');
        $types->add('Content_ContentType_PagesetterPublist');
        $types->add('Content_ContentType_Quote');
        $types->add('Content_ContentType_Rss');
        $types->add('Content_ContentType_Slideshare');
        $types->add('Content_ContentType_Vimeo');
        $types->add('Content_ContentType_YouTube');

        // add layout types
        $types->add('Content_LayoutType_Column1');
        $types->add('Content_LayoutType_Column1top');
        $types->add('Content_LayoutType_Column1woheader');
        $types->add('Content_LayoutType_Column2d12');
        $types->add('Content_LayoutType_Column2d2575');
        $types->add('Content_LayoutType_Column2d3070');
        $types->add('Content_LayoutType_Column2d3366');
        $types->add('Content_LayoutType_Column2d3862');
        $types->add('Content_LayoutType_Column2d6238');
        $types->add('Content_LayoutType_Column2d6633');
        $types->add('Content_LayoutType_Column2d7030');
        $types->add('Content_LayoutType_Column2d7525');
        $types->add('Content_LayoutType_Column2header');
        $types->add('Content_LayoutType_Column3d252550');
        $types->add('Content_LayoutType_Column3d255025');
        $types->add('Content_LayoutType_Column3d502525');
        $types->add('Content_LayoutType_Column3header');
    }

}