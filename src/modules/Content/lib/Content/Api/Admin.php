<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: pnadminapi.php 403 2010-05-31 18:15:19Z drak $
 * @license See license.txt
 */

class Content_Api_Admin extends Zikula_Api
{
    /**
     * get available admin panel links
     *
     * @return array array of admin links
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('Content', 'admin', 'main'), 'text' => $this->__('Administration'), 'class' => 'z-icon-es-cubes');
        }
        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            $links[] = array('url' => ModUtil::url('Content', 'edit', 'main'), 'text' => $this->__('Page list'), 'class' => 'z-icon-es-new');
        }
        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('Content', 'admin', 'settings'), 'text' => $this->__('Settings'), 'class' => 'z-icon-es-config');
        }

        return $links;
    }


    public function getStyleClasses($args)
    {
        $classes = array();
        $userClasses = ModUtil::getVar('Content', 'styleClasses');
        $userClasses = explode("\n", $userClasses);

        foreach ($userClasses as $class)
        {
            list($value,$text) = explode('|', $class);
            $value = trim($value);
            $text = trim($text);
            if (!empty($text) && !empty($value))
                $classes[] = array('text' => $text, 'value' => $value);
        }

        return $classes;
    }
}