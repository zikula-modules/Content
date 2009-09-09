<?php
/**
 * Content heading plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


class content_contenttypesapi_headingPlugin extends contentTypeBase
{
  var $text;

  function getModule() { return 'content'; }
  function getName() { return 'heading'; }
  function getTitle() { return _CONTENT_CONTENTENTTYPE_HEADINGTITLE; }
  function getDescription() { return _CONTENT_CONTENTENTTYPE_HEADINGDESCR; }
  function isTranslatable() { return true; }

  
  function loadData($data)
  {
    $this->text = $data['text'];
  }

  
  function display()
  {
   $render = pnRender::getInstance('content', false);
   $render->assign('text', DataUtil::formatForDisplayHTML($this->text));
	$render->assign('contentId', $this->contentId);
   return $render->fetch('contenttype/heading_view.html');
  }

  
  function displayEditing()
  {
    return "<h3>" . DataUtil::formatForDisplayHTML($this->text) . "</h3>";
  }

  
  function getDefaultData()
  { 
    return array('text' => _CONTENT_CONTENTENTTYPE_HEADINGTEXTDEFAULT);
  }

  
  function startEditing(&$render)
  {
    $scripts = array('javascript/ajax/prototype.js', 'javascript/ajax/pnajax.js');
    PageUtil::addVar('javascript', $scripts);
  }


  function getSearchableText()
  {
    return html_entity_decode(strip_tags($this->text));
  }
}


function content_contenttypesapi_heading($args)
{
  return new content_contenttypesapi_headingPlugin();
}

