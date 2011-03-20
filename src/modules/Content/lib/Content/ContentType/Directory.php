<?php
/**
 * Content directory plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_Directory extends Content_AbstractContentType
{
    protected $pid;
    protected $includeHeading;
    protected $includeSubpage;
    protected $includeNotInMenu;

    public function getPid()
    {
        return $this->pid;
    }

    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    public function getIncludeHeading()
    {
        return $this->includeHeading;
    }

    public function setIncludeHeading($includeHeading)
    {
        $this->includeHeading = $includeHeading;
    }

    public function getIncludeSubpage()
    {
        return $this->includeSubpage;
    }

    public function setIncludeSubpage($includeSubpage)
    {
        $this->includeSubpage = $includeSubpage;
    }

    public function getIncludeNotInMenu()
    {
        return $this->includeNotInMenu;
    }

    public function setIncludeNotInMenu($includeNotInMenu)
    {
        $this->includeNotInMenu = $includeNotInMenu;
    }

    function getTitle()
    {
        return $this->__('Table of contents');
    }
    function getDescription()
    {
        return $this->__('A table of contents of headings and subpages (built from the available Content pages).');
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
        $this->includeNotInMenu = (bool) $data['includeNotInMenu'];
    }
    function display()
    {
        $options = array('makeTree' => true, 'expandContent' => false);
        $options['orderBy'] = 'setLeft';

        // if includeHeading and includeSubpage are set to false, show direct child pages
        if (!$this->includeSubpage && $this->pid == 0) {
            $table = DBUtil::getTables();
            $pageColumn = $table['content_page_column'];
            $options['filter']['where'] = "$pageColumn[level] = 0";
        } elseif (!$this->includeSubpage && $this->pid != 0 && $this->includeHeading) {
            $options['filter']['pageId'] = $this->pid;
        } elseif ($this->includeSubpage && $this->pid != 0) {
            $options['filter']['superParentId'] = $this->pid;
        } elseif ($this->pid != 0) {
            $options['filter']['parentId'] = $this->pid;
        }

        if (!$this->includeNotInMenu) {
            $options['filter']['checkInMenu'] = true;
        }

        if ($this->includeHeading) {
            $options['includeContent'] = true;
        }
        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', $options);

        if ($this->pid == 0 || ($this->pid != 0 && !$this->includeSubpage && !$this->includeHeading)) {
            $directory = array();
            foreach (array_keys($pages) as $page) {
                $directory['directory'][] = $this->_genDirectoryRecursive($pages[$page]);
            }
        } else {
            $directory = $this->_genDirectoryRecursive($pages[0]);
        }

        $this->view->assign('directory', $directory);
        $this->view->assign('contentId', $this->contentId);
        return $this->view->fetch($this->getTemplate());
    }
    function _genDirectoryRecursive(&$pages)
    {
        $directory = array();
        $pageurl = ModUtil::url('Content', 'user', 'view', array('pid' => $pages['id']));
        if ($pages['content']) {
            foreach (array_keys($pages['content']) as $area) {
                foreach (array_keys($pages['content'][$area]) as $id) {
                    $plugin = &$pages['content'][$area][$id];
                    if ($plugin['plugin']!= null && $plugin['plugin']->getModule() == 'Content' && $plugin['plugin']->getName() == 'heading') {
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
        if ($this->pid == 0) {
            $title = $this->__('All pages');
        } else {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pid, 'includeContent' => false, 'translate' => false, 'filter' => array('checkActive' => false)));
            $title = $page['title'];
        }
        return "<h3>" . $this->__f('Table of contents of %s', $title) . "</h3>";
    }
    function getDefaultData()
    {
        return array('pid' => $this->pageId, 'includeHeading' => true, 'includeSubpage' => false, 'includeNotInMenu' => false);

    }
    function startEditing()
    {
        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', array('makeTree' => false, 'orderBy' => 'setLeft', 'includeContent' => false, 'filter' => array('checkActive' => false)));
        $pidItems = array();
        $pidItems[] = array('text' => $this->__('All pages'), 'value' => "0");
        foreach ($pages as $page) {
            $pidItems[] = array('text' => str_repeat('+', $page['level']) . " " . $page['title'], 'value' => $page['id']);
        }

        $this->view->assign('pidItems', $pidItems);
    }
    function getSearchableText()
    {
        return;
    }
}