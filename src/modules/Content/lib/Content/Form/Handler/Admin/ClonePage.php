<?php

class Content_Form_Handler_Admin_ClonePage extends Zikula_Form_Handler
{
    var $pageId; // Parent or previous page ID or null for new top page
    var $backref;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function initialize(Zikula_Form_View $view)
    {
        $this->pageId = FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : null);

        if (!Content_Util::contentHasPageCreateAccess()) {
            return $this->view->registerError(LogUtil::registerPermissionError());
        }
        if (!Content_Util::contentHasPageEditAccess($this->pageId)) {
            return LogUtil::registerPermissionError();
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'filter' => array('checkActive' => false), 'includeContent' => false));
        if ($page === false) {
            return $this->view->registerError(null);
        }

        // Only allow subpages if edit access on parent page
        if (!Content_Util::contentHasPageEditAccess($page['id'])) {
            return LogUtil::registerPermissionError();
        }

        PageUtil::setVar('title', $this->__('Clone page') . ' : ' . $page['title']);

        $this->view->assign('page', $page);
        Content_Util::contentAddAccess($this->view, $this->pageId);

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        if (!Content_Util::contentHasPageCreateAccess()) {
            return $this->view->setErrorMsg($this->__('Error! You have not been granted access to create pages.'));
        }

        $url = ModUtil::url('Content', 'admin', 'Main');

        if ($args['commandName'] == 'clonePage') {
            if (!$this->view->isValid()) {
                return false;
            }

            $pageData = $this->view->getValues();
            $id = ModUtil::apiFunc('Content', 'Page', 'clonePage', array('page' => $pageData, 'pageId' => $this->pageId));
            if ($id === false) {
                return $this->view->registerError(null);
            }
            $url = ModUtil::url('Content', 'admin', 'editPage', array('pid' => $id));
        } else if ($args['commandName'] == 'cancel') {
        }
        return $this->view->redirect($url);
    }
}
