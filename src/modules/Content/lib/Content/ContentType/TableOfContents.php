<?php
/**
 * Content table of contents plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @copyright (C) 2010-2011, Sven Strickroth <email@cs-ware.de>
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_TableOfContents extends Content_AbstractContentType
{
    protected $pid;
    protected $includeSelf;
    protected $includeHeadingLevel;
    protected $includeSubpageLevel;
    protected $includeNotInMenu;

    public function getPid()
    {
        return $this->pid;
    }

    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    public function getIncludeSelf)
    {
        return $this->includeSelf;
    }

    public function setIncludeSelf($includeSelf)
    {
        $this->includeSelf = $includeSelf;
    }

    public function getIncludeHeadingLevel()
    {
        return $this->includeHeadingLevel;
    }

    public function setIncludeHeadingLevel($includeHeadingLevel)
    {
        $this->includeHeadingLevel = $includeHeadingLevel;
    }

    public function getIncludeSubpageLevel()
    {
        return $this->includeSubpageLevel;
    }

    public function setIncludeSubpageLevel($includeSubpageLevel)
    {
        $this->includeSubpageLevel = $includeSubpageLevel;
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
        $this->includeSelf = ((bool) $data['includeSelf']);
        $this->includeNotInMenu = (bool) $data['includeNotInMenu'];
        $this->includeHeadingLevel = -1;
        $this->includeSubpageLevel = 0;
        if ((bool)$data['includeHeading'] && $data['includeHeadingLevel'] >= 0) {
            $this->includeHeadingLevel = (int) $data['includeHeadingLevel'];
        }
        if ($data['includeSubpageLevel'] > 0) {
            $this->includeSubpageLevel = (int) $data['includeSubpageLevel'];
        }
    }
    function display()
    {
        $pntable = pnDBGetTables();
        $pageColumn = $pntable['content_page_column'];

        $options = array('makeTree' => true, 'expandContent' => false);
        $options['orderBy'] = 'setLeft';

        if ($this->pid == 0) {
            $options['filter']['where'] = "$pageColumn[level] <= ".(int) $this->includeSubpageLevel;
        } else {
            if ($this->includeSubpageLevel > 0) {
                $page = ModUtil::apiFunc('content', 'page', 'getPage', array('id' => $this->pid));
                if ($page === false) {
                    return '';
                }
                $options['filter']['where'] = "$pageColumn[level] <= ".($page['level'] + $this->includeSubpageLevel);
                $options['filter']['superParentId'] = $this->pid;
            } else {
                $options['filter']['parentId'] = $this->pid;
            }
        }
        if (!$this->includeNotInMenu) {
            $options['filter']['checkInMenu'] = true;
        }
        if ($this->includeHeadingLevel >= 0) {
            $options['includeContent'] = true;
        }
        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', $options);

        if ($this->pid != 0 && !$this->includeSelf) {
            $toc = $this->_genTocRecursive($pages[0], 0);
        } else {
            $toc = array();
            foreach (array_keys($pages) as $page) {
                $toc['toc'][] = $this->_genTocRecursive($pages[$page], $level);
            }
        }

        $this->view->assign('toc', $toc);
        $this->view->assign('contentId', $this->contentId);
        return $this->view->fetch($this->getTemplate());
    }
    function _genTocRecursive(&$pages)
    {
        $toc = array();
        $pageurl = ModUtil::url('Content', 'user', 'view', array('pid' => $pages['id']));
        if ($pages['content'] && $this->includeHeadingLevel-$level >= 0) {
            foreach (array_keys($pages['content']) as $area) {
                foreach (array_keys($pages['content'][$area]) as $id) {
                    $plugin = &$pages['content'][$area][$id];
                    if ($plugin['plugin']!= null && $plugin['plugin']->getModule() == 'Content' && $plugin['plugin']->getName() == 'heading') {
                        $toc[] = array('title' => $plugin['data']['text'], 'url' => $pageurl . "#heading_" . $plugin['id']);
                    }
                }
            }
        }

        if ($pages['subPages']) {
            foreach (array_keys($pages['subPages']) as $id) {
                $toc[] = $this->_genTocRecursive($pages['subPages'][$id], $level + 1);
            }
        }

        return array('title' => $pages['title'], 'url' => $pageurl, 'toc' => $toc);
    }
    function displayEditing()
    {
        if ($this->pid == 0) {
            $title = $this->__('All pages');
        } else {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pid, 'includeContent' => false, 'translate' => false, 'filter' => array('checkActive' => false)));
            $title = $page['title'];
        }
        return "<h3>" . $this->__f('Table of contents of %s', htmlspecialchars($title)) . "</h3>";
    }
    function getDefaultData()
    {
        return array('pid' => $this->pageId, 'includeSelf' => false, 'includeHeadingLevel' => 0, 'includeSubpageLevel' => 0, 'includeNotInMenu' => false);

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