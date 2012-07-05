<?php
/**
 * Content heading plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_ContentType_Heading extends Content_AbstractContentType
{
    protected $text;
    protected $headerSize;
    protected $anchorName;

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getHeaderSize()
    {
        return $this->headerSize;
    }

    public function setHeaderSize($headerSize)
    {
        $this->headerSize = $headerSize;
    }

    public function getAnchorName()
    {
        return $this->anchorName;
    }

    public function setAnchorName($anchorName)
    {
        $this->anchorName = $anchorName;
    }

    function getTitle()
    {
        return $this->__('Heading');
    }
    function getDescription()
    {
        return $this->__('Section heading for structuring larger amounts of text.');
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        $this->text = $data['text'];
        $this->headerSize = isset($data['headerSize']) ? $data['headerSize'] : 'h3';
        $this->anchorName = $data['anchorName'];
    }
    function display()
    {
        $this->view->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $this->view->assign('headerSize', DataUtil::formatForDisplayHTML($this->headerSize));
        $this->view->assign('anchorName', DataUtil::formatForDisplayHTML($this->anchorName));
        $this->view->assign('contentId', $this->contentId);
        return $this->view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        // just show the header itself during page editing
        $this->view->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $this->view->assign('headerSize', DataUtil::formatForDisplayHTML($this->headerSize));
        $this->view->assign('anchorName', DataUtil::formatForDisplayHTML($this->anchorName));
        $this->view->assign('contentId', $this->contentId);
        return $this->view->fetch($this->getTemplate());
    }
    function getDefaultData()
    {
        return array('text' => $this->__('Heading'), 'headerSize' => 'h3', 'anchorName' => '');
    }
    function startEditing()
    {
        $scripts = array('javascript/ajax/prototype.js', 'javascript/helpers/Zikula.js');
        PageUtil::addVar('javascript', $scripts);
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }
}