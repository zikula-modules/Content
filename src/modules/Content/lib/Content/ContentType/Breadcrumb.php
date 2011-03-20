<?php

/**
 * Content BreadCrumb Plugin
 *
 * @copyright (C) 2010, Sven Strickroth, TU Clausthal
 * @link http://code.zikula.org/content
 * @license See license.txt
 */
class Content_ContentType_Breadcrumb extends Content_AbstractContentType
{

    protected $pageid;

    function __construct(Zikula_View $view, array $data = array()) {
        parent::__construct($view);
        $this->pageid = isset($data['pageId']) ? $data['pageId'] : null;
    }

    public function getPageid()
    {
        return $this->pageid;
    }

    public function setPageid($pageid)
    {
        $this->pageid = $pageid;
    }

    function getTitle()
    {
        return $this->__('BreadCrumb');
    }

    function getDescription()
    {
        return $this->__('Show breadcrumbs for hierarchical pages');
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
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array(
                        'id' => $pageid,
                        'includeContent' => false,
                        'translate' => false));
            array_unshift($path, $page);
            $pageid = $page['parentPageId'];
        }

        $this->view->assign('thispage', $this->pageid);
        $this->view->assign('path', $path);

        return $this->view->fetch($this->getTemplate());
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