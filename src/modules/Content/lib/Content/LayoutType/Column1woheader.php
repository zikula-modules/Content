<?php
/**
 * Content 1 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_LayoutType_Column1woheader extends Content_LayoutType_Base
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->contentAreaTitles = array(__('Centre column', $dom));
    }
    function getName()
    {
        return 'Column1woheader';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('1 column no header', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Single 100% wide column without header', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 1;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
	function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layouttype/column1woheader.png';
    }
}