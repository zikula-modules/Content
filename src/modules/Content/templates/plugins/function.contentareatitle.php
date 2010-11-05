<?php
/**
 * Content
 *
 * @copyright (C) 2010 Sven Strickroth, TU Clausthal
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

function smarty_function_contentareatitle($params, &$view) 
{
    $dom = ZLanguage::getModuleDomain('Content');
    $areatitle = DataUtil::formatForDisplay($params['page']['layoutData']['plugin']->getContentAreaTitle($params['contentArea']));
    if ($areatitle) {
        $html = __f('%s area', $areatitle, $dom) . "<br />";
    }
    if (array_key_exists('assign', $params)) {
        $view->assign($params['assign'], $html);
    } else {
        return $html;
    }
}
