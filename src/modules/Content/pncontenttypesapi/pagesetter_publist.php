<?php

/**
 * Content pagesetter list plugin
 *
 * @copyright (C) 2008, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_pagesetter_publistPlugin extends contentTypeBase
{
    var $tid;
    var $numpubs;
    var $offset;
    var $filter;
    var $order;
    var $tpl;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'pagesetter_publist';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Pagesetter publication list', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Pagesetter list of filtered, ordered, and/or formatted publications.', $dom);
    }
    function isTranslatable()
    {
        return false;
    }
    function loadData(&$data)
    {
        $this->tid = $data['tid'];
        $this->numpubs = $data['numpubs'];
        $this->offset = $data['offset'];
        $this->filter = $data['filter'];
        $this->order = $data['order'];
        $this->tpl = $data['tpl'];
    }
    function display()
    {
        // retrieve filtered and ordered publication list
        $plargs = array('tid' => $this->tid, 'noOfItems' => $this->numpubs, 'offsetItems' => $this->offset, 'language' => ZLanguage::getLanguageCode(), 'orderByStr' => $this->order);

        $filters = preg_split("/\s*&\s*/", $this->filter);
        if (is_array($filters) && strlen(trim($filters[0]))) {
            $plargs['filterSet'] = $filters;
        }

        $publist = ModUtil::apiFunc('pagesetter', 'user', 'getPubList', $plargs);

        // retrieve formatted publications
        $publications = array();
        if ($publist !== false) {
            foreach ($publist['publications'] as $pub) {
                $pub = ModUtil::apiFunc('pagesetter', 'user', 'getPubFormatted', array('tid' => $this->tid, 'pid' => $pub['pid'], 'format' => $this->tpl, 'updateHitCount' => false));
                if ($pub !== false)
                    $publications[] = $pub;
            }
        }

        // render instance - assign publications
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('publications', $publications);

        return $view->fetch('contenttype/pagesetter_publist_view.html');
    }
    function displayEditing()
    {
        $tid = DataUtil::formatForDisplayHTML($this->tid);
        $numpubs = DataUtil::formatForDisplayHTML($this->numpubs);
        $offset = DataUtil::formatForDisplayHTML($this->offset);
        $filter = DataUtil::formatForDisplayHTML($this->filter);
        $order = DataUtil::formatForDisplayHTML($this->order);
        $tpl = DataUtil::formatForDisplayHTML($this->tpl);
    }
    function getDefaultData()
    {
        // deault values
        return array('tid' => ModUtil::getVar('pagesetter', 'frontpagePubType'), 'numpubs' => 5, 'offset' => 0, 'filter' => '', 'order' => '', 'tpl' => 'inlineList');
    }
}

function content_contenttypesapi_pagesetter_publist($args)
{
    return new content_contenttypesapi_pagesetter_publistPlugin($args['data']);
}

?>
