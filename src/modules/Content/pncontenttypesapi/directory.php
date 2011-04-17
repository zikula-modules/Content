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
    var $includeHeadingLevel;
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
                $page = pnModAPIFunc('content', 'page', 'getPage', array('id' => $this->pid));
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
        $pages = pnModAPIFunc('content', 'page', 'getPages', $options);

        if ($this->pid != 0 && !$this->includeSelf) {
            $directory = $this->_genDirectoryRecursive($pages[0], 0);
        } else {
            $directory = array();
            foreach (array_keys($pages) as $page) {
                $directory['directory'][] = $this->_genDirectoryRecursive($pages[$page], $level);
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
        if ($pages['content'] && $this->includeHeadingLevel-$level >= 0) {
            foreach (array_keys($pages['content']) as $area) {
                foreach (array_keys($pages['content'][$area]) as $id) {
                    $plugin = &$pages['content'][$area][$id];
                    if ($plugin['plugin']!= null && $plugin['plugin']->getModule() == 'content' && $plugin['plugin']->getName() == 'heading') {
                        $directory[] = array('title' => $plugin['data']['text'], 'url' => $pageurl . "#heading_" . $plugin['id']);
                    }
                }
            }
        }

        if ($pages['subPages']) {
            foreach (array_keys($pages['subPages']) as $id) {
                $directory[] = $this->_genDirectoryRecursive($pages['subPages'][$id], $level+1);
            }
        }

        return array('title' => $pages['title'], 'url' => $pageurl, 'directory' => $directory);
    }

    function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $page = pnModAPIFunc('content', 'page', 'getPage', array('id' => $this->pid, 'includeContent' => false, 'translate' => false));
        return "<h3>" . __f('Table of contents of %1$s', array("title" => htmlspecialchars($page['title'])), $dom) . "</h3>";
    }

    function getDefaultData()
    {
        return array('pid' => $this->pageId, 'includeSelf' => false, 'includeHeadingLevel' => 0, 'includeSubpageLevel' => 0, 'includeNotInMenu' => false);

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
