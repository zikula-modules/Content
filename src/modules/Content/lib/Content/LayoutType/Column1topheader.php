<?php
/**
 * Content 1 column layout with top header plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_LayoutType_Column1topheader extends Content_LayoutType_Base
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->contentAreaTitles = array(__('Header', $dom), __('Header above page headline', $dom), __('Centre column', $dom));
    }
    function getName()
    {
        return 'Column1topheader';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('1 column, header above page headline', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Single 100% wide column and a top-header above the page headline (for e.g. breadcrumbs or author-information above the title)', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 3;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
	function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layouttype/column1topheader.png';
    }
}