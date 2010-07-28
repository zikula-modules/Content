<?php


function smarty_function_contentclassselector($params, &$render)
{
    return $render->pnFormRegisterPlugin('Content_Form_Plugin_ClassSelector', $params);
}
