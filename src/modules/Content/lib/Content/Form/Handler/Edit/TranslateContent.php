<?php
class Content_Form_Handler_Edit_TranslateContent extends Zikula_Form_Handler
{
    var $contentId;
    var $pageId;
    var $language;
    var $backref;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function initialize(Zikula_Form_View $view)
    {
        $this->contentId = (int)FormUtil::getPassedValue('cid', isset($this->args['cid']) ? $this->args['cid'] : -1);
        $this->language = ZLanguage::getLanguageCode();

        $content = ModUtil::apiFunc('Content', 'Content', 'getContent',
                                array('id' => $this->contentId,
                                      'language' => $this->language,
                                      'translate' => false));
        if ($content === false) {
            return $view->registerError(null);
        }

        $this->contentType = ModUtil::apiFunc('Content', 'Content', 'getContentType', $content);
        if ($this->contentType === false) {
            return $view->registerError(null);
        }
        $this->pageId = $content['pageId'];

        if (!Content_Util::contentHasPageEditAccess($this->pageId)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage',
                             array('id' => $this->pageId,
                                   'includeContent' => false,
                                   'filter' => array('checkActive' => false)));
        if ($page === false) {
            return $view->registerError(null);
        }

        if ($this->language == $page['language']) {
            return $this->view->registerError(LogUtil::registerError($this->__f('Sorry, you cannot translate an item to the same language as it\'s default language ("%1$s"). Change the current site language ("%1$s") to some other language on the <a href="%2$s">localisation settings</a> page.<br /> Another way is to add, for instance, <strong>&amp;lang=de</strong> to the url for changing the current site language to German and after that the item can be translated to German.', array($page['language'], $this->language, ModUtil::url('Settings', 'admin', 'multilingual')))));
        }

        $translationInfo = ModUtil::apiFunc('Content', 'Content', 'getTranslationInfo', array('contentId' => $this->contentId));
        if ($translationInfo === false) {
            return $view->registerError(null);
        }

        PageUtil::setVar('title', $this->__("Translate content item") . ' : ' . $page['title']);

        $templates = $this->contentType['plugin']->getTranslationTemplates();
        $templateOriginal = 'file:' . getcwd() . "/modules/$content[module]/templates/" . $templates['original'];
        $templateNew = 'file:' . getcwd() . "/modules/$content[module]/templates/" . $templates['new'];
        $view->assign('translateOriginalTemplate', $templateOriginal);
        $view->assign('translateNewTemplate', $templateNew);
        $view->assign('page', $page);
        $view->assign('data', $content['data']);
        $view->assign('isTranslatable', $content['isTranslatable']);
        $view->assign('translated', $content['translated']);
        $view->assign('translationInfo', $translationInfo);
        $view->assign('translationStep', $this->contentId);
        $view->assign('language', $this->language);
        $view->assign('contentType', $this->contentType);
        Content_Util::contentAddAccess($view, $this->pageId);

        if (!$view->isPostBack() && FormUtil::getPassedValue('back', 0)) {
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        }
        if ($this->backref != null) {
            $returnUrl = $this->backref;
        } else {
            $returnUrl = ModUtil::url('Content', 'Edit', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'user', 'pageLock', array('lockName' => "contentTranslateContent{$this->contentId}", 'returnUrl' => $returnUrl));

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        $url = null;

        $translationInfo = ModUtil::apiFunc('Content', 'Content', 'getTranslationInfo', array('contentId' => $this->contentId));
        if ($translationInfo === false) {
            return $view->registerError(null);
        }

        if ($args['commandName'] == 'next' || $args['commandName'] == 'prev' || $args['commandName'] == 'quit' || $args['commandName'] == null /* Auto postback */) {
            if (!$view->isValid()) {
                return false;
            }
            $contentData = $view->getValues();

            $ok = ModUtil::apiFunc('Content', 'Content', 'updateTranslation', array('translated' => $contentData['translated'], 'contentId' => $this->contentId, 'language' => $this->language));
            if ($ok === false) {
                return $view->registerError(null);
            }
            if ($args['commandName'] == null) {
                $url = ModUtil::url('Content', 'Edit', 'translatecontent', array('cid' => $contentData['translationStep']));
            } else if ($args['commandName'] == 'next' && $translationInfo['nextContentId'] != null) {
                $url = ModUtil::url('Content', 'Edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
            } else if ($args['commandName'] == 'prev' && $translationInfo['prevContentId'] == null) {
                $url = ModUtil::url('Content', 'Edit', 'translatepage', array('pid' => $this->pageId));
            } else if ($args['commandName'] == 'prev' && $translationInfo['prevContentId'] != null) {
                $url = ModUtil::url('Content', 'Edit', 'translatecontent', array('cid' => $translationInfo['prevContentId']));
            }
        } else if ($args['commandName'] == 'skip') {
            if ($translationInfo['nextContentId'] != null) {
                $url = ModUtil::url('Content', 'Edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
            }
        } else if ($args['commandName'] == 'delete') {
            $ok = ModUtil::apiFunc('Content', 'Content', 'deleteTranslation', array('contentId' => $this->contentId, 'language' => $this->language));
            if ($ok === false)
                return $view->registerError(null);
        }

        if ($url == null) {
            $url = $this->backref;
        }
        if (empty($url)) {
            $url = ModUtil::url('Content', 'Edit', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'User', 'releaseLock', array('lockName' => "contentTranslateContent{$this->contentId}"));

        return $view->redirect($url);
    }
}
