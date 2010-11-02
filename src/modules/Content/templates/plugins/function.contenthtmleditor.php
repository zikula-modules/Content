<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */

function smarty_function_contenthtmleditor($params, &$view) 
{
    $dom = ZLanguage::getModuleDomain('Content');
    $inputId = $params['inputId'];
    $inputType = isset($params['inputType']) ? $params['inputType'] : null;

    // Get reference to optional radio button that enables the editor (a hack for the HTML plugin).
    // It would have been easier just to read a $var in the template, but this won't work with a
    // pnForms plugin - you would just get the initial value from when the page was loaded
    $htmlRadioButton = (isset($params['htmlRadioId']) ? $view->getPluginById($params['htmlRadioId']) : null);
    $textRadioButton = (isset($params['textRadioId']) ? $view->getPluginById($params['textRadioId']) : null);

    $html = '';

    $useWysiwyg = $htmlRadioButton == null && $inputType == null && !$view->isPostBack()
            || $htmlRadioButton == null && $inputType == 'html' && !$view->isPostBack()
            || $htmlRadioButton != null && $htmlRadioButton->checked;

    $useBBCode = $textRadioButton == null && $inputType == 'text' && !$view->isPostBack()
            || $textRadioButton != null && $textRadioButton->checked;

    if ($useWysiwyg && ModUtil::available('scribite'))
    {
        $scribite = ModUtil::func('scribite','user','loader', array('modulename' => 'Content', 'areas' => array($inputId)));
        PageUtil::AddVar('rawtext', $scribite);
    }
    else if ($useWysiwyg && !ModUtil::available('scribite'))
    {
        $html = "<div class=\"z-formrow\"><em class=\"z-sub\">";
        $html .= '(' . __("Please install Scribite to get a real HTML editor.", $dom) . ')';
        $html .= "</em></div>";
    }
    else if ($useBBCode && ModUtil::available('bbcode'))
    {
        $html = "<div class=\"z-formrow\"><em class=\"z-sub\">";
        $html .= ModUtil::func('bbcode', 'user', 'bbcodes', array('textfieldid' => $inputId, 'images' => 0));
        $html .= "</em></div>";
    }
    else if ($useBBCode && !ModUtil::available('bbcode'))
    {
        $html = "<div class=\"z-formrow\"><em class=\"z-sub\">";
        $html .= '(' . __("Please install the hook BBCode to enable bbcode filtering.", $dom) . ')';
        $html .= "</em></div>";
    }

    return $html;
}
