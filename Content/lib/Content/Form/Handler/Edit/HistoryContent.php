<?php
class Content_Form_Handler_Edit_HistoryContent extends pnFormHandler
{
    var $pageId;
    var $backref;

    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('Content');

        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);

        if (!contentHasPageEditAccess($this->pageId))
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'editing' => false, 'filter' => array('checkActive' => false), 'enableEscape' => true, 'translate' => false, 'includeContent' => false, 'includeCategories' => false));
        if ($page === false)
            return $render->pnFormRegisterError(null);

        $versions = ModUtil::apiFunc('Content', 'history', 'getPageVersions', array('pageId' => $this->pageId));
        if ($versions === false)
            return $render->pnFormRegisterError(null);

        $render->assign('page', $page);
        $render->assign('versions', $versions);
        contentAddAccess($render, $this->pageId);

        PageUtil::setVar('title', __("Page history", $dom) . ' : ' . $page['title']);

        if (!$render->pnFormIsPostBack() && FormUtil::getPassedValue('back', 0))
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        $url = null;

        if ($args['commandName'] == 'restore') {
            $ok = ModUtil::apiFunc('Content', 'history', 'restoreVersion', array('id' => $args['commandArgument']));
            if ($ok === false)
                return $render->pnFormRegisterError(null);
        }

        if ($url == null)
            $url = $this->backref;
        if (empty($url))
            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));

        return $render->pnFormRedirect($url);
    }
}
