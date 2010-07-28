<?php

function smarty_function_contentpositionselector($params, &$render)
{
    return $render->registerPlugin('Content_Form_Plugin_PositionSelector', $params);
}
