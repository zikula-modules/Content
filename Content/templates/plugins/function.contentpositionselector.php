<?php

function smarty_function_contentpositionselector($params, &$render)
{
    return $render->pnFormRegisterPlugin('Content_Form_Plugin_PositionSelector', $params);
}
