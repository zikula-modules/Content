<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

function contentHasPageViewAccess($pageId = null)
{
    return SecurityUtil::checkPermission('content:page:', $pageId . '::', ACCESS_READ);
}

function contentHasPageCreateAccess($pageId = null)
{
    return SecurityUtil::checkPermission('content:page:', '::', ACCESS_ADD);
}

function contentHasPageEditAccess($pageId = null)
{
    return SecurityUtil::checkPermission('content:page:', $pageId . '::', ACCESS_EDIT);
}

function contentHasPageDeleteAccess($pageId = null)
{
    return SecurityUtil::checkPermission('content:page:', $pageId . '::', ACCESS_DELETE);
}

function contentAddAccess(&$render, $pageId)
{
    $access = array('pageCreateAllowed' => contentHasPageCreateAccess($pageId), 
                    'pageEditAllowed'   => contentHasPageEditAccess($pageId),
                    'pageDeleteAllowed' => contentHasPageDeleteAccess($pageId));
    $render->assign('access', $access);
}

// Clear all Content caches. Call this function whenever something has been changed.
// If you add other caching schemes then remember to clear them here
function contentClearCaches()
{
    $render = & pnRender::getInstance('content', true);

    // Menu blocks
    $cacheId = 'menu'; // No language: clear all versions
    $render->clear_cache(null, $cacheId);
}

function contentMainEditExpandToggle($pageId)
{
    $expandedPageIds = contentMainEditExpandGet();
    if (isset($expandedPageIds[$pageId]))
        unset($expandedPageIds[$pageId]);
    else
        $expandedPageIds[$pageId] = 1;
    SessionUtil::setVar('contentExpandedPageIds', $expandedPageIds);
}

function contentMainEditExpandSet($pageId, $value)
{
    $expandedPageIds = contentMainEditExpandGet();
    if ($value)
        $expandedPageIds[$pageId] = 1;
    else
        unset($expandedPageIds[$pageId]);
    SessionUtil::setVar('contentExpandedPageIds', $expandedPageIds);
}

function contentMainEditExpandGet()
{
    return SessionUtil::getVar('contentExpandedPageIds', array());
}
