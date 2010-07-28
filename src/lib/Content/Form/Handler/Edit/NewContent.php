<?php

class Content_Form_Handler_Edit_NewContent extends pnFormHandler
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

    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);
        $this->contentAreaIndex = FormUtil::getPassedValue('cai', isset($this->args['cai']) ? $this->args['cai'] : null);
        $this->position = FormUtil::getPassedValue('pos', isset($this->args['pos']) ? $this->args['pos'] : 0);
        $this->contentId = FormUtil::getPassedValue('cid', isset($this->args['cid']) ? $this->args['cid'] : null);
        $this->above = FormUtil::getPassedValue('above', isset($this->args['above']) ? $this->args['above'] : 0);

        if ($this->contentId != null) {
            $content = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $this->contentId));
            if ($content === false)
                return $render->pnFormRegisterError(null);

            $this->pageId = $content['pageId'];
            $this->contentAreaIndex = $content['areaIndex'];
            $this->position = ($this->above ? $content['position'] : $content['position'] + 1);
        }

        if (!contentHasPageEditAccess($this->pageId))
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());

        if ($this->pageId == null)
            return $render->pnFormSetErrorMsg("Missing page ID (pid) in URL");

        if ($this->contentAreaIndex == null)
            return $render->pnFormSetErrorMsg("Missing content area index (cai) in URL");

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'checkActive' => false));
        if ($page === false)
            return $render->pnFormRegisterError(null);

        PageUtil::setVar('title', __("Add new content to page", $dom) . ' : ' . $page['title']);

        $render->assign('page', $page);
        $render->assign('htmlBody', 'content_edit_newcontent.html');
        contentAddAccess($render, $this->pageId);

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        if ($args['commandName'] == 'create') {
            if (!$render->pnFormIsValid())
                return false;

            $contentData = $render->pnFormGetValues();
            list ($module, $type) = explode(':', $contentData['contentType']);
            $contentData['module'] = $module;
            $contentData['type'] = $type;
            unset($contentData['contentType']);
            $contentData['language'] = null;

            $id = ModUtil::apiFunc('Content', 'Content', 'newContent', array('content' => $contentData, 'pageId' => $this->pageId, 'contentAreaIndex' => $this->contentAreaIndex, 'position' => $this->position));
            if ($id === false)
                return $render->pnFormRegisterError(null);

            $url = ModUtil::url('Content', 'edit', 'editcontent', array('cid' => $id));
        } else if ($args['commandName'] == 'cancel') {
            $id = null;
            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
        }

        return $render->pnFormRedirect($url);
    }
}
