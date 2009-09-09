<?php
/**
 * Content 1 column layout plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


class content_layouttypesapi_column1woheaderPlugin extends contentLayoutBase
{
  var $contentAreaTitles = array(_CONTENT_LAYOUT_AREASINGLE, 
                                 _CONTENT_LAYOUT_AREAFOOTER);

  function getName() { return 'column1woheader'; }
  function getTitle() { return _CONTENT_LAYOUT_COLUMN1WOHEADERTITLE; }
  function getDescription() { return _CONTENT_LAYOUT_COLUMN1WOHEADERDESCR; }
  function getNumberOfContentAreas() { return 1; }
  function getContentAreaTitle($areaIndex) { return $this->contentAreaTitles[$areaIndex]; }
}


function content_layouttypesapi_column1woheader($args)
{
  return new content_layouttypesapi_column1woheaderPlugin();
}
