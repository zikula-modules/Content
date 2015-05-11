<?php
/**
 * Content 3 column layout plugin
 *
 * @copyright (C) 2007-2015, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_LayoutType_BootstrapColumn3c4c4c4 extends Content_AbstractLayoutType
{
    protected $templateType = 2;

    function __construct(Zikula_View $view)
    {
        parent::__construct($view);
        $this->contentAreaTitles = array(
            $this->__('Header'),
            $this->__('Left column'),
            $this->__('Centre column'),
            $this->__('Right column'),
            $this->__('Footer'));
    }
    function getTitle()
    {
        return $this->__('3 columns (4|4|4) Bootstrap');
    }
    function getDescription()
    {
        return $this->__('Twitter Bootstrap Layout: Header + three columns (col-md-4|col-md-4|col-md-4) + footer');
    }
    function getNumberOfContentAreas()
    {
        return 5;
    }
    function getImage()
    {
        return System::getBaseUrl().'/modules/Content/images/layouttype/column3header.png';
    }
}