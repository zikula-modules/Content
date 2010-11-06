<?php
/**
 * Content computer code plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class content_contenttypesapi_computerCodePlugin extends contentTypeBase
{
    var $text;

    function getModule()
    {
        return 'Content';
    }
    function getName()
    {
        return 'computercode';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Computer Code', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('A text editor for computer code. Line numbers are added to the text and it is displayed in a monospaced font.', $dom);
    }
    function loadData(&$data)
    {
        $this->text = $data['text'];
    }
    function display()
    {
        if (ModUtil::isHooked('bbcode', 'Content')) {
            $code = '[code]' . $this->text . '[/code]';
            $code = ModUtil::apiFunc('bbcode', 'user', 'transform', array('extrainfo' => array($code), 'objectid' => 999));
            $this->$code = $code[0];
            return $this->$code;
        } else {
            return $this->transformCode($this->text, true);
        }
    }
    function displayEditing()
    {
        // <pre> does not work in IE 7 with the portal javascript
        return $this->transformCode($this->text, false); 
    }
    function getDefaultData()
    {
        return array('text' => '');
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->text));
    }
    function transformCode($code, $usePre)
    {
        $lines = explode("\n", $code);
        $html = "<div class=\"content-computercode\"><ol class=\"codelisting\">\n";

        for ($i = 1, $cou = count($lines); $i <= $cou; ++$i) {
            if ($usePre) {
                $line = empty($lines[$i - 1]) ? ' ' : htmlspecialchars($lines[$i - 1]);
                $line = '<div><pre>' . $line . '</pre></div>';
            } else {
                $line = empty($lines[$i - 1]) ? '&nbsp;' : htmlspecialchars($lines[$i - 1]);
                $line = str_replace(' ', '&nbsp;', $line);
                $line = '<div>' . $line . '</div>';
            }
            $html .= "<li>$line</li>\n";
        }

        $html .= "</ol></div>\n";

        return $html;
    }
}

function content_contenttypesapi_computerCode($args)
{
    return new content_contenttypesapi_computerCodePlugin($args['data']);
}

