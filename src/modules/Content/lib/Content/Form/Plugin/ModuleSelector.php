<?php

class Content_Form_Plugin_ModuleSelector extends Form_Plugin_DropdownList
{
    function getFilename()
    {
        return __FILE__;
    }

    function load($view, &$params)
    {
        if (!$view->isPostBack()) {
            $moduleList = ModUtil::apiFunc('Modules', 'admin', 'listmodules', array());
            $modules = array();
            foreach ($moduleList as $module) {
                if ($module['user_capable'] && $module['state'] == PNMODULE_STATE_ACTIVE) {
                    $modules[] = array('text' => $module['displayname'], 'value' => $module['name']);
                }
            }
            $empty = array(array('text' => '', 'value' => ''));
            $modules = array_merge($empty, $modules);
            $this->setItems($modules);
        }
        parent::load($view, $params);
    }
}
