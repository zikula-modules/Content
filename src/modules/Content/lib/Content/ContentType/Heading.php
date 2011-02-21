<?php
/**
 * Content heading plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_Heading extends Content_ContentType_Base
{
    var $text;
    var $headerSize;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'heading';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Heading', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Section heading for structuring larger amounts of text.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        if (!isset($data['headerSize'])) {
            $data['headerSize'] = 'h3';
        }
        $this->text = $data['text'];
        $this->headerSize = $data['headerSize'];
    }
    function display()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $view->assign('headerSize', DataUtil::formatForDisplayHTML($this->headerSize));
        $view->assign('contentId', $this->contentId);
        return $view->fetch('contenttype/heading_view.html');
    }
    function displayEditing()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $view->assign('headerSize', DataUtil::formatForDisplayHTML($this->headerSize));
        $view->assign('contentId', $this->contentId);
        return $view->fetch('contenttype/heading_view.html');
    }
    function getDefaultData()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return array('text' => __('Heading', $dom), 'headerSize' => 'h3');
    }
    function startEditing(&$view)
    {
        $scripts = array('javascript/ajax/prototype.js', 'javascript/helpers/Zikula.js');
        PageUtil::addVar('javascript', $scripts);
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }
}