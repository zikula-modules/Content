<?php

function smarty_function_contentlayoutselector($params, &$render)
{
    return $render->pnFormRegisterPlugin('Content_Form_Plugin_LayoutSelector', $params);
}
