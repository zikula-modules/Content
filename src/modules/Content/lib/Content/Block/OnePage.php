<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 * @author N.Petkov, based on block menu.php
 */
class Content_Block_OnePage extends Zikula_Controller_AbstractBlock
{

    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Content:OnePageBlock:', 'Block title::');
    }

    public function info()
    {
        return array('module'          => 'Content',
                     'text_type'       => $this->__('Content onepage'),
                     'text_type_long'  => $this->__('Content onepage block'),
                     'allow_multiple'  => true,
                     'form_content'    => false,
                     'form_refresh'    => false,
                     'show_preview'    => true,
                     'admin_tableless' => true);
    }

    public function display($blockinfo)
    {
        // security check
        if (!SecurityUtil::checkPermission('Content:OnePageBlock:', "$blockinfo[title]::", ACCESS_READ)) {
            return;
        }

        // Break out options from our content field
        $vars = BlockUtil::varsFromContent($blockinfo['content']);
        // --- Setting of the Defaults
        if (!isset($vars['usecaching'])) {
            $vars['usecaching'] = true;
        }
        if (!isset($vars['root'])) {
            $vars['root'] = 0;
        }

        if ($vars['usecaching']) {
            $cacheId = 'onepage|' . $blockinfo['title'] . '|' . ZLanguage::getLanguageCode();
        } else {
            $cacheId = null;
        }
        if (!$vars['usecaching'] || ($vars['usecaching'] && !$this->view->is_cached('block/onepage.tpl', $cacheId))) {
            if ($vars['root'] > 0) {
                $blockinfo['content'] = ModUtil::func('content', 'user', 'view', array('pid' => $vars['root']));
            }
        }

        return BlockUtil::themeBlock($blockinfo);
    }

    public function modify($blockinfo)
    {
        $vars = BlockUtil::varsFromContent($blockinfo['content']);
        if (!isset($vars['usecaching'])) {
            $vars['usecaching'] = true;
        }

        $this->view->assign($vars);

        $pages = ModUtil::apiFunc('content', 'page', 'getPages', array(
            'makeTree' => false,
            'orderBy' => 'setLeft',
            'includeContent' => false));
        $pidItems = array();
        $pidItems[] = array('text' => $this->__('Select a page'), 'value' => "0");

        foreach ($pages as $page) {
            $pidItems[] = array('text' => str_repeat('+', $page['level']) . " " . $page['title'], 'value' => $page['id']);
        }
        $this->view->assign('pidItems', $pidItems);

        return $this->view->fetch('block/onepage_modify.tpl');
    }

    function update($blockinfo)
    {
        $vars = BlockUtil::varsFromContent($blockinfo['content']);
        $vars['root'] = FormUtil::getPassedValue('root', 0, 'POST');
        $vars['usecaching'] = (bool)FormUtil::getPassedValue('usecaching', false, 'POST');

        $blockinfo['content'] = BlockUtil::varsToContent($vars);

        // clear the block cache
        $this->view->clear_cache('block/onepage.tpl');

        return $blockinfo;
    }
}