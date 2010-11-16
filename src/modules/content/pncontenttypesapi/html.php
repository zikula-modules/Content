<?php
/**
 * Content html plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_contenttypesapi_htmlPlugin extends contentTypeBase
{
    var $text;
    var $inputType;

    function getModule()
    {
        return 'content';
    }
    function getName()
    {
        return 'html';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('HTML text', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('A rich HTML editor for adding text to your page.', $dom);
    }
    function isTranslatable()
    {
        return true;
    }

    function loadData(&$data)
    {
        if (!isset($data['inputType']))
            $data['inputType'] = 'html';
        if (!pnModAvailable('scribite') && $data['inputType'] == 'html')
            $data['inputType'] = 'text';
        $this->text = $data['text'];
        $this->inputType = $data['inputType'];
    }

    function display()
    {
        $text = DataUtil::formatForDisplayHTML($this->activateinternallinks($this->text));
        $text = pnModCallHooks('item', 'transform', '', array($text));
        $text = $text[0];
        $render = & pnRender::getInstance('content', false);
        $render->assign('inputType', $this->inputType);
        $render->assign('text', $text);

        return $render->fetch('contenttype/paragraph_view.html');
    }

    function displayEditing()
    {
        return $this->display();
    }

    function getDefaultData()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return array('text' => __('Add text here ...', $dom), 'inputType' => (pnModAvailable('scribite') ? 'html' : 'text'));
    }

    function startEditing(&$render)
    {
        $scripts = array('javascript/ajax/prototype.js', 'javascript/ajax/pnajax.js');
        PageUtil::addVar('javascript', $scripts);
    }

    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->activateinternallinks($this->text)));
    }

    function activateinternallinks($text)
    {
        $text = preg_replace_callback("/\[\[link-([0-9]+)(?:\|(.+?))?\]\]/", create_function(
          '$treffer',
          'if ($treffer[2]) { return "<a href=\"".pnModURL("content", "user", "view", array("pid" => $treffer[1]))."\">".$treffer[2]."</a>"; } else {
          $page = pnModAPIFunc("content", "page", "getPage", array("pid" => $treffer[1]));
          if ($page === false) return "";
          return "<a href=\"".pnModURL("content", "user", "view", array("pid" => $treffer[1]))."\">".$page["title"]."</a>";
          }'
        ) , $text);
        if (pnModAvailable('crptag')) {
            $text = preg_replace_callback("/\[\[tag-([0-9]+)(?:\|(.+?))?\]\]/", create_function(
              '$treffer',
              '$title = $treffer[1];
              if ($treffer[2]) { $title = $treffer[2]; }
              return "<a href=\"".pnModURL("crpTag", "user", "display", array("id" => $treffer[1]))."\">".$title."</a>";
              '
            ) , $text);
        }
        return $text;
    }
}

function content_contenttypesapi_html($args)
{
    return new content_contenttypesapi_htmlPlugin();
}
