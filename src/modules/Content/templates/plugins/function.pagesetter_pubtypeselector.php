<?php

/**
 * Pagesetter pnforms plugin for selecting pagesetter publication type
 *
 * @copyright (C) 2008, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


/**
 * Standard Smarty function for this plugin
 */
function smarty_function_pagesetter_pubtypeselector($params, &$render)
{
    // Let the pnFormPlugin class do all the hard work
    return $render->registerPlugin('Content_Form_Plugin_PagesetterPubTypeSelector', $params);
}
