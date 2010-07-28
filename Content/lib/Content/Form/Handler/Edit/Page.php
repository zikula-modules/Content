<?php

class Content_Form_Handler_Edit_Page extends pnFormHandler
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
        $this->pageId = (int) FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : -1);

        if (!contentHasPageEditAccess($this->pageId))
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'editing' => true, 'filter' => array('checkActive' => false), 'enableEscape' => false, 'translate' => false, 'includeContent' => true, 'includeCategories' => true));
        if ($page === false)
            return $render->pnFormRegisterError(null);

        // load the category registry util
        $mainCategory = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page', 'primary', 30); // 30 == /__SYSTEM__/Modules/Global

        $multilingual = ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual');
        if ($page['language'] == ZLanguage::getLanguageCode())
            $multilingual = false;

        PageUtil::setVar('title', __("Edit page", $dom) . ' : ' . $page['title']);

        $pagelayout = ModUtil::apiFunc('Content', 'layout', 'getLayout', array('layout' => $page['layout']));
        if ($pagelayout === false)
            return $render->pnFormRegisterError(null);
        $layouts = ModUtil::apiFunc('Content', 'layout', 'getLayouts');
        if ($layouts === false)
            return $render->pnFormRegisterError(null);
        
        $layoutTemplate = 'layout/' . $page['layoutData']['name'] . '_edit.html';
        $render->assign('layoutTemplate', $layoutTemplate);
        $render->assign('mainCategory', $mainCategory);
        $render->assign('page', $page);
        $render->assign('multilingual', $multilingual);
        $render->assign('layouts', $layouts);
        $render->assign('pagelayout', $pagelayout);
        $render->assign('enableVersioning', ModUtil::getVar('Content', 'enableVersioning'));
        contentAddAccess($render, $this->pageId);

        if (!$render->pnFormIsPostBack() && FormUtil::getPassedValue('back', 0))
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        if ($this->backref != null)
            $returnUrl = $this->backref;
        else
            $returnUrl = ModUtil::url('Content', 'edit', 'main');
        ModUtil::apiFunc('PageLock', 'user', 'pageLock', array('lockName' => "contentPage{$this->pageId}", 'returnUrl' => $returnUrl));

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $url = null;

        if ($args['commandName'] == 'save' || $args['commandName'] == 'saveAndView' || $args['commandName'] == 'translate') {
            if (!$render->pnFormIsValid())
                return false;

            $pageData = $render->pnFormGetValues();

            // fetch old data *before* updating
            $oldPageData = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'editing' => true, 'filter' => array('checkActive' => false), 'enableEscape' => false));
            if ($oldPageData === false)
                return $render->pnFormRegisterError(null);

            $ok = ModUtil::apiFunc('Content', 'Page', 'updatePage', array('page' => $pageData['page'], 'pageId' => $this->pageId));
            if ($ok === false)
                return $render->pnFormRegisterError(null);

            if ($args['commandName'] == 'translate')
                $url = ModUtil::url('Content', 'edit', 'translatepage', array('pid' => $this->pageId));
            else if ($args['commandName'] == 'saveAndView')
                $url = ModUtil::url('Content', 'user', 'view', array('pid' => $this->pageId));
            else if ($oldPageData['layout'] != $pageData['page']['layout']) {
                $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
                LogUtil::registerStatus(__('Layout changed', $dom));
            }
        } else if ($args['commandName'] == 'deleteContent') {
            $ok = ModUtil::apiFunc('Content', 'Content', 'deleteContent', array('contentId' => $args['commandArgument']));
            if ($ok === false)
                return $render->pnFormRegisterError(null);

            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
        } else if ($args['commandName'] == 'deletePage') {
            $ok = ModUtil::apiFunc('Content', 'Page', 'deletePage', array('pageId' => $this->pageId));
            if ($ok === false)
                return $render->pnFormRegisterError(null);

            $url = ModUtil::url('Content', 'edit', 'main');
        } else if ($args['commandName'] == 'cancel') {
        }

        ModUtil::apiFunc('PageLock', 'user', 'releaseLock', array('lockName' => "contentPage{$this->pageId}"));

        if ($url == null)
            $url = $this->backref;
        if ($url == null)
            $url = ModUtil::url('Content', 'edit', 'main');
        return $render->pnFormRedirect($url);
    }
}
