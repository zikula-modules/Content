<?php

class Content_LayoutType_Base
{
    function getName()
    {
        return 'unknown';
    }
    function getTitle()
    {
        return 'Unknown layout';
    }
    function getDescription()
    {
        return 'This is the base class for layouts - do not use!';
    }
    function getNumberOfContentAreas()
    {
        return 0;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $areaIndex;
    }
    function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layout_nopreview.png';
    }
}