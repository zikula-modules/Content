<?php
/**
 * Content directory plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @copyright (C) 2010-2011, Sven Strickroth <email@cs-ware.de>
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_directoryPlugin extends contentTypeBase
{
    var $pid;
    var $includeSelf;
    var $includeHeading;
    var $includeHeadingLevel;
    var $includeSubpage;
    var $includeSubpageLevel;
    var $includeNotInMenu;

    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'directory';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Table of contents', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __("A table of contents of headings and subpages (build from this module's pages).", $dom);
    }

    function isTranslatable()
    {
        return false;
    }

    function loadData($data)
    {
        $this->pid = $data['pid'];
        $this->includeSelf = ((bool) $data['includeSelf']);
        $this->includeNotInMenu = (bool) $data['includeNotInMenu'];
        $this->includeHeading = $data['includeHeading'];
        $this->includeHeadingLevel = -1;
        $this->includeSubpage = $data['includeSubpage'];
        $this->includeSubpageLevel = 0;
        if (!$this->includeHeading && !$this->includeSubpage) {
            // handle really special case from 3.2.0 which limited output to only one level
            $this->includeSubpageLevel = 1;
        }
        if ($this->includeHeading && $data['includeHeadingLevel'] >= 0) {
            $this->includeHeadingLevel = (int) $data['includeHeadingLevel'];
        }
        if ($this->includeSubpage && $data['includeSubpageLevel'] > 0) {
            $this->includeSubpageLevel = (int) $data['includeSubpageLevel'];
        }
    }

    function display()
    {
        $pntable = pnDBGetTables();
        $pageColumn = $pntable['content_page_column'];

        $options = array('makeTree' => true, 'expandContent' => false);
        $options['orderBy'] = 'setLeft';

        if ($this->pid == 0 && $this->includeSubpage) {
            if ($this->includeSubpage == 2) {
                $options['filter']['where'] = "$pageColumn[level] <= ".($this->includeSubpageLevel-1);
            }
        } else {
            if ($this->includeSubpage) {
                if ($this->includeSubpage == 2 && $this->includeSubpageLevel > 0) {
                    $page = pnModAPIFunc('content', 'page', 'getPage', array('id' => $this->pid));
                    if ($page === false) {
                        return '';
                    }
                    $options['filter']['where'] = "$pageColumn[level] <= ".($page['level'] + $this->includeSubpageLevel);
                }
                $options['filter']['superParentId'] = $this->pid;
            } else {
                // this is a special case, this is also applied if pid==0 and no subpages included, which makes no sense
                $options['filter']['parentId'] = $this->pid;
            }
        }
        if (!$this->includeNotInMenu) {
            $options['filter']['checkInMenu'] = true;
        }
        if ($this->includeHeadingLevel >= 0) {
            $options['includeContent'] = true;
        }
        $pages = pnModAPIFunc('content', 'page', 'getPages', $options);

        if ($this->pid != 0 && !$this->includeSelf) {
            $directory = $this->_genDirectoryRecursive($pages[0], 0);
        } else {
            $directory = array();
            foreach (array_keys($pages) as $page) {
                $directory['directory'][] = $this->_genDirectoryRecursive($pages[$page], ($this->pid == 0 ? 1 : 0));
            }
        }

        $render = & pnRender::getInstance('content', false);
        $render->assign('directory', $directory);
        $render->assign('contentId', $this->contentId);
        return $render->fetch('contenttype/directory_view.html');
    }

    function _genDirectoryRecursive(&$pages, $level)
    {
        $directory = array();
        $pageurl = pnModUrl('content', 'user', 'view', array('pid' => $pages['id']));
        if ($pages['content'] && ($this->includeHeading == 1 || $this->includeHeadingLevel-$level >= 0)) {
            foreach (array_keys($pages['content']) as $area) {
                foreach (array_keys($pages['content'][$area]) as $id) {
                    $plugin = &$pages['content'][$area][$id];
                    if ($plugin['plugin']!= null && $plugin['plugin']->getModule() == 'content' && $plugin['plugin']->getName() == 'heading') {
                        $directory[] = array('title' => $plugin['data']['text'], 'url' => $pageurl . "#heading_" . $plugin['id'], 'level' => $level, 'css' => 'content-directory-heading');
                    }
                }
            }
        }

        if ($pages['subPages']) {
            foreach (array_keys($pages['subPages']) as $id) {
                $directory[] = $this->_genDirectoryRecursive($pages['subPages'][$id], $level+1);
            }
        }

        return array('title' => $pages['title'], 'url' => $pageurl, 'level' => $level, 'css' => '', 'directory' => $directory);
    }

    function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $page = pnModAPIFunc('content', 'page', 'getPage', array('id' => $this->pid, 'includeContent' => false, 'translate' => false));
        return "<h3>" . __f('Table of contents of %1$s', array("title" => htmlspecialchars($page['title'])), $dom) . "</h3>";
    }

    function getDefaultData()
    {
        return array('pid' => $this->pageId, 'includeSelf' => false, 'includeHeading' => 0, 'includeHeadingLevel' => 0, 'includeSubpage' => 1, 'includeSubpageLevel' => 0, 'includeNotInMenu' => false);

    }

    function startEditing(&$render)
    {
        $dom = ZLanguage::getModuleDomain('content');
        $pages = pnModAPIFunc('content', 'page', 'getPages', array('makeTree' => false, 'orderBy' => 'setLeft', 'includeContent' => false, 'filter' => array('checkActive' => false)));

        $pidItems = array();
        $pidItems[] = array('text' => __('All pages', $dom), 'value' => "0");
        foreach ($pages as $page) {
            $pidItems[] = array('text' => str_repeat('+', $page['level']) . " " . $page['title'], 'value' => $page['id']);
        }

        $render->assign('pidItems', $pidItems);
        $render->assign('includeHeadingItems', array(array('text' => __('No'), 'value' => 0), array('text' => __('Yes, unlimited'), 'value' => 1), array('text' => __('Yes, limited'), 'value' => 2)));
        $render->assign('includeSubpageItems', array(array('text' => __('No'), 'value' => 0), array('text' => __('Yes, unlimited'), 'value' => 1), array('text' => __('Yes, limited'), 'value' => 2)));
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
