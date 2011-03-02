<?php

class Content_Form_Handler_Admin_NewPage extends Zikula_Form_Handler
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

        if (!Content_Util::contentHasPageCreateAccess()) {
            return $this->view->registerError(LogUtil::registerPermissionError());
        }

        // Only allow subpages if edit access on parent page
        if (!Content_Util::contentHasPageEditAccess($this->pageId)) {
            return LogUtil::registerPermissionError();
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
        if (!Content_Util::contentHasPageCreateAccess()) {
            return $this->view->setErrorMsg($this->__('Error! You have not been granted access to create pages.'));
        }

        if ($args['commandName'] == 'create') {
            if (!$this->view->isValid()) {
                return false;
            }
            $pageData = $this->view->getValues();

            $id = ModUtil::apiFunc('Content', 'Page', 'newPage', array(
                'page' => $pageData,
                'pageId' => $this->pageId,
                'location' => $this->location));
            if ($id === false) {
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
