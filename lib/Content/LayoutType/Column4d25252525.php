<?php
/**
 * Content 3 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_LayoutType_Column4d25252525 extends Content_AbstractLayoutType
{
    protected $templateType = 0;

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
        return $this->__('4 columns (25|25|25|25)');
    }
    function getDescription()
    {
        return $this->__('Header + four columns (25|25|25|25) + footer');
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