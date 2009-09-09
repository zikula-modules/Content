<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: pnuser.php,v 1.21 2007/09/04 15:15:22 jornlind Exp $
 * @license See license.txt
 */

Loader::requireOnce('modules/content/common.php');


function content_menublock_init()
{
    // Security
    pnSecAddSchema('Content:menublock:', 'Block title::');
}


function content_menublock_info()
{
    return array('text_type'       => 'Menu',
                 'module'          => 'Content',
                 'text_type_long'  => 'Content menu',
                 'allow_multiple'  => true,
                 'form_content'    => false,
                 'form_refresh'    => false,
                 'show_preview'    => true,
                 'admin_tableless' => true);
}


function content_menublock_display($blockinfo)
{
    // security check
    if (!SecurityUtil::checkPermission( 'Content:menublock:', "$blockinfo[title]::", ACCESS_READ))
        return;

    $cacheId = 'menu|' . pnUserGetLang();

    $render = pnRender::getInstance('content', true);
    if (!$render->is_cached('content_block_menu.html',$cacheId))
    {
      $vars = pnBlockVarsFromContent($blockinfo['content']);
      if (!isset($vars['root']))
          $vars['root'] = 0;

  
   $options = array('orderBy' => 'setLeft',
                       'makeTree' => true,
                       'filter' => array());
      if ($vars['root'] > 0)
          $options['filter']['superParentId'] = $vars['root'];

      $pages = pnModAPIFunc('content', 'page', 'getPages', $options);

      if ($pages === false)
          return false;

      if ($vars['root'] > 0)
          $render->assign(reset($pages));
      else
          $render->assign('subPages', $pages);
    }
    $blockinfo['content'] = $render->fetch('content_block_menu.html', $cacheId);
    return pnBlockThemeBlock($blockinfo);
}


function content_menublock_modify($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    $render = pnRender::getInstance('content', false);
    $render->assign($vars);
    pnModLangLoad('content', 'contenttypes', true);
    $pages = pnModAPIFunc('content', 'page', 'getPages', array('makeTree' => false,
                                                               'orderBy' => 'setLeft',
                                                               'includeContent' => false,
                                                               'enableEscape' => false));
    $pidItems = array();
    $pidItems[] = array('text' => _CONTENT_CONTENTENTTYPE_DIRECTORYSELECTROOT,
                        'value'=> 0);

    foreach ($pages as $page) 
    {
        $pidItems[] = array('text' => str_repeat('+', $page['level']) . " " . $page['title'],
                            'value'=> $page['id']);
   
    }

    $render->assign('pidItems', $pidItems);

    return $render->fetch('content_block_menu_modify.html');
}


function content_menublock_update($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $vars['root'] = FormUtil::getPassedValue('root', 0, 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    // clear the block cache
    //$pnRender = pnRender::getInstance('News');
    //$pnRender->clear_cache('news_block_stories.htm');

    return $blockinfo;
}
