<?php



function smarty_function_contentmoduleselector($params, &$render)
{
    return $render->registerPlugin('Content_Form_Plugin_ModuleSelector', $params);
}
