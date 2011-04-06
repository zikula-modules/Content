<?php
/**
 * Content
 *
 * @copyright (C) 2010, Sven Strickroth, TU Clausthal
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

Loader::requireOnce('modules/content/common.php');

function content_mostviewedblock_init()
{
    // Security
    SecurityUtil::registerPermissionSchema('content:mostviewedblock:', 'Block title::');
}

function content_mostviewedblock_info()
{
    $dom = ZLanguage::getModuleDomain('content');
    return array('module'          => 'content',
                 'text_type'       => __('Content Most Viewed', $dom),
                 'text_type_long'  => __('Content Most viewed Pages block', $dom),
                 'allow_multiple'  => true, 
                 'form_content'    => false,
                 'form_refresh'    => false,
                 'show_preview'    => true,
                 'admin_tableless' => true);
}

function content_mostviewedblock_display($blockinfo)
{
    // security check
    if (!SecurityUtil::checkPermission('content:mostviewedblock:', "$blockinfo[title]::", ACCESS_READ)) {
        return;
    }

    // Break out options from our content field
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    // --- Setting of the Defaults
    if (!isset($vars['usecaching'])) {
        $vars['usecaching'] = true;
    }
    if (!isset($vars['root'])) {
        $vars['root'] = 0;
    }
    if (!isset($vars['count'])) {
        $vars['count'] = 10;
    }

    if ($vars['usecaching']) {
        $render = & pnRender::getInstance('content', true);
        $cacheId = 'mostviewed|' . $blockinfo[title] . '|' . ZLanguage::getLanguageCode();
    } else {
        $render = & pnRender::getInstance('content', false);
        $cacheId = null;
    }
    if (!$vars['usecaching'] || ($vars['usecaching'] && !$render->is_cached('content_block_menu.html', $cacheId))) {
        $options = array('orderBy' => 'views', 'orderDir' => 'desc', 'pageSize' => $vars['count'], 'filter' => array());
        if ($vars['root'] > 0) {
            $options['filter']['superParentId'] = $vars['root'];
        }
        $options['filter']['checkInMenu'] = $vars['checkInMenu'];
        $pages = pnModAPIFunc('content', 'page', 'getPages', $options);
        if ($pages === false) {
            return false;
        }
        $render->assign('subPages', $pages);
    }
    $blockinfo['content'] = $render->fetch('content_block_menu.html', $cacheId);
    return pnBlockThemeBlock($blockinfo);
}

function content_mostviewedblock_modify($blockinfo)
{
    $dom = ZLanguage::getModuleDomain('content');
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    if (!isset($vars['usecaching'])) {
        $vars['usecaching'] = true;
    }
    if (!isset($vars['count'])) {
        $vars['count'] = 10;
    }

    $render = & pnRender::getInstance('content', false);
    $render->assign($vars);
    $render->assign('dom', $dom);

    $pages = pnModAPIFunc('content', 'page', 'getPages', array('orderBy' => 'setLeft', 'includeContent' => false));
    $pidItems = array();
    $pidItems[] = array('text' => __('All pages', $dom), 'value' => "0");

    foreach ($pages as $page) {
        $pidItems[] = array('text' => str_repeat('+', $page['level']) . " " . $page['title'], 'value' => $page['id']);
    }
    $render->assign('pidItems', $pidItems);

    return $render->fetch('content_block_mostviewed_modify.html');
}

function content_mostviewedblock_update($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $vars['root'] = (int)FormUtil::getPassedValue('root', 0, 'POST');
    $vars['count'] = (int)FormUtil::getPassedValue('count', 10, 'POST');
    $vars['usecaching'] = (bool)FormUtil::getPassedValue('usecaching', false, 'POST');
    $vars['checkInMenu'] = (bool)FormUtil::getPassedValue('checkinmenu', true, 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    // clear the block cache
    $render = & pnRender::getInstance('content', false);
    $render->clear_cache('content_block_menu.html');

    return $blockinfo;
}
