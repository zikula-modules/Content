<?php

class content_contenttypesapi_pagesetter_pubPlugin extends contentTypeBase
{
    var $tid;
    var $pid;
    var $tpl;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'pagesetter_pub';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Pagesetter publication', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display a Pagesetter publication.', $dom);
    }
    function isTranslatable()
    {
        return false;
    }
    function loadData(&$data)
    {
        $this->tid = $data['tid'];
        $this->pid = $data['pid'];
        $this->tpl = $data['tpl'];
    }
    function display()
    {
        $tid = DataUtil::formatForDisplayHTML($this->tid);
        $pid = DataUtil::formatForDisplayHTML($this->pid);
        $tpl = DataUtil::formatForDisplayHTML($this->tpl);

        $url = ModUtil::url('pagesetter', 'user', 'view', array('tid' => $tid, 'pid' => $pid));
        $url = htmlspecialchars($url);

        // get the formatted publication
        $publication = ModUtil::apiFunc('pagesetter', 'user', 'getPubFormatted', array('tid' => $tid, 'pid' => $pid, 'format' => $tpl, 'useTransformHooks' => false,
            'coreExtra' => array('page' => 0, 'baseURL' => $url, 'format' => $tpl)));

        // render instance - assign publication
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('publication', $publication);

        return $view->fetch('contenttype/pagesetter_pub_view.html');
    }
    function displayEditing()
    {
        $tid = DataUtil::formatForDisplayHTML($this->tid);
        $pid = DataUtil::formatForDisplayHTML($this->pid);
        $tpl = DataUtil::formatForDisplayHTML($this->tpl);

        $url = ModUtil::url('pagesetter', 'user', 'view', array('tid' => $tid, 'pid' => $pid));
        $url = htmlspecialchars($url);

        // get the formatted publication
        $publication = ModUtil::apiFunc('pagesetter', 'user', 'getPubFormatted', array('tid' => $tid, 'pid' => $pid, 'format' => $tpl, 'useTransformHooks' => false,
            'coreExtra' => array('page' => 0, 'baseURL' => $url, 'format' => $tpl)));

        // render instance - assign publication
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('publication', $publication);

        return $view->fetch('contenttype/pagesetter_pub_view.html');
    }
    function getDefaultData()
    {
        // deault values
        return array('tid' => ModUtil::getVar('pagesetter', 'frontpagePubType'), 'pid' => '', 'tpl' => 'full');
    }
}

function content_contenttypesapi_pagesetter_pub($args)
{
    return new content_contenttypesapi_pagesetter_pubPlugin($args['data']);
}

?>
