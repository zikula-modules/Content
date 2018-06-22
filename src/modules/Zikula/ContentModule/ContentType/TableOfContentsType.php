<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

/**
 * Table of contents content type.
 */
class TableOfContentsType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'book';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Table of contents');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('A table of contents of headings and subpages (built from the available Content pages).');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'pid' => 0/* TODO $this->pageId */,
            'includeSelf' => false,
            'includeNotInMenu' => false,
            'includeHeading' => 0, 
            'includeHeadingLevel' => 0,
            'includeSubpage' => 1,
            'includeSubpageLevel' => 0
        ];
    }

/** TODO    
    public function display()
    {
        $tables = DBUtil::getTables();
        $pageColumn = $tables['content_page_column'];

        $options = array('makeTree' => true, 'expandContent' => false);
        $options['orderBy'] = 'setLeft';

        // get the current active page where this contentitem is in
        $curPage = ModUtil::apiFunc('Content', 'page', 'getPage', array('id' => $this->pageId, 'makeTree' => false, 'includeContent' => false));

        if ($this->pid == 0 && $this->includeSubpage) {
            if ($this->includeSubpage == 2) {
                $options['filter']['where'] = "$pageColumn[level] <= ".($this->includeSubpageLevel-1);
            }
        } else {
            if ($this->includeSubpage) {
                if ($this->includeSubpage == 2 && $this->includeSubpageLevel > 0) {
                    $page = ModUtil::apiFunc('Content', 'page', 'getPage', array('id' => $this->pid));
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
        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', $options);

        if ($this->pid != 0 && !$this->includeSelf) {
            $toc = $this->_genTocRecursive($pages[0], 0);
        } else {
            $toc = array();
            foreach (array_keys($pages) as $page) {
                $toc['toc'][] = $this->_genTocRecursive($pages[$page], ($this->pid == 0 ? 1 : 0));
            }
        }

        $this->view->assign('page', $curPage);
        $this->view->assign('toc', $toc);
        $this->view->assign('contentId', $this->contentId);
        return $this->view->fetch($this->getTemplate());
    }
    
    protected function _genTocRecursive(&$pages, $level)
    {
        $toc = array();
        $pageurl = ModUtil::url('Content', 'user', 'view', array('pid' => $pages['id']));
        if ($pages['content'] && ($this->includeHeading == 1 || $this->includeHeadingLevel - $level >= 0)) {
            foreach (array_keys($pages['content']) as $area) {
                foreach (array_keys($pages['content'][$area]) as $id) {
                    $plugin = &$pages['content'][$area][$id];
                    if ($plugin['plugin'] != null && $plugin['plugin']->getModule() == 'Content' && $plugin['plugin']->getName() == 'Heading') {
                        $toc[] = array('title' => $plugin['plugin']->getText(), 'url' => $pageurl . "#heading_" . $plugin['id'], 'level' => $level, 'css' => 'content-toc-heading');
                    }
                }
            }
        }

        if ($pages['subPages']) {
            foreach (array_keys($pages['subPages']) as $id) {
                $toc[] = $this->_genTocRecursive($pages['subPages'][$id], $level + 1);
            }
        }

        return array('pid' => $pages['id'], 'title' => $pages['title'], 'url' => $pageurl, 'level' => $level, 'css' => '', 'toc' => $toc);
    }
    
    public function displayEditing()
    {
        if ($this->pid == 0) {
            $title = $this->__('All pages');
        } else {
            $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pid, 'includeContent' => false, 'translate' => false, 'filter' => array('checkActive' => false)));
            $title = $page['title'];
        }
        return "<h3>" . $this->__f('Table of contents of %s', htmlspecialchars($title)) . "</h3>";
    }

    public function startEditing()
    {
        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', array('makeTree' => false, 'orderBy' => 'setLeft', 'includeContent' => false, 'filter' => array('checkActive' => false)));
        $pidItems = array();
        $pidItems[] = array('text' => $this->__('All pages'), 'value' => "0");
        foreach ($pages as $page) {
            $pidItems[] = array('text' => str_repeat('-', $page['level']) . " " . $page['title'], 'value' => $page['id']);
        }

        $this->view->assign('pidItems', $pidItems);
        $this->view->assign('includeHeadingItems', array(
            array('text' => __('No'), 'value' => 0), 
            array('text' => __('Yes, unlimited'), 'value' => 1), 
            array('text' => __('Yes, limited'), 'value' => 2))
        );
        $this->view->assign('includeSubpageItems', array(
            array('text' => __('No'), 'value' => 0), 
            array('text' => __('Yes, unlimited'), 'value' => 1), 
            array('text' => __('Yes, limited'), 'value' => 2))
        );
    }
}
*/
    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return ''; // TODO
    }
}
