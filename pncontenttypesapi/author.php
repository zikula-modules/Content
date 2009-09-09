<?php
/**
 * Content author plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_authorPlugin extends contentTypeBase
{
  var $uid;

  function getModule() { return 'content'; }
  function getName() { return 'author'; }
  function getTitle() { return _CONTENT_CONTENTENTTYPE_AUTHORTITLE; }
  function getDescription() { return _CONTENT_CONTENTENTTYPE_AUTHORDESCR; }
  function isTranslatable() { return false; }

  
  function loadData($data)
  {
    $this->uid = $data['uid'];
  }

  
  function display()
  {
    $render = pnRender::getInstance('content', false);
    $render->assign('uid', DataUtil::formatForDisplayHTML($this->uid));
    $render->assign('contentId', $this->contentId);
    return $render->fetch('contenttype/author_view.html');
  }

  
  function displayEditing()
  {
    return "<h3>" . pnUserGetVar('uname', $this->uid) . "</h3>";
  }

  
  function getDefaultData()
  { 
    return array('uid' => '1');
  }

  function getSearchableText()
  {
    return html_entity_decode(strip_tags(pnUserGetVar($this->uid, 'uname')));
  }
}


function content_contenttypesapi_author($args)
{
  return new content_contenttypesapi_authorPlugin();
}

