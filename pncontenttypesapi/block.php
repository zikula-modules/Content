<?php
/**
* Content Blocks plugin
*
* @copyright (C) 2008, Markus Größing
*/

class content_contenttypesapi_blockPlugin extends contentTypeBase
{
  var $text;
  var $blockid;
  var $inputType;

  function getModule() { return 'content'; }
  function getName() { return 'block'; }
  function getTitle() { return _CONTENT_CONTENTTYPE_BLOCKTITLE; }
  function getDescription() { return _CONTENT_CONTENTTYPE_BLOCKDESCR; }  
  function isTranslatable() { return false; }

  function loadData(&$data)
  {
    $this->blockid = $data['blockid'];
  }

  function display()
  {

    $id= $this->blockid;
    $blockinfo= pnBlockGetInfo($id);
    $modinfo = pnModGetInfo($blockinfo['mid']);

    $text=pnBlockShow($modinfo['name'], $blockinfo['bkey'], $blockinfo);

    $render = pnRender::getInstance('content', false);
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