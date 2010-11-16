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
    $dom = ZLanguage::getModuleDomain('content');
    $areatitle = DataUtil::formatForDisplay($params['page']['layoutData']['plugin']->getContentAreaTitle($params['contentArea']));
    if ($areatitle) {
        $html = "<div class='con_area'>" . __f('%s area', $areatitle, $dom) . "</div>";
    }
    if (array_key_exists('assign', $params)) {
        $smarty->assign($params['assign'], $html);
    } else {
        return $html;
    }
}
