<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: pnuser.php 406 2010-06-01 03:04:45Z drak $
 * @license See license.txt
 */


class Content_Controller_User extends Zikula_Controller
{
    /**
     * Show sitemap
     *
     * @return Renderer
     */
    public function main($args)
    {
        return $this->sitemap($args);
    }

    /**
     * View list of categories
     *
     * @return Renderer
     */
    public function categories($args)
    {
        if (!contentHasPageViewAccess())
            return LogUtil::registerPermissionError();

        $view = Zikula_View::getInstance('Content');

        $mainCategoryId = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'page', 'primary', 30); // 30 == /__SYSTEM__/Modules/Global
        $categories = CategoryUtil::getCategoriesByParentID($mainCategoryId);
        $rootCategory = CategoryUtil::getCategoryByID($mainCategoryId);

        $view->assign('rootCategory', $rootCategory);
        $view->assign('categories', $categories);
        $view->assign('lang', ZLanguage::getLanguageCode());
        //$view->assign(ModUtil::getVar('Pages'));
        $view->assign('shorturls', System::getVar('shorturls'));
        $view->assign('shorturlstype', System::getVar('shorturlstype'));

        return $view->fetch('content_user_main.htm');
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
        $dom = ZLanguage::getModuleDomain('Content');

        $pageId = isset($args['pid']) ? $args['pid'] : FormUtil::getPassedValue('pid');
        $versionId = isset($args['vid']) ? $args['vid'] : FormUtil::getPassedValue('vid');
        $urlname = isset($args['name']) ? $args['name'] : FormUtil::getPassedValue('name');
        $preview = isset($args['preview']) ? $args['preview'] : FormUtil::getPassedValue('preview');
        $editmode = isset($args['editmode']) ? $args['editmode'] : FormUtil::getPassedValue('editmode');

        if ($editmode !== null) {
            SessionUtil::setVar('ContentEditMode', $editmode);
        } else {
            $editmode = SessionUtil::getVar('ContentEditMode', null);
        }

        $versionHtml = '';
        $hasEditAccess = contentHasPageEditAccess($pageId);

        if ($versionId !== null && $hasEditAccess) {
            $preview = true;
            $version = ModUtil::apiFunc('Content', 'history', 'getPageVersion', array('id' => $versionId, 'preview' => $preview, 'includeContent' => true));
            $versionData = & $version['data'];
            $page = & $versionData['page'];
            $pageId = $page['id'];

            //var_dump($version);
            $translatable = array('revisionNo' => $version['revisionNo'], 'date' => $version['date'], 'action' => constant($version['action']), 'userName' => $version['userName'], 'ipno' => $version['ipno']);
            $iconSrc = 'images/icons/extrasmall/clock.gif';
            $versionHtml = "<p class=\"content-versionpreview\"><img alt=\"\" src=\"$iconSrc\"/> " . __f('Version #%1$s - %2$s - %3$s by %4$s from %5$s', $translatable, $dom) . "</p>";
        } else if ($pageId === null && !empty($urlname)) {
            $pageId = ModUtil::apiFunc('Content', 'Page', 'solveURLPath', compact('urlname'));
            System::queryStringSetVar('pid', $pageId);
        }

        if (!contentHasPageViewAccess($pageId))
            return LogUtil::registerPermissionError();

        if ($pageId !== null && $versionId === null) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $pageId, 'preview' => $preview, 'includeContent' => true));
        } else if ($versionId === null)
            return LogUtil::registerArgsError();

        if ($page === false)
            return false;

        $multilingual = ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual');
        if ($page['language'] == ZLanguage::getLanguageCode())
            $multilingual = false;

        $pageTitle = html_entity_decode($page['title']);
        PageUtil::setVar('title', ($preview ? __("Preview", $dom) . ' - ' . $pageTitle : $pageTitle));

        //$layoutTemplate = 'layout/' . $page['layoutData']['name'] . '.html';
        $view = Zikula_View::getInstance('Content');
        $view->assign('page', $page);
        $view->assign('preview', $preview);
        $view->assign('editmode', $editmode);
        $view->assign('multilingual', $multilingual);
        $view->assign('enableVersioning', ModUtil::getVar('Content', 'enableVersioning'));

        contentAddAccess($view, $pageId);

        return $versionHtml . $view->fetch('content_user_page.html');
    }

    /**
     * View simple list of pages
     *
     * @return Renderer
     */
    public function listpages($args)
    {
        return $this->contentCommonList($args, 'content_user_list.html', false);
    }

    /**
     * View extended list of pages (showing page headers only)
     *
     * @return Renderer
     */
    public function extlist($args)
    {
        return $this->contentCommonList($args, 'content_user_extlist.html', true);
    }

    /**
     * View complete list of pages (showing complete pages)
     *
     * @return Renderer
     */
    public function pagelist($args)
    {
        return $this->contentCommonList($args, 'content_user_pagelist.html', true);
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
        if (!contentHasPageViewAccess())
            return LogUtil::registerPermissionError();

        $category = isset($args['cat']) ? $args['cat'] : (string) FormUtil::getPassedValue('cat');
        $pageIndex = isset($args['page']) ? $args['page'] : (int) FormUtil::getPassedValue('page');
        $orderBy = isset($args['orderby']) ? $args['orderby'] : (string) FormUtil::getPassedValue('orderby');
        $orderDir = isset($args['orderdir']) ? $args['orderdir'] : (string) FormUtil::getPassedValue('orderdir');
        $pageSize = isset($args['pagesize']) ? $args['pagesize'] : (string) FormUtil::getPassedValue('pagesize');

        if ($pageIndex < 1)
            $pageIndex = 1;
        --$pageIndex; // API is zero-based


        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', array('filter' => array('category' => $category), 'pageIndex' => $pageIndex, 'pageSize' => $pageSize, 'orderBy' => $orderBy, 'orderDir' => $orderDir, 'includeContent' => $includeContent));
        if ($pages === false)
            return false;

        $pageCount = ModUtil::apiFunc('Content', 'Page', 'getPageCount', array('category' => $category));
        if ($pageCount === false)
            return false;

        $view = Zikula_View::getInstance('Content');
        $view->assign('pages', $pages);
        $view->assign('pageIndex', $pageIndex);
        $view->assign('pageSize', $pageSize);
        $view->assign('pageCount', $pageCount);
        $view->assign('preview', false);
        contentAddAccess($view, null);
        return $view->fetch($template);
    }

    /**
     * List subpages
     *
     * @author Philipp Niethammer <webmaster@nochwer.de>
     *
     * @param int       pid     Page ID
     * @param string    name    URL name, alternative for pid
     * @return Renderer
     */
    public function subpages($args)
    {
        $pageId = isset($args['pid']) ? $args['pid'] : FormUtil::getPassedValue('pid');
        $urlname = isset($args['name']) ? $args['name'] : FormUtil::getPassedValue('name');

        if ($pageId === null && !empty($urlname)) {
            $pageId = ModUtil::apiFunc('Content', 'Page', 'solveURLPath', compact('urlname'));
        }

        if ($pageId === null)
            return LogUtil::registerError(__('Error! Unknown page.', $dom), 404);

        if (!contentHasPageViewAccess($pageId))
            return LogUtil::registerPermissionError();

        $topPage = ModUtil::apiFunc('Content', 'Page', 'getPages', array('filter' => array('superParentId' => $pageId), 'orderBy' => 'setLeft', 'makeTree' => true));
        if ($topPage === false)
            return false;

        $view = Zikula_View::getInstance('Content');
        $view->assign(reset($topPage));
        return $view->fetch('content_user_subpages.html');
    }

    /**
     * View sitemap
     *
     * @return Renderer
     */
    public function sitemap($args)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        if (!contentHasPageViewAccess())
            return LogUtil::registerPermissionError();

        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', array('orderBy' => 'setLeft', 'makeTree' => true));
        if ($pages === false)
            return false;

        PageUtil::setVar('title', __('Sitemap', $dom));

        $view = Zikula_View::getInstance('Content');
        $view->assign('pages', $pages);

        $tpl = FormUtil::getPassedValue('tpl', '', 'GET');
        if ($tpl == 'xml') {
            $view->display('content_user_sitemap.xml');
            return true;
        }

        return $view->fetch('content_user_sitemap.html');
    }
}