<?php
/**
 * Content BreadCrumb Plugin
 *
 * @copyright (C) 2010, Sven Strickroth, TU Clausthal
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license GPLv3
 */

class content_contenttypesapi_breadcrumbPlugin extends contentTypeBase
{
    var $pageid;
    function content_contenttypesapi_breadcrumbPlugin($data)
    {
        $this->pageid = $data['pageId'];
    }
    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'breadcrumb';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('BreadCrumb', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Show breadcrumbs for hierarchical pages', $dom);
    }
    function isTranslatable()
    {
        return false;
    }
    function display()
    {
        $path = array();
        $pageid = $this->pageid;
        while ($pageid > 0) {
            $page = pnModAPIFunc('content', 'page', 'getPage', array('id' => $pageid, 'includeContent' => false, 'translate' => false));
            array_unshift($path, $page);
            $pageid = $page['parentPageId'];
        }

        $render = & pnRender::getInstance('content', false);
        $render->assign('thispage', $this->pageid);
        $render->assign('path', $path);

        return $render->fetch('contenttype/breadcrumb_view.html');
    }
    function displayEditing()
    {
        return '';
    }
    function getDefaultData()
    {
        return array();
    }
}

function content_contenttypesapi_breadcrumb($args)
{
    return new content_contenttypesapi_breadcrumbPlugin($args);
}
