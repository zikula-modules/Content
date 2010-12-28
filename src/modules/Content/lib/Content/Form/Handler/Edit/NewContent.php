<?php

class Content_Form_Handler_Edit_NewContent extends Form_Handler
{
    // Set these three for new content in empty area (or always first position)
    var $pageId; // ID of page to insert content on
    var $contentAreaIndex; // Index of the content are where new content is to be inserted
    var $position; // Position of new content inside above area (insert at this position)

    // Set these two for content relatively positioned to exiting content
    var $contentId; // ID of content we are creating new item relative to
    var $above; // Position relative to $contentid (above=0 => below)

    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize($view)
    {
        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);
        $this->contentAreaIndex = FormUtil::getPassedValue('cai', isset($this->args['cai']) ? $this->args['cai'] : null);
        $this->position = FormUtil::getPassedValue('pos', isset($this->args['pos']) ? $this->args['pos'] : 0);
        $this->contentId = FormUtil::getPassedValue('cid', isset($this->args['cid']) ? $this->args['cid'] : null);
        $this->above = FormUtil::getPassedValue('above', isset($this->args['above']) ? $this->args['above'] : 0);

        if ($this->contentId != null) {
            $content = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $this->contentId));
            if ($content === false) {
                return $view->registerError(null);
            }
            $this->pageId = $content['pageId'];
            $this->contentAreaIndex = $content['areaIndex'];
            $this->position = ($this->above ? $content['position'] : $content['position'] + 1);
        }

        if (!contentHasPageEditAccess($this->pageId)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }
        if ($this->pageId == null) {
            return $view->setErrorMsg($this->__("Missing page ID (pid) in URL"));
        }

        if ($this->contentAreaIndex == null) {
            return $view->setErrorMsg($this->__("Missing content area index (cai) in URL"));
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'filter' => array('checkActive' => false)));
        if ($page === false) {
            return $view->registerError(null);
        }

        PageUtil::setVar('title', $this->__("Add new content to page") . ' : ' . $page['title']);

        $view->assign('page', $page);
        $view->assign('htmlBody', 'content_edit_newcontent.html');
        contentAddAccess($view, $this->pageId);

        return true;
    }

    function handleCommand($view, &$args)
    {
        if ($args['commandName'] == 'create') {
            if (!$view->isValid()) {
                return false;
            }

            $contentData = $view->getValues();
            list ($module, $type) = explode(':', $contentData['contentType']);
            $contentData['module'] = $module;
            $contentData['type'] = $type;
            unset($contentData['contentType']);
            $contentData['language'] = null;

            $id = ModUtil::apiFunc('Content', 'Content', 'newContent', array('content' => $contentData, 'pageId' => $this->pageId, 'contentAreaIndex' => $this->contentAreaIndex, 'position' => $this->position));
            if ($id === false) {
                return $view->registerError(null);
            }

            $url = ModUtil::url('Content', 'Edit', 'editcontent', array('cid' => $id));
        } else if ($args['commandName'] == 'cancel') {
            $id = null;
            $url = ModUtil::url('Content', 'Edit', 'editpage', array('pid' => $this->pageId));
        }

        return $view->redirect($url);
    }
}
