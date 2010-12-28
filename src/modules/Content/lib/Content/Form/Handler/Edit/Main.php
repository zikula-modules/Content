<?php

class Content_Form_Handler_Edit_Main extends Form_Handler
{
    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize($view)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        if (!contentHasPageEditAccess()) {
            return $view->registerError(LogUtil::registerPermissionError());
        }

        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', array('editing' => true, 'filter' => array('checkActive' => false, 'expandedPageIds' => contentMainEditExpandGet()), 'enableEscape' => true, 'translate' => false, 'includeLanguages' => true,
            'orderBy' => 'setLeft'));
        if ($pages === false) {
            return $view->registerError(null);
        }

        PageUtil::setVar('title', __('Page list and content structure', $dom));
        $csssrc = ThemeUtil::getModuleStylesheet('admin', 'admin.css');
        PageUtil::addVar('stylesheet', $csssrc);

        $view->assign('pages', $pages);
        $view->assign('multilingual', ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual'));
        $view->assign('enableVersioning', ModUtil::getVar('Content', 'enableVersioning'));
        $view->assign('language', ZLanguage::getLanguageCode());
        contentAddAccess($view, null);

        return true;
    }

    function handleCommand($view, &$args)
    {
        $url = ModUtil::url('Content', 'Edit', 'main');

        if ($args['commandName'] == 'edit') {
            $url = ModUtil::url('Content', 'Edit', 'editpage', array('pid' => $args['commandArgument']));
        } else if ($args['commandName'] == 'newSubPage') {
            $url = ModUtil::url('Content', 'Edit', 'newpage', array('pid' => $args['commandArgument'], 'loc' => 'sub'));
        } else if ($args['commandName'] == 'newPage') {
            $url = ModUtil::url('Content', 'Edit', 'newpage', array('pid' => $args['commandArgument']));
        } else if ($args['commandName'] == 'clonePage') {
            $url = ModUtil::url('Content', 'Edit', 'clonepage', array('pid' => $args['commandArgument']));
        } else if ($args['commandName'] == 'drag') {
            $srcId = FormUtil::getPassedValue('contentTocDragSrcId', null, 'POST');
            $dstId = FormUtil::getPassedValue('contentTocDragDstId', null, 'POST');
            list ($dummy, $srcId) = explode('_', $srcId);
            list ($dummy, $dstId) = explode('_', $dstId);

            $ok = ModUtil::apiFunc('Content', 'Page', 'drag', array('srcId' => $srcId, 'dstId' => $dstId));
            if (!$ok) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'decIndent') {
            // Decreasing indent is the same as dragging it onto parent page

            $srcId = (int) $args['commandArgument'];
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $srcId));
            if ($page === false) {
                return $view->registerError(null);
            }
            $dstId = (int) $page['parentPageId'];
            if ($dstId != 0) {
                $ok = ModUtil::apiFunc('Content', 'Page', 'drag', array('srcId' => $srcId, 'dstId' => $dstId));
                if (!$ok) {
                    return $view->registerError(null);
                }
            }
        } else if ($args['commandName'] == 'incIndent') {
            $pageId = (int) $args['commandArgument'];
            $ok = ModUtil::apiFunc('Content', 'Page', 'increaseIndent', array('pageId' => $pageId));
            if (!$ok) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'deletePage') {
            $pageId = (int) $args['commandArgument'];
            $ok = ModUtil::apiFunc('Content', 'Page', 'deletePage', array('pageId' => $pageId));
            if ($ok === false) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'history') {
            $pageId = (int) $args['commandArgument'];
            $url = ModUtil::url('Content', 'Edit', 'history', array('pid' => $pageId));
        } else if ($args['commandName'] == 'toggleExpand') {
            $pageId = FormUtil::getPassedValue('contentTogglePageId', null, 'POST');
            contentMainEditExpandToggle($pageId);
        }
        $view->redirect($url);
        return true;
    }
}
