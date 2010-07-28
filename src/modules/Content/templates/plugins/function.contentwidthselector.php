<?php


function smarty_function_contentwidthselector($params, &$render)
{
    return $render->registerPlugin('Content_Form_Plugin_WidthSelector', $params);
}
