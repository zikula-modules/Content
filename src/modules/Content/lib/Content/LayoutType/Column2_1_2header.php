<?php
/**
 * Content 2-1-2 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_LayoutType_Column2_1_2header extends Content_LayoutType_Base
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->contentAreaTitles = array(__('Header', $dom), 
                                         __('Left column1', $dom), 
                                         __('Right column1', $dom), 
                                         __('center1', $dom),
                                         __('Left column2', $dom),
                                         __('Right column2', $dom),
                                         __('Footer', $dom)
		);
    }
    function getName()
    {
        return 'column2_1_2header';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('2-1-2 columns (50|50)', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Header + two-one-two columns (50|50) + footer', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 7;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
	function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layout/column2_1_2header.png';
    }
}