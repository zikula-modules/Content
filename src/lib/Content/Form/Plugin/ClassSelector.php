<?php

class Content_Form_Plugin_ClassSelector extends pnFormDropdownList
{
    function getFilename()
    {
        return __FILE__;
    }


    function load(&$render, $params)
    {
        if (!$render->pnFormIsPostBack())
        {
            $classes = ModUtil::apiFunc('Content', 'admin', 'getStyleClasses');
            $empty = array(array('text' => '', 'value' => ''));
            $classes = array_merge($empty, $classes);
            $this->setItems($classes);
        }
        parent::load($render, $params);
    }
}
