<?php
/**
 * Content html plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class content_contenttypesapi_htmlPlugin extends contentTypeBase
{
    var $text;
    var $inputType;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'html';
    }
    function getTitle()
    {
        return $this->__('HTML text');
    }
    function getDescription()
    {
        return $this->__('A rich HTML editor for adding text to your page.');
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        if (!isset($data['inputType'])) {
            $data['inputType'] = 'html';
		}
        if (!ModUtil::available('scribite') && $data['inputType'] == 'html') {
            $data['inputType'] = 'text';
		}
        $this->text = $data['text'];
        $this->inputType = $data['inputType'];
    }
    function display()
    {
        $text = DataUtil::formatForDisplayHTML($this->text);
        $text = ModUtil::callHooks('item', 'transform', '', array($text));
        $text = $text[0];
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('inputType', $this->inputType);
        $view->assign('text', $text);

        return $view->fetch('contenttype/paragraph_view.html');
    }
    function displayEditing()
    {
        return $this->display();
    }
    function getDefaultData()
    {
        return array('text' => $this->__('Add text here ...'), 'inputType' => (ModUtil::available('Scribite') ? 'html' : 'text'));
    }
    function startEditing(&$view)
    {
        $scripts = array('javascript/ajax/prototype.js', 'javascript/helpers/Zikula.js');
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
          'if ($treffer[2]) { return "<a href=\"".ModUtil::url("Content", "user", "view", array("pid" => $treffer[1]))."\">".$treffer[2]."</a>"; } else {
          $page = ModUtil::apiFunc("Content", "page", "getPage", array("pid" => $treffer[1]));
          if ($page === false) return "";
          return "<a href=\"".ModUtil::url("Content", "user", "view", array("pid" => $treffer[1]))."\">".$page["title"]."</a>";
          }'
        ) , $text);
        if (ModUtil::available('crptag')) {
            $text = preg_replace_callback("/\[\[tag-([0-9]+)(?:\|(.+?))?\]\]/", create_function(
              '$treffer',
              '$title = $treffer[1];
              if ($treffer[2]) { $title = $treffer[2]; }
              return "<a href=\"".ModUtil::url("crpTag", "user", "display", array("id" => $treffer[1]))."\">".$title."</a>";
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