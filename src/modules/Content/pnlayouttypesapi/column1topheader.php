<?php
/**
 * Content 1 column layout with top header plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

class content_layouttypesapi_column1topheaderPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $this->contentAreaTitles = array(__('Header', $dom), __('Header above page headline', $dom), __('Centre column', $dom));
    }

    function content_layouttypesapi_column1topheaderPlugin()
    {
        $this->__construct();
    }

    function getName()
    {
        return 'column1topheader';
    }

    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('1 column, header above page headline', $dom);
    }

    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
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
}

function content_layouttypesapi_column1topheader($args)
{
    return new content_layouttypesapi_column1topheaderPlugin();
}
