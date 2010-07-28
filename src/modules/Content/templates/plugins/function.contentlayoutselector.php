<?php

function smarty_function_contentlayoutselector($params, &$render)
{
    return $render->registerPlugin('Content_Form_Plugin_LayoutSelector', $params);
}
