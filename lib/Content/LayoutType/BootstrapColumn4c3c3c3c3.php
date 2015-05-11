<?php
/**
 * Content 4 column layout plugin
 *
 * @copyright (C) 2007-2015, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_LayoutType_BootstrapColumn4c3c3c3c3 extends Content_AbstractLayoutType
{
    protected $templateType = 2;

    function __construct(Zikula_View $view)
    {
        parent::__construct($view);
        $this->contentAreaTitles = array(
            $this->__('Header'),
            $this->__('Left column'),
            $this->__('Centre left column'),
            $this->__('Centre right column'),
            $this->__('Right column'),
            $this->__('Footer'));
    }
    function getTitle()
    {
        return $this->__('4 columns (3|3|3|3) Bootstrap');
    }
    function getDescription()
    {
        return $this->__('Twitter Bootstrap Layout: Header + four columns (col-md-3|col-md-3|col-md-3|col-md-3) + footer');
    }
    function getNumberOfContentAreas()
    {
        return 5;
    }
    function getImage()
    {
        return System::getBaseUrl().'/modules/Content/images/layouttype/column4_25252525_header.png';
    }
}