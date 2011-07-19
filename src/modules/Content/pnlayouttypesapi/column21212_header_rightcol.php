<?php
/**
 * Content 2-1-2-1-2 + header + extra right column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class content_layouttypesapi_column21212_header_rightcolPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $this->contentAreaTitles = array(__('Header', $dom), 
									     __('Left column1', $dom), 
                                         __('Right column1', $dom), 
                                         __('Center1', $dom),
                                         __('Left column2', $dom),
                                         __('Right column2', $dom),
                                         __('Center2', $dom),
                                         __('Left column3', $dom),
                                         __('Right column3', $dom),
                                         __('Footer', $dom),
                                         __('Right Extra Column Top', $dom),
                                         __('Right Extra Column Bottom', $dom)
                                        );
    }
    function content_layouttypesapi_column21212_header_rightcolPlugin()
    {
        $this->__construct();
    }
    function getName()
    {
        return 'column221212_header_rightcol';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('2-1-2-1-2 columns (50|50) + extra Right column', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('Header + two-one-two-one-two columns (50|50) + footer + extra right column', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 12;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
}

function content_layouttypesapi_column21212_header_rightcol($args)
{
    return new content_layouttypesapi_column21212_header_rightcolPlugin();
}
