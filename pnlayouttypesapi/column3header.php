<?php
/**
 * Content 3 column layout plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


class content_layouttypesapi_column3headerPlugin extends contentLayoutBase
{
  var $contentAreaTitles = array(_CONTENT_LAYOUT_AREAHEADER, 
                                 _CONTENT_LAYOUT_AREALEFT,
                                 _CONTENT_LAYOUT_AREACENTER, 
                                 _CONTENT_LAYOUT_AREARIGHT, 
                                 _CONTENT_LAYOUT_AREAFOOTER);

  function getName() { return 'column3header'; }
  function getTitle() { return _CONTENT_LAYOUT_COLUMN3HEADERTITLE; }
  function getDescription() { return _CONTENT_LAYOUT_COLUMN3HEADERDESCR; }
  function getNumberOfContentAreas() { return 5; }
  function getContentAreaTitle($areaIndex) { return _CONTENT_LAYOUT_AREASINGLE; }
}


function content_layouttypesapi_column3header($args)
{
  return new content_layouttypesapi_column3headerPlugin();
}
