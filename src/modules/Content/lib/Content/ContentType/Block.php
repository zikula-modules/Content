<?php
/**
 * Content Blocks plugin
 *
 * @copyright (C) 2008, Markus Gr��ing
 */

class Content_ContentType_Block extends Content_ContentType
{
    protected $text;
    protected $blockid;
    protected $inputType;

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getBlockid()
    {
        return $this->blockid;
    }

    public function setBlockid($blockid)
    {
        $this->blockid = $blockid;
    }

    public function getInputType()
    {
        return $this->inputType;
    }

    public function setInputType($inputType)
    {
        $this->inputType = $inputType;
    }

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
        $this->view->assign('content', $text);
        return $this->view->fetch($this->getTemplate());
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