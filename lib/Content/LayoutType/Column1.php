<?php
/**
 * Content 1 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_LayoutType_Column1 extends Content_AbstractLayoutType
{
    protected $templateType = 0;

    function __construct(Zikula_View $view)
    {
        parent::__construct($view);
        $this->contentAreaTitles = array(
            $this->__('Header'),
            $this->__('Centre column'));
    }
    function getTitle()
    {
        return $this->__('1 column with header');
    }
    function getDescription()
    {
        return $this->__('Header + single 100% wide column');
    }
    function getNumberOfContentAreas()
    {
        return 2;
    }
	function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layouttype/column1header.png';
    }
}