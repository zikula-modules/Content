<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */


function smarty_block_contentFormFrame($params, $content, &$view)
{
    $result = $view->registerPlugin('Form_Plugin_ValidationSummary', $params);
    $result .= $view->registerBlock('Content_Form_Plugin_FormFrame', $params, $content);

    return $result;
}
