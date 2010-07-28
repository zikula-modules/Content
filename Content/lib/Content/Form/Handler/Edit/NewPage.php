<?php

class Content_Form_Handler_Edit_NewPage extends pnFormHandler
{
    var $pageId; // Parent or previous page ID or null for new top page
    var $location; // Create 'sub' page or next page (at same level)


    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);
        $this->location = FormUtil::getPassedValue('loc', isset($this->args['loc']) ? $this->args['loc'] : null);

        if (!contentHasPageCreateAccess())
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());

        // Only allow subpages if edit access on parent page
        if (!contentHasPageEditAccess($this->pageId))
            return LogUtil::registerPermissionError();

        if ($this->pageId != null) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false));
            if ($page === false)
                return $render->pnFormRegisterError(null);
        } else
            $page = null;

        $layouts = ModUtil::apiFunc('Content', 'layout', 'getLayouts');
        if ($layouts === false)
            return $render->pnFormRegisterError(null);

        PageUtil::setVar('title', __('Add new page', $dom));

        $render->assign('layouts', $layouts);
        $render->assign('page', $page);
        $render->assign('location', $this->location);
        if ($this->location == 'sub')
            $render->assign('locationLabel', __('Located below:', $dom));
        else
            $render->assign('locationLabel', __('Located after:', $dom));
        contentAddAccess($render, $this->pageId);

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        if (!contentHasPageCreateAccess())
            return $render->pnFormSetErrorMsg(__('Error! You have not been granted access to this page.', $dom));

        if ($args['commandName'] == 'create') {
            if (!$render->pnFormIsValid())
                return false;

            $pageData = $render->pnFormGetValues();

            $id = ModUtil::apiFunc('Content', 'Page', 'newPage', array('page' => $pageData, 'pageId' => $this->pageId, 'location' => $this->location));
            if ($id === false)
                return false;

            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $id));
        } else if ($args['commandName'] == 'cancel') {
            $id = null;
            $url = ModUtil::url('Content', 'edit', 'main');
        }

        return $render->pnFormRedirect($url);
    }
}
