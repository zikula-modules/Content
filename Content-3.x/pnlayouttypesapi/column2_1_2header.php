<?php
/**
 * Content 2-1-2 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: column2header.php 371 2010-01-05 16:15:52Z herr.vorragend $
 * @license See license.txt
 */

class content_layouttypesapi_column2_1_2headerPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('content');
        $contentAreaTitles = array(	__('Header', $dom), 
									__('Left column1', $dom), 
									__('Right column1', $dom), 
									__('center1', $dom),
									__('Left column2', $dom),
									__('Right column2', $dom),
									__('Footer', $dom)
									);
    }
    function content_layouttypesapi_column2_1_2headerPlugin()
    {
        $this->__construct();
    }
    function getName()
    {
        return 'column2_1_2header';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('content');
        return __('2-1-2 columns (50|50)', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('content');
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
    	return pngetBaseUrl().'/modules/Content/pnimages/layout/column2_1_2header.png';
    }
}

function content_layouttypesapi_column2_1_2header($args)
{
    return new content_layouttypesapi_column2_1_2headerPlugin();
}
