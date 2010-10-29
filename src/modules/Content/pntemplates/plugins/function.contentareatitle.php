<?php
/**
 * Content
 *
 * @copyright (C) 2010 Sven Strickroth, TU Clausthal
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

function smarty_function_contentareatitle($params, &$render) 
{
    $html = DataUtil::formatForDisplay($params['page']['layoutData']['plugin']->getContentAreaTitle($params['contentArea']));
    if ($html) {
        $html .= "<br />";
    }
    if (array_key_exists('assign', $params)) {
        $smarty->assign($params['assign'], $html);
    } else {
        return $html;
    }
}
