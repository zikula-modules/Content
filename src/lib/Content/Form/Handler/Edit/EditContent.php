<?php

class Content_Form_Handler_Edit_EditContent extends pnFormHandler
{
    var $contentId;
    var $pageId;
    var $backref;

    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->contentId = (int) FormUtil::getPassedValue('cid', isset($this->args['cid']) ? $this->args['cid'] : -1);

        $content = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $this->contentId, 'translate' => false));
        if ($content === false)
            return $render->pnFormRegisterError(null);

        $this->contentType = ModUtil::apiFunc('Content', 'Content', 'getContentType', $content);
        if ($this->contentType === false)
            return $render->pnFormRegisterError(null);

        $this->contentType['plugin']->startEditing($render);

        $this->pageId = $content['pageId'];

        if (!contentHasPageEditAccess($this->pageId))
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false, 'checkActive' => false));
        if ($page === false)
            return $render->pnFormRegisterError(null);

        $multilingual = ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual');
        if ($page['language'] == ZLanguage::getLanguageCode())
            $multilingual = false;

        PageUtil::setVar('title', __("Edit content item", $dom) . ' : ' . $page['title']);

        $template = 'file:' . getcwd() . "/modules/$content[module]/templates/contenttype/" . $content['type'] . '_edit.html';
        $render->assign('contentTypeTemplate', $template);
        $render->assign('page', $page);
        $render->assign('content', $content);
        $render->assign('data', $content['data']);
        $render->assign('contentType', $this->contentType);
        $render->assign('multilingual', $multilingual);
        $render->assign('enableVersioning', ModUtil::getVar('Content', 'enableVersioning'));
        contentAddAccess($render, $this->pageId);

        if (!$render->pnFormIsPostBack() && FormUtil::getPassedValue('back', 0))
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        if ($this->backref != null)
            $returnUrl = $this->backref;
        else
            $returnUrl = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
        ModUtil::apiFunc('PageLock', 'user', 'pageLock', array('lockName' => "contentContent{$this->contentId}", 'returnUrl' => $returnUrl));

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        $url = null;

        if ($args['commandName'] == 'save' || $args['commandName'] == 'translate') {
            if (!$render->pnFormIsValid())
                return false;
            $contentData = $render->pnFormGetValues();

            $message = null;
            if (!$this->contentType['plugin']->isValid($contentData['data'], $message)) {
                $errorPlugin = &$render->pnFormGetPluginById('error');
                $errorPlugin->message = $message;
                return false;
            }

            $this->contentType['plugin']->loadData($contentData['data']);

            $ok = ModUtil::apiFunc('Content', 'Content', 'updateContent', array('content' => $contentData + $contentData['content'], 'searchableText' => $this->contentType['plugin']->getSearchableText(), 'id' => $this->contentId));
            if ($ok === false)
                return $render->pnFormRegisterError(null);

            if ($args['commandName'] == 'translate')
                $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $this->contentId, 'back' => 1));
        } else if ($args['commandName'] == 'delete') {
            $ok = ModUtil::apiFunc('Content', 'Content', 'deleteContent', array('contentId' => $this->contentId));
            if ($ok === false)
                return $render->pnFormRegisterError(null);
        } else if ($args['commandName'] == 'cancel') {
        }

        if ($url == null)
            $url = $this->backref;
        if (empty($url))
            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));

        ModUtil::apiFunc('PageLock', 'user', 'releaseLock', array('lockName' => "contentContent{$this->contentId}"));

        return $render->pnFormRedirect($url);
    }

    function handleSomethingChanged(&$render, &$args)
    {
        $contentData = $render->pnFormGetValues();
        $this->contentType['plugin']->handleSomethingChanged($render, $contentData['data']);
    }
}
