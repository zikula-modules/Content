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


function content_ajax_dragcontent($args)
{
  $ok = pnModAPIFunc('content', 'content', 'dragcontent',
                     array('pageId' => FormUtil::getPassedValue('pid', null, 'P'),
                           'contentId' => FormUtil::getPassedValue('cid', null, 'P'),
                           'contentAreaIndex' => FormUtil::getPassedValue('cai', null, 'P'),
                           'position' => FormUtil::getPassedValue('pos', null, 'P')));
  if (!$ok)
    return array('ok' => false, 'message' => LogUtil::getErrorMessagesText());

  return array('ok' => true, 'message' => _OK);
}

