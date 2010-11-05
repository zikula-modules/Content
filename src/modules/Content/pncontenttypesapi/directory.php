<?php
/**
 * Content directory plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_directoryPlugin extends contentTypeBase
{
    var $pid;
    var $includeHeading;
    var $includeSubpage;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'directory';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Table of contents', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __("A table of contents of headings and subpages (build from this module's pages).", $dom);
    }
    function isTranslatable()
    {
        return false;
    }
    function loadData(&$data)
    {
        $this->pid = $data['pid'];
        $this->includeHeading = (bool) $data['includeHeading'];
        $this->includeSubpage = (bool) $data['includeSubpage'];
    }
    function display()
    {
        $work = SessionUtil::getVar('directory_yournotthefirst', false);
        if ($work)
            return '';
        SessionUtil::setVar('directory_yournotthefirst', true);
        $options = array('makeTree' => true);
        $options['orderBy'] = 'setLeft';
        if ($this->includeSubpage && $this->pid != 0) {
            $options['filter']['superParentId'] = $this->pid;
        } elseif (!$this->includeSubpage && $this->pid == 0) {
            $pntable = DBUtil::getTables();
            $pageColumn = $pntable['content_page_column'];
            $options['filter']['where'] = "$pageColumn[level] = 0";
        } elseif (!$this->includeSubpage && $this->pid != 0)
            $options['filter']['pageId'] = $this->pid;

        if ($this->includeHeading) {
            $options['includeContent'] = true;
        }
        $pages = ModUtil::apiFunc('Content', 'page', 'getPages', $options);
        if (!$work) {
            SessionUtil::delVar('directory_yournotthefirst');
        }

        if ($this->pid == 0) {
            $directory = array();
            foreach (array_keys($pages) as $page) {
                $directory['directory'][] = $this->_genDirectoryRecursive($pages[$page]);
            }
        } else {
            $directory = $this->_genDirectoryRecursive($pages[0]);
        }

        $view = Zikula_View::getInstance('Content', false);
        $view->assign('directory', $directory);
        $view->assign('contentId', $this->contentId);
        return $view->fetch('contenttype/directory_view.html');
    }
    function _genDirectoryRecursive(&$pages)
    {
        $directory = array();
        $pageurl = ModUtil::url('Content', 'user', 'view', array('pid' => $pages['id']));
        if ($pages['content']) {
            foreach (array_keys($pages['content']) as $area) {
                foreach (array_keys($pages['content'][$area]) as $id) {
                    $plugin = &$pages['content'][$area][$id];
                    if ($plugin['plugin']->getModule() == 'Content' && $plugin['plugin']->getName() == 'heading') {
                        $directory[] = array('title' => $plugin['data']['text'], 'url' => $pageurl . "#heading_" . $plugin['id']);
                    }
                }
            }
        }

        if ($pages['subPages']) {
            foreach (array_keys($pages['subPages']) as $id) {
                $directory[] = $this->_genDirectoryRecursive($pages['subPages'][$id]);
            }
        }

        return array('title' => $pages['title'], 'url' => $pageurl, 'directory' => $directory);
    }
    function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $page = ModUtil::apiFunc('Content', 'page', 'getPage', array('id' => $this->pid, 'includeContent' => false, 'translate' => false));
        return "<h3>" . __f('Table of contents of %1$s', array("title" => $page['title']), $dom) . "</h3>";
    }
    function getDefaultData()
    {
        return array('pid' => $this->pageId, 'includeHeading' => true, 'includeSubpage' => false);

    }
    function startEditing(&$view)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $pages = ModUtil::apiFunc('Content', 'page', 'getPages', array('makeTree' => false, 'orderBy' => 'setLeft', 'includeContent' => false, 'enableEscape' => false));

        $pidItems = array();
        $pidItems[] = array('text' => __('All pages', $dom), 'value' => "0");
        foreach ($pages as $page) {
            $pidItems[] = array('text' => str_repeat('+', $page['level']) . " " . $page['title'], 'value' => $page['id']);
        }

        $view->assign('pidItems', $pidItems);
    }
    function getSearchableText()
    {
        return;
    }
}

function content_contenttypesapi_directory($args)
{
    return new content_contenttypesapi_directoryPlugin();
}