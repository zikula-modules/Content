<?php

/**
 * Content BreadCrumb Plugin
 *
 * @copyright (C) 2010 - 2011, Sven Strickroth <email@cs-ware.de>
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */
class Content_ContentType_Breadcrumb extends Content_AbstractContentType
{
    protected $includeSelf;
    protected $translateTitles;

    public function getIncludeSelf()
    {
        return $this->includeSelf;
    }

    public function setIncludeSelf($includeSelf)
    {
        $this->includeSelf = $includeSelf;
    }

    public function getTranslateTitles()
    {
        return $this->translateTitles;
    }

    public function setTranslateTitles($translateTitles)
    {
        $this->translateTitles = $translateTitles;
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

    function loadData(&$data)
    {
        if (isset($data['includeSelf'])) {
            $this->includeSelf = (bool) $data['includeSelf'];
        } else {
            $this->includeSelf = true;
        }
        if (isset($data['translateTitles'])) {
            $this->translateTitles = (bool) $data['translateTitles'];
        } else {
            $this->translateTitles = true;
        }
    }

    function display()
    {
        $path = array();
        $pageid = $this->getPageId();
        while ($pageid > 0) {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array(
                        'id' => $pageid,
                        'includeContent' => false,
                        'translate' => $this->translateTitles));
            if (!isset($this->includeSelf) || $this->includeSelf || $pageid != $this->getPageId()) {
                array_unshift($path, $page);
            }
            $pageid = $page['parentPageId'];
        }

        $this->view->assign('thispage', $this->getPageId());
        $this->view->assign('path', $path);

        return $this->view->fetch($this->getTemplate());
    }

    function displayEditing()
    {
        return '';
    }

    function getDefaultData()
    {
        return array('includeSelf' => true, 'translateTitles' => true);
    }

}