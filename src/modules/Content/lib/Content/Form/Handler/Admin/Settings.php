<?php

class Content_Form_Handler_Admin_Settings extends Form_Handler
{
    function initialize($view)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }
        // Assign all module vars
        $view->assign('config', ModUtil::getVar('Content'));

        return true;
    }

    function handleCommand($view, &$args)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        if ($args['commandName'] == 'save') {
            if (!$view->isValid()) {
                return false;
            }

            $data = $view->getValues();

            if (!ModUtil::setVars('Content', $data['config'])) {
                return $view->setErrorMsg($this->__('Failed to set configuration variables'));
            }
            LogUtil::registerStatus($this->__('Done! Saved module configuration.'));
        } else if ($args['commandName'] == 'cancel') {
        }

        $url = ModUtil::url('Content', 'admin', 'main');

        return $view->redirect($url);
    }
}

