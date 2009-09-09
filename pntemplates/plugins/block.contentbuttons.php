<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */

function smarty_block_contentbuttons($params, $content, &$render) 
{
  if ($content)
  {
    echo "<div class=\"buttons\">\n";
    echo $content;
    echo "</div>\n";
  }
}

?>