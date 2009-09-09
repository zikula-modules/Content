<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

Loader::requireOnce('modules/content/common.php');
Loader::requireOnce('includes/pnForm.php');


function content_admin_main()
{
  if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN))
    return LogUtil::registerPermissionError();

  $render = pnRender::getInstance('content');

  return $render->fetch('content_admin_main.html');
}


class content_admin_settingsHandler extends pnFormHandler
{
  function initialize(&$render)
  {
    if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_ADMIN))
      return $render->pnFormRegisterError(LogUtil::registerPermissionError());

    //PageUtil::setVar('title', $page['title']);

    // Assign all module vars
    $render->assign('config', pnModGetVar('Content'));

    return true;
  }


  function handleCommand(&$render, &$args)
  {
    if ($args['commandName'] == 'save')
    {
      if (!$render->pnFormIsValid())
        return false;

      $data = $render->pnFormGetValues();

      if (!pnModSetVars('content', $data['config']))
        return $render->pnFormSetErrorMsg('Failed to set configuration variables');

      LogUtil::registerStatus(_CONTENT_ADMINUPDATED);
    }
    else if ($args['commandName'] == 'cancel')
    {
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

