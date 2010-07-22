<?php
/**
 * Content 2 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_layouttypesapi_column2_2575_headerPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $contentAreaTitles = array(__('Header', $dom), __('Left column', $dom), __('Right column', $dom), __('Footer', $dom));
    }
    function content_layouttypesapi_column2_2575_headerPlugin()
    {
        $this->__construct();
    }
    function getName()
    {
        return 'column2_2575_header';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('2 columns (25|75)', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Header + two columns (25|75) + footer', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 4;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
    function getImage()
    {
    	return pngetBaseUrl().'/modules/Content/pnimages/layout/column2_2575_header.png';
    }
}

function content_layouttypesapi_column2_2575_header($args)
{
    return new content_layouttypesapi_column2_2575_headerPlugin();
}
