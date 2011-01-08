<?php

class Content_Form_Handler_Edit_EditContent extends Form_Handler
{
    var $contentId;
    var $pageId;
    var $backref;

    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize($view)
    {
        $this->contentId = (int) FormUtil::getPassedValue('cid', isset($this->args['cid']) ? $this->args['cid'] : -1);

        $content = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $this->contentId, 'translate' => false));
        if ($content === false) {
            return $view->registerError(null);
        }

        $this->contentType = ModUtil::apiFunc('Content', 'Content', 'getContentType', $content);
        if ($this->contentType === false) {
            return $view->registerError(null);
        }

        $this->contentType['plugin']->startEditing($view);
        $this->pageId = $content['pageId'];

        if (!contentHasPageEditAccess($this->pageId)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false, 'filter' => array('checkActive' => false)));
        if ($page === false) {
            return $view->registerError(null);
        }

        $multilingual = ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual');
        if ($page['language'] == ZLanguage::getLanguageCode())
            $multilingual = false;

        PageUtil::setVar('title', $this->__("Edit content item") . ' : ' . $page['title']);

        $template = 'file:' . getcwd() . "/modules/$content[module]/templates/contenttype/" . $content['type'] . '_edit.html';
        $view->assign('contentTypeTemplate', $template);
        $view->assign('page', $page);
        $view->assign('visiblefors', array(array('text' => $this->__('public (all)'), 'value' => '1'), array('text' => $this->__('only logged in members'), 'value' => '0'), array('text' => $this->__('only non logged in people'), 'value' => '2')));
        $view->assign('content', $content);
        $view->assign('data', $content['data']);
        $view->assign('contentType', $this->contentType);
        $view->assign('multilingual', $multilingual);
        $view->assign('enableVersioning',  ModUtil::getVar('Content', 'enableVersioning'));
        contentAddAccess($view, $this->pageId);

        if (!$this->view->isPostBack() && FormUtil::getPassedValue('back', 0)) {
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        }
        if ($this->backref != null) {
            $returnUrl = $this->backref;
        } else {
            $returnUrl = ModUtil::url('Content', 'Edit', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'user', 'pageLock', array('lockName' => "contentContent{$this->contentId}", 'returnUrl' => $returnUrl));

        return true;
    }

    function handleCommand($view, &$args)
    {
        $url = null;

        if ($args['commandName'] == 'save' || $args['commandName'] == 'translate') {
            if (!$view->isValid()) {
                return false;
            }
            $contentData = $view->getValues();

            $message = null;
            if (!$this->contentType['plugin']->isValid($contentData['data'], $message)) {
                $errorPlugin = &$view->getPluginById('error');
                $errorPlugin->message = $message;
                return false;
            }

            $this->contentType['plugin']->loadData($contentData['data']);

            $ok = ModUtil::apiFunc('Content', 'Content', 'updateContent', array('content' => $contentData + $contentData['content'], 'searchableText' => $this->contentType['plugin']->getSearchableText(), 'id' => $this->contentId));
            if ($ok === false) {
                return $view->registerError(null);
            }
            if ($args['commandName'] == 'translate') {
                $url = ModUtil::url('Content', 'Edit', 'translatecontent', array('cid' => $this->contentId, 'back' => 1));
            }
        } else if ($args['commandName'] == 'delete') {
            $ok = ModUtil::apiFunc('Content', 'Content', 'deleteContent', array('contentId' => $this->contentId));
            if ($ok === false) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'cancel') {
        }

        if ($url == null) {
            $url = $this->backref;
        }
        if (empty($url)) {
            $url = ModUtil::url('Content', 'Edit', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'user', 'releaseLock', array('lockName' => "contentContent{$this->contentId}"));

        return $view->redirect($url);
    }

    function handleSomethingChanged(&$view, &$args)
    {
        $contentData = $view->getValues();
        $this->contentType['plugin']->handleSomethingChanged($view, $contentData['data']);
    }
}
