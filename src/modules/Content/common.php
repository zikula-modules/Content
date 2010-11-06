<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
  * @license See license.txt
 */

function contentHasPageViewAccess($pageId = null)
{
    return SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_READ);
}

function contentHasPageCreateAccess($pageId = null)
{
    return SecurityUtil::checkPermission('Content:page:', '::', ACCESS_ADD);
}

function contentHasPageEditAccess($pageId = null)
{
    return SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_EDIT);
}

function contentHasPageDeleteAccess($pageId = null)
{
    return SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_DELETE);
}

function contentAddAccess(&$view, $pageId)
{
    $access = array('pageCreateAllowed'    => contentHasPageCreateAccess($pageId),
                    'pageEditAllowed'      => contentHasPageEditAccess($pageId),
                    'pageDeleteAllowed'    => contentHasPageDeleteAccess($pageId));
    $view->assign('access', $access);
}

// Clear all Content caches. Call this function whenever something has been changed.
// If you add other caching schemes then remember to clear them here
function contentClearCaches()
{
    $view = Zikula_View::getInstance('Content', true);
    // Menu blocks
    $cacheId = 'menu'; // No language: clear all versions
    $view->clear_cache(null, $cacheId);
}

function contentMainEditExpandToggle($pageId)
{
    $expandedPageIds = SessionUtil::getVar('contentExpandedPageIds', array());
    if (isset($expandedPageIds[$pageId])) {
        unset($expandedPageIds[$pageId]);
    } else {
        $expandedPageIds[$pageId] = 1;
    }
    SessionUtil::setVar('contentExpandedPageIds', $expandedPageIds);
}

function contentMainEditExpandSet($pageId, $value)
{
    $expandedPageIds = SessionUtil::getVar('contentExpandedPageIds', array());
    if ($value) {
        $expandedPageIds[$pageId] = 1;
    } else {
        unset($expandedPageIds[$pageId]);
    }
    SessionUtil::setVar('contentExpandedPageIds', $expandedPageIds);
}

function contentMainEditExpandGet()
{
    return SessionUtil::getVar('contentExpandedPageIds', array());
}
