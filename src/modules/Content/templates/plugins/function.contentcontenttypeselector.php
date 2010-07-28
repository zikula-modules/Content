<?php

function smarty_function_contentcontenttypeselector($params, &$render)
{
    return $render->registerPlugin('Content_Form_Plugin_TypeSelector', $params);
}
