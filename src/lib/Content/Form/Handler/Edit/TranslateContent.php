<?php
class Content_Form_Handler_Edit_TranslateContent extends pnFormHandler
{
  var $contentId;
  var $pageId;
  var $language;
  var $backref;


  function __construct($args)
  {
    $this->args = $args;
  }


  function initialize(&$render)
  {
    $this->contentId = (int)FormUtil::getPassedValue('cid', isset($this->args['cid']) ? $this->args['cid'] : -1);
    $this->language = ZLanguage::getLanguageCode();

    $content = ModUtil::apiFunc('Content', 'Content', 'getContent',
                            array('id' => $this->contentId,
                                  'language' => $this->language,
                                  'translate' => false));
    if ($content === false)
      return $render->pnFormRegisterError(null);

    $this->contentType = ModUtil::apiFunc('Content', 'Content', 'getContentType', $content);
    if ($this->contentType === false)
      return $render->pnFormRegisterError(null);

    $this->pageId = $content['pageId'];

    if (!contentHasPageEditAccess($this->pageId))
      return $render->pnFormRegisterError(LogUtil::registerPermissionError());

    $page = ModUtil::apiFunc('Content', 'Page', 'getPage',
                         array('id' => $this->pageId,
                               'includeContent' => false,
                               'checkActive' => false));
    if ($page === false)
      return $render->pnFormRegisterError(null);

    if ($this->language == $page['language'])
      return $render->pnFormRegisterError(LogUtil::registerError(__("You should not translate item to same language as it's default language.", $dom)))
        ;

        $translationInfo = ModUtil::apiFunc('Content', 'Content', 'getTranslationInfo', array('contentId' => $this->contentId));
        if ($translationInfo === false)
            return $render->pnFormRegisterError(null);

        PageUtil::setVar('title', __("Translate content item", $dom) . ' : ' . $page['title']);

        $templateOriginal = 'file:' . getcwd() . "/modules/$content[module]/templates/contenttype/" . $content['type'] . '_translate_original.html';
        $templateNew = 'file:' . getcwd() . "/modules/$content[module]/templates/contenttype/" . $content['type'] . '_translate_new.html';
        $render->assign('translateOriginalTemplate', $templateOriginal);
        $render->assign('translateNewTemplate', $templateNew);
        $render->assign('page', $page);
        $render->assign('data', $content['data']);
        $render->assign('isTranslatable', $content['isTranslatable']);
        $render->assign('translated', $content['translated']);
        $render->assign('translationInfo', $translationInfo);
        $render->assign('translationStep', $this->contentId);
        $render->assign('language', $this->language);
        $render->assign('contentType', $this->contentType);
        contentAddAccess($render, $this->pageId);

        if (!$render->pnFormIsPostBack() && FormUtil::getPassedValue('back', 0))
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        if ($this->backref != null)
            $returnUrl = $this->backref;
        else
            $returnUrl = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
        ModUtil::apiFunc('PageLock', 'user', 'pageLock', array('lockName' => "contentTranslateContent{$this->contentId}", 'returnUrl' => $returnUrl));

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        $url = null;

        $translationInfo = ModUtil::apiFunc('Content', 'Content', 'getTranslationInfo', array('contentId' => $this->contentId));
        if ($translationInfo === false)
            return $render->pnFormRegisterError(null);

        if ($args['commandName'] == 'next' || $args['commandName'] == 'prev' || $args['commandName'] == 'quit' || $args['commandName'] == null /* Auto postback */)
    {
            if (!$render->pnFormIsValid())
                return false;

            $contentData = $render->pnFormGetValues();

            $ok = ModUtil::apiFunc('Content', 'Content', 'updateTranslation', array('translated' => $contentData['translated'], 'contentId' => $this->contentId, 'language' => $this->language));
            if ($ok === false)
                return $render->pnFormRegisterError(null);

            if ($args['commandName'] == null) {
                $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $contentData['translationStep']));
            } else if ($args['commandName'] == 'next' && $translationInfo['nextContentId'] != null) {
                $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
            } else if ($args['commandName'] == 'prev' && $translationInfo['prevContentId'] == null) {
                $url = ModUtil::url('Content', 'edit', 'translatepage', array('pid' => $this->pageId));
            } else if ($args['commandName'] == 'prev' && $translationInfo['prevContentId'] != null) {
                $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $translationInfo['prevContentId']));
            }
        } else if ($args['commandName'] == 'skip') {
            if ($translationInfo['nextContentId'] != null) {
                $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
            }
        } else if ($args['commandName'] == 'delete') {
            $ok = ModUtil::apiFunc('Content', 'Content', 'deleteTranslation', array('contentId' => $this->contentId, 'language' => $this->language));
            if ($ok === false)
                return $render->pnFormRegisterError(null);
        }

        if ($url == null)
            $url = $this->backref;
        if (empty($url))
            $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));

        ModUtil::apiFunc('PageLock', 'user', 'releaseLock', array('lockName' => "contentTranslateContent{$this->contentId}"));

        return $render->pnFormRedirect($url);
    }
}
