<?php

class Content_Form_Handler_Edit_ClonePage extends Form_Handler
{
    var $pageId; // Parent or previous page ID or null for new top page
    var $backref;

    function __construct($args)
    {
        $this->args = $args;
    }

    function initialize($view)
    {
        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);

        if (!contentHasPageCreateAccess()) {
            return $view->registerError(LogUtil::registerPermissionError());
        }
        if (!contentHasPageEditAccess($this->pageId)) {
            return LogUtil::registerPermissionError();
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'filter' => array('checkActive' => false), 'includeContent' => false));
        if ($page === false) {
            return $view->registerError(null);
        }

        // Only allow subpages if edit access on parent page
        if (!contentHasPageEditAccess($page['id'])) {
            return LogUtil::registerPermissionError();
        }

        PageUtil::setVar('title', $this->__('Clone page') . ' : ' . $page['title']);

        $view->assign('page', $page);
        contentAddAccess($view, $this->pageId);

        return true;
    }

    function handleCommand($view, &$args)
    {
        if (!contentHasPageCreateAccess()) {
            return $view->setErrorMsg($this->__('Error! You have not been granted access to create pages.'));
        }

        $url = ModUtil::url('Content', 'Edit', 'Main');

        if ($args['commandName'] == 'clonePage') {
            if (!$view->isValid()) {
                return false;
            }

            $pageData = $view->getValues();
            $id = ModUtil::apiFunc('Content', 'Page', 'clonePage', array('page' => $pageData, 'pageId' => $this->pageId));
            if ($id === false) {
                return $view->registerError(null);
            }
            $url = ModUtil::url('Content', 'Edit', 'editPage', array('pid' => $id));
        } else if ($args['commandName'] == 'cancel') {
        }
        return $view->redirect($url);
    }
}
