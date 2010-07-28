<?php
/**
 * Content Blocks plugin
 *
 * @copyright (C) 2008, Markus Gr��ing
 */

class content_contenttypesapi_blockPlugin extends contentTypeBase
{
    var $text;
    var $blockid;
    var $inputType;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'block';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Blocks', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Display Zikula blocks.', $dom);
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

        $render = & Zikula_View::getInstance('Content', false);
        $render->assign('content', $text);
        return $render->fetch('contenttype/block_view.html');

    }

    function displayEditing()
    {
        $output = "Block-Id=$this->blockid";
        return $output;
    }

    function getDefaultData($data)
    {
        return array('blockid' => "0");
    }
}

function content_contenttypesapi_block($args)
{
    return new content_contenttypesapi_blockPlugin($args['data']);
}