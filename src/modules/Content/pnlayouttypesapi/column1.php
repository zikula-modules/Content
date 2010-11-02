<?php
/**
 * Content 1 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_layouttypesapi_column1Plugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->contentAreaTitles = array(__('Header', $dom), __('Centre column', $dom));
    }
    function getName()
    {
        return 'column1';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('1 column', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Header + single 100% wide column', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 2;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
	function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layout/column1header.png';
    }
}

function content_layouttypesapi_column1($args)
{
    return new content_layouttypesapi_column1Plugin();
}
