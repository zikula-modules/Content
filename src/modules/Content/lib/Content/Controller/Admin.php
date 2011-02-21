<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */


class Content_Controller_Admin extends Zikula_Controller
{
    public function main()
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        return $this->view->fetch('content_admin_main.tpl');
    }

    public function settings()
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('content_admin_settings.tpl', new Content_Form_Handler_Admin_Settings(array()));
    }
}
