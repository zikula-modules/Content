<?php
class Content_Form_Handler_Edit_TranslatePage extends pnFormHandler
{
    var $pageId;
    var $language;
    var $backref;

    function initialize(&$render)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->pageId = (int) FormUtil::getPassedValue('pid', -1);
        $this->language = ZLanguage::getLanguageCode();

        if (!contentHasPageEditAccess($this->pageId))
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false, 'checkActive' => false, 'translate' => false));
        if ($page === false)
            return $render->pnFormRegisterError(null);

        if ($this->language == $page['language'])
            return $render->pnFormRegisterError(LogUtil::registerError(__("You should not translate item to same language as it's default language.", $dom)));

    PageUtil::setVar('title', __("Translate page", $dom) . ' : ' . $page['title']);

    $render->assign('page', $page);
    $render->assign('translated', $page['translated']);
    $render->assign('language', $this->language);
    contentAddAccess($render, $this->pageId);

    if (!$render->pnFormIsPostBack() && FormUtil::getPassedValue('back',0))
      $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

    if ($this->backref != null)
      $returnUrl = $this->backref;
    else
      $returnUrl = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));
    ModUtil::apiFunc('PageLock', 'user', 'pageLock',
                 array('lockName' => "contentTranslatePage{$this->pageId}",
                       'returnUrl' => $returnUrl));

    return true;
  }


  function handleCommand(&$render, &$args)
  {
    $url = null;

    $translationInfo = ModUtil::apiFunc('Content', 'Content', 'getTranslationInfo',
                                    array('pageId' => $this->pageId));
    if ($translationInfo === false)
      return $render->pnFormRegisterError(null);

    if ($args['commandName'] == 'next' || $args['commandName'] == 'quit')
    {
      if (!$render->pnFormIsValid())
        return false;

      $pageData = $render->pnFormGetValues();

      $ok = ModUtil::apiFunc('Content', 'Page', 'updateTranslation',
                         array('translated' => $pageData['translated'],
                               'pageId' => $this->pageId,
                               'language' => $this->language));
      if ($ok === false)
        return $render->pnFormRegisterError(null);

      if ($args['commandName'] == 'next' && $translationInfo['nextContentId'] != null)
      {
        $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
      }
    }
    else if ($args['commandName'] == 'skip')
    {
      if ($translationInfo['nextContentId'] != null)
      {
        $url = ModUtil::url('Content', 'edit', 'translatecontent', array('cid' => $translationInfo['nextContentId']));
      }
    }
    else if ($args['commandName'] == 'delete')
    {
      $ok = ModUtil::apiFunc('Content', 'Page', 'deleteTranslation',
                         array('pageId' => $this->pageId,
                               'language' => $this->language));
      if ($ok === false)
        return $render->pnFormRegisterError(null);
    }

    if ($url == null)
      $url = $this->backref;
    if (empty($url))
      $url = ModUtil::url('Content', 'edit', 'editpage', array('pid' => $this->pageId));

    ModUtil::apiFunc('PageLock', 'user', 'releaseLock',
                 array('lockName' => "contentTranslatePage{$this->pageId}"));

    return $render->pnFormRedirect($url);
  }
}

