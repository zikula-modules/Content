<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: menu.php 406 2010-06-01 03:04:45Z drak $
 * @license See license.txt
 */

require_once 'modules/Content/common.php';

class Content_Block_Menu extends Zikula_Block
{
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('content:menublock:', 'Block title::');
    }

    public function info()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return array('module'          => 'Content',
                'text_type'       => __('Content menu', $dom),
                'text_type_long'  => __('Content menu block', $dom),
                'allow_multiple'  => true,
                'form_content'    => false,
                'form_refresh'    => false,
                'show_preview'    => true,
                'admin_tableless' => true);
    }

    public function display($blockinfo)
    {
        // security check
        if (!SecurityUtil::checkPermission('Content:menublock:', "$blockinfo[title]::", ACCESS_READ)) {
            return;
        }

        $cacheId = 'menu|' . $blockinfo[title] . '|' . ZLanguage::getLanguageCode();

        $view = Zikula_View::getInstance('Content', true);
        if (!$view->is_cached('content_block_menu.html', $cacheId)) {
            $vars = BlockUtil::varsFromContent($blockinfo['content']);
            if (!isset($vars['root'])) {
                $vars['root'] = 0;
            }

            $options = array('orderBy' => 'setLeft', 'makeTree' => true, 'filter' => array());
            if ($vars['root'] > 0) {
                $options['filter']['superParentId'] = $vars['root'];
            }

            $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', $options);
            if ($pages === false) {
                return false;
            }

            if ($vars['root'] > 0) {
                $view->assign(reset($pages));
            } else {
                $view->assign('subPages', $pages);
            }
        }
        $blockinfo['content'] = $view->fetch('content_block_menu.html', $cacheId);
        return BlockUtil::themeBlock($blockinfo);
    }

    public function modify($blockinfo)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $vars = BlockUtil::varsFromContent($blockinfo['content']);

        $view = Zikula_View::getInstance('Content', false);
        $view->assign($vars);

        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', array('makeTree' => false, 'orderBy' => 'setLeft', 'includeContent' => false, 'enableEscape' => false));
        $pidItems = array();
        $pidItems[] = array('text' => __('All pages', $dom), 'value' => "0");

        foreach ($pages as $page) {
            $pidItems[] = array('text' => str_repeat('+', $page['level']) . " " . $page['title'], 'value' => $page['id']);

        }

        $view->assign('pidItems', $pidItems);

        return $view->fetch('content_block_menu_modify.html');
    }

    public function update($blockinfo)
    {
        $vars = BlockUtil::varsFromContent($blockinfo['content']);
        $vars['root'] = FormUtil::getPassedValue('root', 0, 'POST');
        $blockinfo['content'] = BlockUtil::varsToContent($vars);

        return $blockinfo;
    }
}