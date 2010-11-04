<?php
/**
 * Content 2 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: column2header.php 425 2010-06-28 14:32:52Z philipp $
 * @license See license.txt
 */

class content_layouttypesapi_column2_6633_headerPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->contentAreaTitles = array(__('Header', $dom), __('Left column', $dom), __('Right column', $dom), __('Footer', $dom));
    }
    function getName()
    {
        return 'column2_6633_header';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('2 columns (66|33)', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Header + two columns (66|33) + footer', $dom);
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
    	return System::getBaseUrl().'/modules/Content/images/layout/column2_6633_header.png';
    }
}

function content_layouttypesapi_column2_6633_header($args)
{
    return new content_layouttypesapi_column2_6633_headerPlugin();
}
