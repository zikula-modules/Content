<?php


function smarty_function_contentwidthselector($params, &$render)
{
    return $render->pnFormRegisterPlugin('Content_Form_Plugin_WidthSelector', $params);
}
