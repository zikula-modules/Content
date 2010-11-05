<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

Loader::requireOnce('modules/content/common.php');

function content_ajax_dragcontent($args)
{
    $dom = ZLanguage::getModuleDomain('content');
    $ok = pnModAPIFunc('content', 'content', 'dragcontent', array('pageId' => FormUtil::getPassedValue('pid', null, 'P'), 'contentId' => FormUtil::getPassedValue('cid', null, 'P'), 'contentAreaIndex' => FormUtil::getPassedValue('cai', null, 'P'),
        'position' => FormUtil::getPassedValue('pos', null, 'P')));
    if (!$ok) {
        return array('ok' => false, 'message' => LogUtil::getErrorMessagesText());
    }

    return array('ok' => true, 'message' => __('OK', $dom));
}

/**
 * togglepagestate
 * This function toggles online/offline
 *
 * @author Erik Spaan
 * @param id int  id of page to toggle
 * @return mixed true or Ajax error
 */
function content_ajax_togglepagestate($args)
{
    if (!SecurityUtil::checkPermission('content::', '::', ACCESS_EDIT)) {
        AjaxUtil::error(__('Sorry! You have not been granted access to this page.'));
    }

    $dom = ZLanguage::getModuleDomain('content');

    $id = FormUtil::getPassedValue('id', -1, 'GET');
    if ($id == -1) {
        LogUtil::registerError(__('No page ID passed.'));
        AjaxUtil::output();
    }

    // read the page information
    $pageData = pnModAPIFunc('content', 'page', 'getPage', array('id' => $id, 'filter' => array('checkActive' => false), 'enableEscape' => false, 'includeContent' => false, 'includeLanguages' => false));
    if ($pageData === false) {
        LogUtil::registerError(__f('Error! Could not retrieve page with ID %s.', DataUtil::formatForDisplay($id), $dom));
        AjaxUtil::output();
    }
    // toggle the active state
    if ($pageData['active'] == 1) {
        $active = 0;
    } else {
        $active = 1;
    }

    $ok = pnModAPIFunc('content', 'page', 'updateState', array('pageId' => $id, 'active' => $active, 'inMenu' => $pageData['inMenu']));
    if (!$ok) {
        LogUtil::registerError(__('Error! Could not update state.', $dom));
        AjaxUtil::output();
    }
    AjaxUtil::output(array('id' => $id));
}

/**
 * togglepageinmenu
 * This function toggles inmenu/outmenu
 *
 * @author Erik Spaan
 * @param id int  id of page to toggle
 * @return mixed true or Ajax error
 */
function content_ajax_togglepageinmenu($args)
{
    if (!SecurityUtil::checkPermission('content::', '::', ACCESS_EDIT)) {
        AjaxUtil::error(__('Sorry! You have not been granted access to this page.'));
    }

    $dom = ZLanguage::getModuleDomain('content');

    $id = FormUtil::getPassedValue('id', -1, 'GET');
    if ($id == -1) {
        LogUtil::registerError(__('No page ID passed.'));
        AjaxUtil::output();
    }

    // read the page information
    $pageData = pnModAPIFunc('content', 'page', 'getPage', array('id' => $id, 'filter' => array('checkActive' => false), 'enableEscape' => false, 'includeContent' => false, 'includeLanguages' => false));
    if ($pageData === false) {
        LogUtil::registerError(__f('Error! Could not retrieve page with ID %s.', DataUtil::formatForDisplay($id), $dom));
        AjaxUtil::output();
    }
    // toggle the inMenu state
    if ($pageData['inMenu'] == 1) {
        $inMenu = 0;
    } else {
        $inMenu = 1;
    }

    $ok = pnModAPIFunc('content', 'page', 'updateState', array('pageId' => $id, 'active' => $pageData['active'], 'inMenu' => $inMenu));
    if (!$ok) {
        LogUtil::registerError(__('Error! Could not update state.', $dom));
        AjaxUtil::output();
    }
    AjaxUtil::output(array('id' => $id));
}
