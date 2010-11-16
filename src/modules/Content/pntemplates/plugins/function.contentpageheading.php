<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */

function smarty_function_contentpageheading($params, &$render) 
{
  $html = '<div class="content-page-heading"><h2>' . DataUtil::formatForDisplay($params['header']) . "</h2>\n";

  if (array_key_exists('subheader', $params))
    if ($params['noescape']) {
        $html .= "<h3>".$params['subheader']."</h3>\n";
    } else {
        $html .= "<h3>".DataUtil::formatForDisplay($params['subheader'])."</h3>\n";
    }
  else
    $html .= "<h3>&nbsp;</h3>\n";

  $html .= "</div>\n";

  if (array_key_exists('assign', $params))
    $smarty->assign($params['assign'], $html);
  else
    return $html;
}
