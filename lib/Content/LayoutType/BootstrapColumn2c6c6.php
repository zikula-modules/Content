<?php
/**
 * Content 2 column layout plugin
 *
 * @copyright (C) 2007-2015, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_LayoutType_BootstrapColumn2c6c6 extends Content_AbstractLayoutType
{
    protected $templateType = 2;
    
    function __construct(Zikula_View $view)
    {
        parent::__construct($view);
        $this->contentAreaTitles = array(
            $this->__('Header'),
            $this->__('Left column'),
            $this->__('Right column'),
            $this->__('Footer'));
    }
    function getTitle()
    {
        return $this->__('2 columns (6|6) Bootstrap');
    }
    function getDescription()
    {
        return $this->__('Twitter Bootstrap Layout: Header + two columns (col-md-6|col-md-6) + footer');
    }
    function getNumberOfContentAreas()
    {
        return 4;
    }
    function getImage()
    {
        return System::getBaseUrl().'/modules/Content/images/layouttype/column2header.png';
    }
}