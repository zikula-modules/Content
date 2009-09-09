<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */

function smarty_function_contenteditthis($params, &$render) 
{
  $data = $params['data'];
  $type = $params['type'];
  $access = $params['access'];  

  if (!$access['pageEditAllowed'])
    return '';

  $editmode = SessionUtil::getVar('ContentEditMode');

  $vars = $render->get_template_vars();
  if ($vars['preview'])
    return '';

  $html = '';

  if ($type == 'page')
  {
    // Unused ...
    $html = '<div class="content-editthis">';    
    $url = DataUtil::formatForDisplay(pnModURL('content', 'edit', 'editpage', 
                                               array('pid' => $data['id'], 'back' => 1)));
    $translateurl = DataUtil::formatForDisplay(pnModURL('content', 'edit', 'translatepage', 
                                                        array('pid' => $data['id'], 'back' => 1)));
    $html .= "<a href=\"$url\">" .  _CONTENT_EDITTHISPAGE . "</a>";
    if ($vars['multilingual'] == 1) {
        $html .= "| <a href=\"$translateurl\">". _CONTENT_TRANSLATETHISPAGE ."</a>";
    }
    $html .= '</div>';                                      
  } 
  elseif ($type == 'content' && $editmode) 
  {
    $html = '<div class="content-editthis">';
    $url = DataUtil::formatForDisplay(pnModURL('content', 'edit', 'editcontent', 
                                               array('cid' => $data['id'], 'back' => 1)));
    $translateurl = DataUtil::formatForDisplay(pnModURL('content', 'edit', 'translatecontent', 
                                               array('cid' => $data['id'], 'back' => 1)));                       
    $html .= "[<a href=\"$url\">" .  _CONTENT_EDITTHIS . "</a>] ";
    if ($vars['multilingual'] == 1) {
        $html .= "[<a href=\"$translateurl\">". _CONTENT_TRANSLATETHIS ."</a>]";
    }                    
    $html .= '</div>';                
  }

  if (isset($params['assign']))
    $smarty->assign($params['assign'], $html);
  else
    return $html;
}