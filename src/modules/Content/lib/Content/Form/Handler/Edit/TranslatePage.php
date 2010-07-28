<?php
class Content_Form_Handler_Edit_TranslatePage extends Form_Handler
{
    var $pageId;
    var $language;
    var $backref;

    function initialize($view)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->pageId = (int) FormUtil::getPassedValue('pid', -1);
        $this->language = ZLanguage::getLanguageCode();

        if (!contentHasPageEditAccess($this->pageId)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false, 'checkActive' => false, 'translate' => false));
        if ($page === false) {
            return $view->registerError(null);
        }

        if ($this->language == $page['language']) {
            return $this->view->registerError(LogUtil::registerError(__f('Sorry, you cannot translate an item to the same language as it\'s default language ("%s"). Change the current site language ("%s") to some other language on the <a href="%s">localisation settings</a> page.<br /> Another way is to add, for instance, <strong>&amp;lang=de</strong> to the url for changing the current site language to German and after that the item can be translated to German.', array($page['language'], $this->language, ModUtil::url('Settings', 'admin', 'multilingual')), $dom)));
        }

        PageUtil::setVar('title', __("Translate page", $dom) . ' : ' . $page['title']);

        $view->assign('page', $page);
        $view->assign('translated', $page['translated']);
        $view->assign('language', $this->language);
        contentAddAccess($view, $this->pageId);

        if (!$this->view->isPostBack() && FormUtil::getPassedValue('back',0)) {
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        }

        if ($this->backref != null) {
            $returnUrl = $this->backref;
        } else {
            $returnUrl = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'user', 'pageLock',
                     array('lockName' => "contentTranslatePage{$this->pageId}",
                           'returnUrl' => $returnUrl));

        return true;
    }

    function handleCommand($view, &$args)
    {
        $url = null;

        $translationInfo = ModUtil::apiFunc('Content', 'Content', 'getTranslationInfo', array('pageId' => $this->pageId));
        if ($translationInfo === false) {
            return $view->registerError(null);
        }

        if ($args['commandName'] == 'next' || $args['commandName'] == 'quit') {
            if (!$view->isValid()) {
                return false;
            }

            $pageData = $view->getValues();

            $ok = ModUtil::apiFunc('Content', 'Page', 'updateTranslation',
                               array('translated' => $pageData['translated'],
                                     'pageId' => $this->pageId,
                                     'language' => $this->language));
            if ($ok === false) {
                return $view->registerError(null);
            }

            if ($args['commandName'] == 'next' && $translationInfo['nextContentId'] != null) {
                $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
            }
        } else if ($args['commandName'] == 'skip') {
            if ($translationInfo['nextContentId'] != null) {
              $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
            }
        } else if ($args['commandName'] == 'delete') {
            $ok = ModUtil::apiFunc('Content', 'Page', 'deleteTranslation',
                               array('pageId' => $this->pageId,
                                     'language' => $this->language));
            if ($ok === false) {
                return $view->registerError(null);
            }
        }

        if ($url == null) {
            $url = $this->backref;
        }
        if (empty($url)) {
            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'user', 'releaseLock',
                   array('lockName' => "contentTranslatePage{$this->pageId}"));

        return $view->redirect($url);
    }
}

