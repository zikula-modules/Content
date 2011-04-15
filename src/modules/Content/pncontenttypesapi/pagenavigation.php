<?php
/**
 * Content prev- & next-page plugin
 *
 * @copyright (C) 2010 Sven Strickroth
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_pagenavigationPlugin extends contentTypeBase
{
    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'pagenavigation';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Page navigation', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __("Allows to navigate within pages on the same level.", $dom);
    }

    function isTranslatable()
    {
        return false;
    }

    function display()
    {
        $prevpage = null;
        $nextpage = null;

        $page = pnModAPIFunc('content', 'page', 'getPage', array('id' => $this->pageId));

        $pntable = pnDBGetTables();
        $pageTable = $pntable['content_page'];
        $pageColumn = $pntable['content_page_column'];

        $options = array('makeTree' => true);
        $options['orderBy'] = 'position';
        $options['orderDir'] = 'desc';
        $options['pageSize'] = 1;
        $options['filter']['superParentId'] = $page['parentPageId'];

        if ($page[position] > 0) {
            $options['filter']['where'] = "$pageColumn[level] = $page[level] and $pageColumn[position] < $page[position]";

            $pages = pnModAPIFunc('content', 'page', 'getPages', $options);
            if (count($pages) > 0) {
                $prevpage = $pages[0];
            }
        }

        if ($page[position]) {
            $options['orderDir'] = 'asc';
            $options['filter']['where'] = "$pageColumn[level] = $page[level] and $pageColumn[position] > $page[position]";
            $pages = pnModAPIFunc('content', 'page', 'getPages', $options);
            if (count($pages) > 0) {
                $nextpage = $pages[0];
            }
        }

        $render = & pnRender::getInstance('content', false);
        $render->assign('loggedin', pnUserLoggedIn());
        $render->assign('prevpage', $prevpage);
        $render->assign('nextpage', $nextpage);
        return $render->fetch('contenttype/pagenavigation_view.html');
    }

    function displayEditing()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return "<h3>" . __('Page navigation', $dom)."</h3>";
    }

    function getSearchableText()
    {
        return;
    }
}

function content_contenttypesapi_pagenavigation($args)
{
    return new content_contenttypesapi_pagenavigationPlugin();
}
