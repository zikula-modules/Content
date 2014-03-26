<?php

/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */
class Content_Controller_User extends Zikula_AbstractController
{

    /**
     * Show sitemap
     *
     * @return Renderer
     */
    public function main($args)
    {
        $this->redirect(ModUtil::url('Content', 'user', 'sitemap', $args));
    }

    /**
     * View list of categories
     *
     * @return Renderer
     */
    public function categories($args)
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Content:page:', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        $mainCategoryId = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page', $this->getVar('categoryPropPrimary'), 30); // 30 == /__SYSTEM__/Modules/Global
        $categories = CategoryUtil::getCategoriesByParentID($mainCategoryId);
        $rootCategory = CategoryUtil::getCategoryByID($mainCategoryId);

        $this->view->assign('rootCategory', $rootCategory);
        $this->view->assign('categories', $categories);
        $this->view->assign('lang', ZLanguage::getLanguageCode());
        
        // Count the numer of pages in a specific category
        $pagecount = array();
        foreach ($categories as $category) {
            $pagecount[$category['id']] = ModUtil::apiFunc('Content', 'Page', 'getPageCount', array ('filter' => array('category' => $category['id'])));
        }
        $this->view->assign('pagecount', $pagecount);
        
        return $this->view->fetch('user/main.tpl');
    }

    /**
     * view a page
     *
     * @param int       pid       Page ID
     * @param string    name      URL name, alternative for pid
     * @param bool      preview   Display preview
     * @param bool      editmode  Activate editmode
     * @return Renderer output
     */
    public function view($args)
    {
        $pageId = isset($args['pid']) ? $args['pid'] : FormUtil::getPassedValue('pid');
        $versionId = isset($args['vid']) ? $args['vid'] : FormUtil::getPassedValue('vid');
        $urlname = isset($args['name']) ? $args['name'] : FormUtil::getPassedValue('name');
        $preview = isset($args['preview']) ? $args['preview'] : FormUtil::getPassedValue('preview');
        $editmode = isset($args['editmode']) ? $args['editmode'] : FormUtil::getPassedValue('editmode', null, 'GET');

        if ($pageId === null && !empty($urlname)) {
            $pageId = ModUtil::apiFunc('Content', 'Page', 'solveURLPath', compact('urlname'));
            System::queryStringSetVar('pid', $pageId);
        }

        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        if ($editmode !== null) {
            SessionUtil::setVar('ContentEditMode', $editmode);
        } else {
            $editmode = SessionUtil::getVar('ContentEditMode', null);
        }

        if ($editmode) {
            $this->view->setCaching(false);
        }
        $this->view->setCacheId("$pageId|$versionId");
        if ($this->view->is_cached('user/page.tpl')) {
            return $this->view->fetch('user/page.tpl');
        }

        $versionHtml = '';
        $hasEditAccess = SecurityUtil::checkPermission('Content:page:', $pageId . '::', ACCESS_EDIT);

        if ($versionId !== null && $hasEditAccess) {
            $preview = true;
            $version = ModUtil::apiFunc('Content', 'History', 'getPageVersion', array(
                'id' => $versionId,
                'preview' => $preview,
                'includeContent' => true));
            $versionData = & $version['data'];
            $page = & $versionData['page'];
            $pageId = $page['id'];
            $action = ModUtil::apiFunc('Content', 'History', 'contentHistoryActionTranslate', $version['action']);
            
            $translatable = array(
                'revisionNo' => $version['revisionNo'],
                'date' => $version['date'],
                'action' => $action,
                'userName' => $version['userName'],
                'ipno' => $version['ipno']);
            $iconSrc = 'images/icons/extrasmall/clock.png';
            $versionHtml = "<p class=\"content-versionpreview\"><img alt=\"\" src=\"$iconSrc\"/> " . $this->__f('Version #%1$s - %2$s - %3$s by %4$s from %5$s', $translatable) . "</p>";
        }

        if ($pageId !== null && $versionId === null) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array(
                'id' => $pageId,
                'preview' => $preview,
                'includeContent' => true,
                'filter' => array(
                    'checkActive' => !($preview && $hasEditAccess))));
        } else if ($versionId === null)
            return LogUtil::registerArgsError();

        if ($page === false) {
            return false;
        }
        $multilingual = ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual');
        if ($page['language'] == ZLanguage::getLanguageCode()) {
            $multilingual = false;
        }

        if ($this->getVar('overrideTitle')) {
            $pageTitle = html_entity_decode($page['title']);
            PageUtil::setVar('title', ($preview ? $this->__("Preview") . ' - ' . $pageTitle : $pageTitle));
        }

        $this->view->assign('page', $page);
        $this->view->assign('preview', $preview);
        $this->view->assign('editmode', $editmode);
        $this->view->assign('multilingual', $multilingual);
        $this->view->assign('enableVersioning', $this->getVar('enableVersioning'));

        // add layout type and column count as page variables to the template
		// columncount can be used via plugin contentcolumncount, since it holds regular expressions that slow down
        $this->view->assign('contentLayoutType', $page['layout']);

        // add access parameters
        Content_Util::contentAddAccess($this->view, $pageId);

        // exclude writers from statistics
        if (!$hasEditAccess && !$preview && !$editmode && $this->getVar('countViews')) {
            // Check against session to see if user was already counted
            if (!SessionUtil::getVar("ContentRead" . $pageId)) {
                SessionUtil::setVar("ContentRead" . $pageId, $pageId);
                DBUtil::incrementObjectFieldByID('content_page', 'views', $pageId);
            }
        }

        return $versionHtml . $this->view->fetch('user/page.tpl');
    }

    /**
     * View simple list of pages
     *
     * @return Renderer
     */
    public function listpages($args)
    {
        return $this->contentCommonList($args, 'user/list.tpl', false);
    }

    /**
     * View extended list of pages (showing page headers only)
     *
     * @return Renderer
     */
    public function extlist($args)
    {
        return $this->contentCommonList($args, 'user/extlist.tpl', true);
    }

    /**
     * View complete list of pages (showing complete pages)
     *
     * @return Renderer
     */
    public function pagelist($args)
    {
        return $this->contentCommonList($args, 'user/pagelist.tpl', true);
    }

    /**
     * List pages (optionally in a category) with different templates
     *
     * @param int cat           Category
     * @param int page          Page index
     * @param string orderby    Field to order by
     * @return Renderer output
     */
    protected function contentCommonList($args, $template, $includeContent)
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission('Content:page:', '::', ACCESS_READ), LogUtil::getErrorMsgPermission());

        $category = isset($args['cat']) ? $args['cat'] : (int)
