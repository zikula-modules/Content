<?php
/**
 * Content author plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_Author extends Content_ContentType
{
    var $uid;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'Author';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Author Infobox', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Various information about the author of the page.', $dom);
    }
    function isTranslatable()
    {
        return false;
    }
    function loadData(&$data)
    {
        $this->uid = $data['uid'];
    }
    function display()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('uid', DataUtil::formatForDisplayHTML($this->uid));
        $view->assign('contentId', $this->contentId);
        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        return "<h3>" . UserUtil::getVar('uname', $this->uid) . "</h3>";
    }
    function getDefaultData()
    {
        return array('uid' => '1');
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags(UserUtil::getVar($this->uid, 'uname')));
    }
}