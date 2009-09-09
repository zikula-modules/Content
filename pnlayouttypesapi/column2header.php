<?php
/**
 * Content 2 column layout plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


class content_layouttypesapi_column2headerPlugin extends contentLayoutBase
{
  var $contentAreaTitles = array(_CONTENT_LAYOUT_AREAHEADER, 
                                 _CONTENT_LAYOUT_AREALEFT,
                                 _CONTENT_LAYOUT_AREARIGHT, 
                                 _CONTENT_LAYOUT_AREAFOOTER);

  function getName() { return 'column2header'; }
  function getTitle() { return _CONTENT_LAYOUT_COLUMN2HEADERTITLE; }
  function getDescription() { return _CONTENT_LAYOUT_COLUMN2HEADERDESCR; }
  function getNumberOfContentAreas() { return 4; }
  function getContentAreaTitle($areaIndex) { return $this->contentAreaTitles[$areaIndex]; }
}


function content_layouttypesapi_column2header($args)
{
  return new content_layouttypesapi_column2headerPlugin();
}
