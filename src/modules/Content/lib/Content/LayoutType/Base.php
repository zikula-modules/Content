<?php

class Content_LayoutType_Base
{
    public function getName()
    {
        return 'unknown';
    }
    public function getTitle()
    {
        return 'Unknown layout';
    }
    public function getDescription()
    {
        return 'This is the base class for layouts - do not use!';
    }
    public function getNumberOfContentAreas()
    {
        return 0;
    }
    public function getContentAreaTitle($areaIndex)
    {
        return $areaIndex;
    }
    public function getImage()
    {
    	return System::getBaseUrl() . '/modules/Content/images/layout_nopreview.png';
    }
    public function getTemplate()
    {
        return 'layouttype/' . strtolower($this->getName()) . ".tpl";
    }
    public function getEditTemplate()
    {
        return 'layouttype/' . strtolower($this->getName()) . "_edit.tpl";
    }
}