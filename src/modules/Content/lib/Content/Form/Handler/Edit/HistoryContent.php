<?php
class Content_Form_Handler_Edit_HistoryContent extends Form_Handler
{
    var $pageId;
    var $backref;

    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize($view)
    {
        $dom = ZLanguage::getModuleDomain('Content');

        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);

        if (!contentHasPageEditAccess($this->pageId)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }
        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'editing' => false, 'filter' => array('checkActive' => false), 'enableEscape' => true, 'translate' => false, 'includeContent' => false, 'includeCategories' => false));
        if ($page === false) {
            return $view->registerError(null);
        }

        $versions = ModUtil::apiFunc('Content', 'history', 'getPageVersions', array('pageId' => $this->pageId));
        if ($versions === false) {
            return $view->registerError(null);
        }

        $view->assign('page', $page);
        $view->assign('versions', $versions);
        contentAddAccess($view, $this->pageId);

        PageUtil::setVar('title', __("Page history", $dom) . ' : ' . $page['title']);

        if (!$this->view->isPostBack() && FormUtil::getPassedValue('back', 0)) {
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        }

        return true;
    }

    function handleCommand($view, &$args)
    {
        $url = null;

        if ($args['commandName'] == 'restore') {
            $ok = ModUtil::apiFunc('Content', 'history', 'restoreVersion', array('id' => $args['commandArgument']));
            if ($ok === false) {
                return $view->registerError(null);
            }
        }

        if ($url == null) {
            $url = $this->backref;
        }
        if (empty($url)) {
            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
        }

        return $view->redirect($url);
    }
}
