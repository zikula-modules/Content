<?php

class Content_Form_Handler_Admin_Settings extends pnFormHandler
{
    function initialize(&$render)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN))
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());

        // Assign all module vars
        $render->assign('config', ModUtil::getVar('Content'));

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        if ($args['commandName'] == 'save') {
            if (!$render->pnFormIsValid())
                return false;

            $data = $render->pnFormGetValues();

            if (!ModUtil::setVars('Content', $data['config']))
                return $render->pnFormSetErrorMsg('Failed to set configuration variables');

            LogUtil::registerStatus(__('Done! Saved module configuration.', $dom));
        } else if ($args['commandName'] == 'cancel') {
        }

        $url = ModUtil::url('Content', 'admin', 'main');

        return $render->pnFormRedirect($url);
    }
}

