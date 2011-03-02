<?php

class Content_Form_Handler_Admin_Page extends Zikula_Form_Handler
{
    var $pageId;
    var $backref;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function initialize(Zikula_Form_View $view)
    {
        $this->pageId = (int) FormUtil::getPassedValue('pid', isset($this->args['pid']) ? $this->args['pid'] : -1);

        if (!Content_Util::contentHasPageEditAccess($this->pageId)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array(
            'id' => $this->pageId,
            'editing' => true,
            'filter' => array('checkActive' => false),
            'enableEscape' => false,
            'translate' => false,
            'includeContent' => true,
            'includeCategories' => true));
        if ($page === false) {
            return $view->registerError(null);
        }

        // load the category registry util
        $mainCategory = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page',  $this->getVar('categoryPropPrimary'), 30);
        $secondCategory = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page',  $this->getVar('categoryPropSecondary'));
        $multilingual = ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual');
        if ($page['language'] == ZLanguage::getLanguageCode()) {
            $multilingual = false;
        }

        PageUtil::setVar('title', $this->__("Edit page") . ' : ' . $page['title']);

        $pagelayout = ModUtil::apiFunc('Content', 'Layout', 'getLayout', array('layout' => $page['layout']));
        if ($pagelayout === false) {
            return $view->registerError(null);
        }
        $layouts = ModUtil::apiFunc('Content', 'Layout', 'getLayouts');
        if ($layouts === false) {
            return $view->registerError(null);
        }

        $layoutTemplate = $page['layoutEditTemplate'];
        $view->assign('layoutTemplate', $layoutTemplate);
        $view->assign('mainCategory', $mainCategory);
        $view->assign('secondCategory', $secondCategory);
        $view->assign('page', $page);
        $view->assign('multilingual', $multilingual);
        $view->assign('layouts', $layouts);
        $view->assign('pagelayout', $pagelayout);
        $view->assign('enableVersioning',  $this->getVar('enableVersioning'));
        $view->assign('categoryUsage',  $this->getVar('categoryUsage'));
        Content_Util::contentAddAccess($view, $this->pageId);

        if (!$this->view->isPostBack() && FormUtil::getPassedValue('back', 0)) {
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        }
        if ($this->backref != null) {
            $returnUrl = $this->backref;
        } else {
            $returnUrl = ModUtil::url('Content', 'admin', 'main');
        }
        ModUtil::apiFunc('PageLock', 'User', 'pageLock', array('lockName' => "contentPage{$this->pageId}", 'returnUrl' => $returnUrl));

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        $url = null;

        if ($args['commandName'] == 'save' || $args['commandName'] == 'saveAndView' || $args['commandName'] == 'translate') {
            if (!$view->isValid()) {
                return false;
            }
            $pageData = $view->getValues();

            // fetch old data *before* updating
            $oldPageData = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'editing' => true, 'filter' => array('checkActive' => false), 'enableEscape' => false));
            if ($oldPageData === false) {
                return $view->registerError(null);
            }

            $ok = ModUtil::apiFunc('Content', 'Page', 'updatePage', array('page' => $pageData['page'], 'pageId' => $this->pageId));
            if ($ok === false) {
                return $view->registerError(null);
            }

            if ($args['commandName'] == 'translate') {
                $url = ModUtil::url('Content', 'admin', 'translatepage', array('pid' => $this->pageId));
            } else if ($args['commandName'] == 'saveAndView') {
                $url = ModUtil::url('Content', 'User', 'view', array('pid' => $this->pageId));
            } else if ($oldPageData['layout'] != $pageData['page']['layout']) {
                $url = ModUtil::url('Content', 'admin', 'editpage', array('pid' => $this->pageId));
                LogUtil::registerStatus($this->__('Layout changed'));
            }
        } else if ($args['commandName'] == 'deleteContent') {
            $ok = ModUtil::apiFunc('Content', 'Content', 'deleteContent', array('contentId' => $args['commandArgument']));
            if ($ok === false) {
                return $view->registerError(null);
            }
            $url = ModUtil::url('Content', 'admin', 'editpage', array('pid' => $this->pageId));
        } else if ($args['commandName'] == 'cloneContent') {
            $clonedId = ModUtil::apiFunc('Content', 'Content', 'cloneContent', array('id' => (int) $args['commandArgument'], 'translation' => true));
            if ($clonedId === false) {
                return $view->registerError(null);
            }
            $url = ModUtil::url('Content', 'admin', 'editcontent', array('cid' => $clonedId));
        } else if ($args['commandName'] == 'deletePage') {
            $ok = ModUtil::apiFunc('Content', 'Page', 'deletePage', array('pageId' => $this->pageId));
            if ($ok === false) {
                return $view->registerError(null);
            }
            $url = ModUtil::url('Content', 'admin', 'main');
        } else if ($args['commandName'] == 'cancel') {
        }

        ModUtil::apiFunc('PageLock', 'User', 'releaseLock', array('lockName' => "contentPage{$this->pageId}"));

        if ($url == null) {
            $url = $this->backref;
        }
        if ($url == null) {
            $url = ModUtil::url('Content', 'admin', 'main');
        }
        return $view->redirect($url);
    }
}
