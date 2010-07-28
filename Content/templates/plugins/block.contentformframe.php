<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */


function smarty_block_contentFormFrame($params, $content, &$render)
{
    $result = $render->pnFormRegisterPlugin('pnFormValidationSummary', $params);
    $result .= $render->pnFormRegisterBlock('Content_Form_Plugin_FormFrame', $params, $content);

    return $result;
}
