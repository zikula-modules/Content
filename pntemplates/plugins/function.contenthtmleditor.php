<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */

function smarty_function_contenthtmleditor($params, &$render) 
{
  $dom = ZLanguage::getModuleDomain('content');
  $inputId = $params['inputId'];
  $inputType = isset($params['inputType']) ? $params['inputType'] : null;

  // Get reference to optional radio button that enables the editor (a hack for the HTML plugin).
  // It would have been easier just to read a $var in the template, but this won't work with a
  // pnForms plugin - you would just get the initial value from when the page was loaded
  $htmlRadioButton = (isset($params['htmlRadioId']) ? $render->pnFormGetPluginById($params['htmlRadioId']) : null);
  $textRadioButton = (isset($params['textRadioId']) ? $render->pnFormGetPluginById($params['textRadioId']) : null);

  $html = '';

  $useWysiwyg =    $htmlRadioButton == null && $inputType == null && !$render->pnFormIsPostBack()
                || $htmlRadioButton == null && $inputType == 'html' && !$render->pnFormIsPostBack()
                || $htmlRadioButton != null && $htmlRadioButton->checked;

  $useBBCode =     $textRadioButton == null && $inputType == 'text' && !$render->pnFormIsPostBack()
                || $textRadioButton != null && $textRadioButton->checked;

  if ($useWysiwyg && pnModAvailable('scribite'))
  {
    $scribite = pnModFunc('scribite','user','loader', array('modulename' => 'content', 'areas' => array($inputId)));
    PageUtil::AddVar('rawtext', $scribite);
  }
  else if ($useWysiwyg && !pnModAvailable('scribite'))
  {
    $html = '(' . __("Please install Scribite to get a real HTML editor.", $dom) . ')';
  }
  else if ($useBBCode && pnModAvailable('bbcode'))
  {
    $html = pnModFunc('bbcode', 'user', 'bbcodes', array('textfieldid' => $inputId, 'images' => 0));
  }
  else if ($useBBCode && !pnModAvailable('bbcode'))
  {
    $html = '(' . __("Please install the hook BBCode to enable bbcode filtering.", $dom) . ')';
  }

  return $html;
}
