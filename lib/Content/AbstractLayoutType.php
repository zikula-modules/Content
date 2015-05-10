<?php

abstract class Content_AbstractLayoutType extends Content_AbstractType
{
    /**
     * Is the title displayed in the template?
     * @var boolean
     */
    public $titleInTemplate = false;

    /**
     * the strings describing the contentAreaTitles, filled in child class
     * @var array
     */
    protected $contentAreaTitles = array();

    /**
     * templateType, override in child class
     *    0 = Regular content layouts (e.g. 1/2/3 columns)
     *    1 = Special content layouts (e.g. 21212 columns)
     *    2 = Regular Bootstrap styled layouts
     *    3 = Special Bootstrap styled layouts
     * @var integer
     */
    protected $templateType = 0;

    /**
     * Weight for override of plugin sort
     *
     * @var int
     */
    protected $weight = 0;

    public function getWeight()
    {
        return $this->weight;
    }
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function getNumberOfContentAreas()
    {
        return 0;
    }

    public function getImage()
    {
        return System::getBaseUrl() . '/modules/Content/images/layout_nopreview.png';
    }

    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }

    public function getTemplateType()
    {
        return $this->templateType;
    }

    /**
     * return the default template name as a string
     * @return string
     */
    public function getTemplate()
    {
        return 'layouttype/' . strtolower($this->getName()) . ".tpl";
    }

    /**
     * return the default edit template name as a string
     * @return string
     */
    public function getEditTemplate()
    {
        return 'layouttype/' . strtolower($this->getName()) . "_edit.tpl";
    }

}