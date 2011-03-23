<?php

class Content_Form_Handler_Admin_NewPage extends Zikula_Form_AbstractHandler
{
    var $pageId; // Parent or previous page ID or null for new top page
    var $location; // Create 'sub' page or next page (at same level)

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function initialize(Zikula_Form_View $view)
    {
        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);
        $this->location = FormUtil::getPassedValue('loc', isset($this->args['loc']) ? $this->args['loc'] : null);

        if (!SecurityUtil::checkPermission('Content:page:', '::', ACCESS_ADD)) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }

        // Only allow subpages if edit access on parent page
        if (!SecurityUtil::checkPermission('Content:page:', $this->pageId . '::', ACCESS_EDIT)) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }

        if ($this->pageId != null) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false, 'filter' => array('checkActive' => false)));
            if ($page === false) {
                return $this->view->registerError(null);
            }
        } else {
            $page = null;
        }

        $layouts = ModUtil::apiFunc('Content', 'Layout', 'getLayouts');
        if ($layouts === false) {
            return $this->view->registerError(null);
        }

        PageUtil::setVar('title', $this->__('Add new page'));

        $this->view->assign('layouts', $layouts);
        $this->view->assign('page', $page);
        $this->view->assign('location', $this->location);
        if ($this->location == 'sub') {
            $this->view->assign('locationLabel', $this->__('Located below:'));
        } else {
            $this->view->assign('locationLabel', $this->__('Located after:'));
        }
        Content_Util::contentAddAccess($this->view, $this->pageId);

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        if (!SecurityUtil::checkPermission('Content:page:', '::', ACCESS_ADD)) {
            throw new Zikula_Exception_Forbidden($this->__('Error! You have not been granted access to create pages.'));
        }

        if ($args['commandName'] == 'create') {

            $pageData = $this->view->getValues();

            $validators = $this->notifyHooks('content.hook.pages.validate.edit', $pageData, $this->pageId, array(), new Zikula_Hook_ValidationProviders())->getData();
            if (!$validators->hasErrors() && $this->view->isValid()) {
                $id = ModUtil::apiFunc('Content', 'Page', 'newPage', array(
                    'page' => $pageData,
                    'pageId' => $this->pageId,
                    'location' => $this->location));
                if ($id === false) {
                    return false;
                }
                // notify any hooks they may now commit the as the original form has been committed.
                $this->notifyHooks('content.hook.pages.process.edit', $pageData, $this->pageId);
            } else {
                return false;
            }
            $url = ModUtil::url('Content', 'admin', 'editPage', array('pid' => $id));
        } else if ($args['commandName'] == 'cancel') {
            $id = null;
            $url = ModUtil::url('Content', 'admin', 'main');
        }

        return $this->view->redirect($url);
    }
}
