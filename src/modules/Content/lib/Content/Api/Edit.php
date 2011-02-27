<?php

/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */
class Content_Api_Edit extends Zikula_Api
{

    /**
     * get available admin panel links
     *
     * @return array array of admin links
     */
    public function getlinks()
    {
        $links = array();

        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            $links[] = array(
                'url' => ModUtil::url('Content', 'edit', 'main'),
                'text' => $this->__('Page list'),
                'class' => 'z-icon-es-view',
                'links' => array(
                    array('url' => ModUtil::url('Content', 'user', 'sitemap'),
                        'text' => $this->__('Sitemap')),
                    array('url' => ModUtil::url('Content', 'user', 'extlist'),
                        'text' => $this->__('Extended')),
                    array('url' => ModUtil::url('Content', 'user', 'pagelist'),
                        'text' => $this->__('Complete')),
                    array('url' => ModUtil::url('Content', 'user', 'categories'),
                        'text' => $this->__('Category list')),
                    array('url' => ModUtil::url('Content', 'edit', 'deletedpages'),
                        'text' => $this->__('Restore pages')),
                    ));
        }
        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_ADD)) {
            $links[] = array(
                'url' => ModUtil::url('Content', 'edit', 'newPage'),
                'text' => $this->__('Add new page'),
                'class' => 'z-icon-es-new');
        }
        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN)) {
            $links[] = array(
                'url' => ModUtil::url('Content', 'admin', 'settings'),
                'text' => $this->__('Settings'),
                'class' => 'z-icon-es-config');
        }

        return $links;
    }

}