<?php


function smarty_function_contentclassselector($params, &$render)
{
    return $render->registerPlugin('Content_Form_Plugin_ClassSelector', $params);
}
