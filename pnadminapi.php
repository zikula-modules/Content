<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


/**
 * get available admin panel links
 *
 * @return array array of admin links
 */
function content_adminapi_getlinks()
{
    $links = array();

    pnModLangLoad('content', 'admin');

    $links[] = array('url' => pnModURL('content', 'admin', 'main'), 'text' => _CONTENT_ADMINMAINLINK);
    $links[] = array('url' => pnModURL('content', 'edit', 'main'), 'text' => _CONTENT_ADMINEDIT);
    $links[] = array('url' => pnModURL('content', 'admin', 'settings'), 'text' => _CONTENT_ADMINSETTINGS);

    return $links;
}


function content_adminapi_getStyleClasses($args)
{
  $classes = array();
  $userClasses = pnModGetVar('content', 'styleClasses');
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
