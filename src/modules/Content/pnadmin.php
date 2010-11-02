<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

Loader::requireOnce('modules/content/common.php');
Loader::requireOnce('includes/pnForm.php');

function content_admin_main()
{
    if (!SecurityUtil::checkPermission('content::', '::', ACCESS_EDIT)) {
        return LogUtil::registerPermissionError();
    }

    $dom = ZLanguage::getModuleDomain('content');
    $render = & pnRender::getInstance('content');

    return $render->fetch('content_admin_main.html');
}

class content_admin_settingsHandler extends pnFormHandler
{
    function initialize(&$render)
    {
        if (!SecurityUtil::checkPermission('content::', '::', ACCESS_ADMIN)) {
            return $render->pnFormRegisterError(LogUtil::registerPermissionError());
        }

        $catoptions = array( array('text' => __('Use 2 category levels (1st level single, 2nd level multi selection)', $dom), 'value' => '1'),
                             array('text' => __('Use 2 category levels (both single selection)', $dom), 'value' => '2'),
                             array('text' => __('Use 1 category level', $dom), 'value' => '3'),
                             array('text' => __("Don't use Categories at all", $dom), 'value' => '4') );
                        
        $render->assign('catoptions', $catoptions);
        $render->assign('categoryusage', 1);
        
        // Assign all module vars
        $render->assign('config', pnModGetVar('content'));

        return true;
    }

    function handleCommand(&$render, &$args)
    {
        $dom = ZLanguage::getModuleDomain('content');
        if ($args['commandName'] == 'save') {
            if (!$render->pnFormIsValid()) {
                return false;
            }
            $data = $render->pnFormGetValues();

            if (!pnModSetVars('content', $data['config'])) {
                return $render->pnFormSetErrorMsg('Failed to set configuration variables');
            }
            if ($data['config']['categoryUsage'] < 4) {
                // load the category registry util
                Loader::loadClass('CategoryRegistryUtil');
                $mainCategory = CategoryRegistryUtil::getRegisteredModuleCategory('content', 'content_page', $data['config']['categoryPropPrimary']);
                if (!$mainCategory) {
                    return LogUtil::registerError(__('Main category property does not exist.', $dom));
                }
                if ($data['config']['categoryUsage'] < 3) {
                    $secondCategory = CategoryRegistryUtil::getRegisteredModuleCategory('content', 'content_page', $data['config']['categoryPropSecondary']);
                    if (!$secondCategory) {
                        return LogUtil::registerError(__('Second category property does not exist.', $dom));
                    }
                }
            }
            LogUtil::registerStatus(__('Done! Saved module configuration.', $dom));
        } else if ($args['commandName'] == 'cancel') {
            // do nothing
        }
        $url = pnModUrl('content', 'admin', 'main');

        return $render->pnFormRedirect($url);
    }
}

function content_admin_settings()
{
    $render = FormUtil::newpnForm('content');
    return $render->pnFormExecute('content_admin_settings.html', new content_admin_settingsHandler($args));
}

