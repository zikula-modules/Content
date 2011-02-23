<?php
/**
 * Content Blocks plugin
 *
 * @copyright (C) 2008, Markus Gr��ing
 */

class Content_ContentType_Block extends Content_ContentType
{
    var $text;
    var $blockid;
    var $inputType;

    function getTitle()
    {
        return $this->__('Blocks');
    }
    function getDescription()
    {
        return $this->__('Display Zikula blocks.');
    }
    function isTranslatable()
    {
        return false;
    }
    function loadData(&$data)
    {
        $this->blockid = $data['blockid'];
    }
    function display()
    {
        $id = $this->blockid;
        $blockinfo = BlockUtil::getBlockInfo($id);
        $modinfo = ModUtil::getInfo($blockinfo['mid']);
        $text = BlockUtil::show($modinfo['name'], $blockinfo['bkey'], $blockinfo);
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('content', $text);
        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        $output = "Block-Id=$this->blockid";
        return $output;
    }
    function getDefaultData()
    {
        return array('blockid' => "0");
    }
}