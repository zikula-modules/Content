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
            return $view->registerError(LogUtil::registerPermissionError());
        }

        // Only allow subpages if edit access on parent page
        if (!Content_Util::contentHasPageEditAccess($this->pageId)) {
            return LogUtil::registerPermissionError();
        }

        if ($this->pageId != null) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false, 'filter' => array('checkActive' => false)));
            if ($page === false) {
                return $view->registerError(null);
            }
        } else {
            $page = null;
        }

        $layouts = ModUtil::apiFunc('Content', 'Layout', 'getLayouts');
        if ($layouts === false) {
            return $view->registerError(null);
        }

        PageUtil::setVar('title', $this->__('Add new page'));

        $view->assign('layouts', $layouts);
        $view->assign('page', $page);
        $view->assign('location', $this->location);
        if ($this->location == 'sub') {
            $view->assign('locationLabel', $this->__('Located below:'));
        } else {
            $view->assign('locationLabel', $this->__('Located after:'));
        }
        Content_Util::contentAddAccess($view, $this->pageId);

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        if (!Content_Util::contentHasPageCreateAccess()) {
            return $view->setErrorMsg($this->__('Error! You have not been granted access to create pages.'));
        }

        if ($args['commandName'] == 'create') {
            if (!$view->isValid()) {
                return false;
            }
            $pageData = $view->getValues();
            list($pageData['module'], $pageData['layout']) = explode(":", $pageData['layout']);

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

        return $view->redirect($url);
    }
}
