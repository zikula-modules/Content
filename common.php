<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

function contentHasPageViewAccess($pageId = null)
{
    return SecurityUtil::checkPermission('content:page:', $pageId . '::', ACCESS_READ);
}

function contentHasPageCreateAccess()
{
    return SecurityUtil::checkPermission('content:page:', '::', ACCESS_ADD);
}

function contentHasPageEditAccess($pageId = null)
{
    return SecurityUtil::checkPermission('content:page:', $pageId . '::', ACCESS_EDIT);
}

function contentAddAccess(&$render, $pageId)
{
    $access = array('pageCreateAllowed' => contentHasPageCreateAccess($pageId), 'pageEditAllowed' => contentHasPageEditAccess($pageId));

    $render->assign('access', $access);
}

function contentRegisterGreyBox()
{
    $baseUrl = pnGetBaseURL();
    $rootDir = $baseUrl . 'modules/content/pnincludes/greybox/';

    $header = "
<script type=\"text/javascript\">\nvar GB_ROOT_DIR = '$rootDir';\n</script>
<script type=\"text/javascript\" src=\"modules/content/pnincludes/greybox/AJS.js\"></script>
<script type=\"text/javascript\" src=\"modules/content/pnincludes/greybox/AJS_fx.js\"></script>
<script type=\"text/javascript\" src=\"modules/content/pnincludes/greybox/gb_scripts.js\"></script>
<script type=\"text/javascript\" src=\"modules/content/pnjavascript/content.js\"></script>
";

    // Cannot use addvar(javascript) since "rawtext" is after "javascript"
    // So there's no where to set GB_ROOT_DIR before the javascript files are loaded.
    PageUtil::AddVar('rawtext', $header);
    PageUtil::AddVar('stylesheet', 'modules/content/pnincludes/greybox/gb_styles.css');
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
    $expandedPageIds = SessionUtil::getVar('contentExpandedPageIds', array());
    if (isset($expandedPageIds[$pageId]))
        unset($expandedPageIds[$pageId]);
    else
        $expandedPageIds[$pageId] = 1;
    SessionUtil::setVar('contentExpandedPageIds', $expandedPageIds);
}

function contentMainEditExpandSet($pageId, $value)
{
    $expandedPageIds = SessionUtil::getVar('contentExpandedPageIds', array());
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
