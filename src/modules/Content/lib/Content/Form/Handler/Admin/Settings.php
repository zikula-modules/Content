<?php

class Content_Form_Handler_Admin_Settings extends Zikula_Form_Handler
{
    public function initialize(Zikula_Form_View $view)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }
        $catoptions = array( array('text' => $this->__('Use 2 category levels (1st level single, 2nd level multi selection)'), 'value' => '1'),
                             array('text' => $this->__('Use 2 category levels (both single selection)'), 'value' => '2'),
                             array('text' => $this->__('Use 1 category level'), 'value' => '3'),
                             array('text' => $this->__("Don't use Categories at all"), 'value' => '4') );
        $view->assign('catoptions', $catoptions);
        $view->assign('categoryusage', 1);

        $activeoptions = array( array('text' => $this->__('New pages will be active and available in the menu'), 'value' => '1'),
                                array('text' => $this->__('New pages will be inactive and available in the menu'), 'value' => '2'),
                                array('text' => $this->__('New pages will be active and not available in the menu'), 'value' => '3'),
                                array('text' => $this->__('New pages will be inactive and not available in the menu'), 'value' => '4') );
        $view->assign('activeoptions', $activeoptions);

        // Assign all module vars
        $view->assign('config', ModUtil::getVar('Content'));

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'save') {
            if (!$view->isValid()) {
                return false;
            }

            $data = $view->getValues();

            if (!ModUtil::setVars('Content', $data['config'])) {
                return $view->setErrorMsg($this->__('Failed to set configuration variables'));
            }
            if ($data['config']['categoryUsage'] < 4) {
                // load the category registry util
                $mainCategory = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page', $data['config']['categoryPropPrimary']);
                if (!$mainCategory) {
                    return LogUtil::registerError($this->__('Main category property does not exist.'));
                }
                if ($data['config']['categoryUsage'] < 3) {
                    $secondCategory = CategoryRegistryUtil::getRegisteredModuleCategory('Content', 'content_page', $data['config']['categoryPropSecondary']);
                    if (!$secondCategory) {
                        return LogUtil::registerError($this->__('Second category property does not exist.'));
                    }
                }
            }
            LogUtil::registerStatus($this->__('Done! Saved module configuration.'));
        } else if ($args['commandName'] == 'cancel') {
        }

        $url = ModUtil::url('Content', 'Admin', 'main');

        return $view->redirect($url);
    }
}

